<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ManageAdmins extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $editingId = null;

    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $is_active = true;

    protected function rules()
    {
        $emailRule = 'required|email|unique:users,email';
        if ($this->editingId) {
            $emailRule .= ',' . $this->editingId;
        }

        $rules = [
            'name' => 'required|string|min:3|max:255',
            'email' => $emailRule,
            'is_active' => 'boolean',
        ];

        // Password obligatorio solo al crear
        if (!$this->editingId) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else if ($this->password) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio',
        'name.min' => 'El nombre debe tener al menos 3 caracteres',
        'email.required' => 'El email es obligatorio',
        'email.email' => 'El email debe ser válido',
        'email.unique' => 'Este email ya está registrado',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        'password.confirmed' => 'Las contraseñas no coinciden',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $user = User::where('is_super_admin', true)->findOrFail($id);
        
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_active = $user->is_active;
        $this->password = '';
        $this->password_confirmation = '';
        
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            // Editar
            $user = User::where('is_super_admin', true)->findOrFail($this->editingId);
            
            $user->name = $this->name;
            $user->email = strtolower($this->email);
            $user->is_active = $this->is_active;
            
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }
            
            $user->save();

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('Super Admin actualizado');

            session()->flash('success', 'Super Admin actualizado correctamente');
        } else {
            // Crear
            $user = User::create([
                'tenant_id' => null,
                'name' => $this->name,
                'email' => strtolower($this->email),
                'password' => Hash::make($this->password),
                'role' => 'super_admin',
                'is_super_admin' => true,
                'is_active' => $this->is_active,
            ]);

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('Super Admin creado');

            session()->flash('success', 'Super Admin creado correctamente');
        }

        $this->closeModal();
    }

    public function toggleActive($id)
    {
        $user = User::where('is_super_admin', true)->findOrFail($id);

        // Evitar desactivarse a sí mismo
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes desactivar tu propio usuario');
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Super Admin ' . ($user->is_active ? 'activado' : 'desactivado'));

        session()->flash('success', 'Estado actualizado correctamente');
    }

    public function deleteAdmin($id)
    {
        $user = User::where('is_super_admin', true)->findOrFail($id);

        // Evitar eliminarse a sí mismo
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propio usuario');
            return;
        }

        // Verificar que quede al menos un super admin activo
        $activeAdmins = User::where('is_super_admin', true)
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->count();

        if ($activeAdmins < 1) {
            session()->flash('error', 'Debe quedar al menos un Super Admin activo');
            return;
        }

        $userName = $user->name;
        $user->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Super Admin eliminado: ' . $userName);

        session()->flash('success', 'Super Admin eliminado correctamente');
    }

    protected function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_active = true;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $admins = User::where('is_super_admin', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.super-admin.manage-admins', [
            'admins' => $admins
        ])->layout('components.layouts.super-admin');
    }
}