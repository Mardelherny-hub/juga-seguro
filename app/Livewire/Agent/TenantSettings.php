<?php

namespace App\Livewire\Agent;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class TenantSettings extends Component
{
    use WithFileUploads;

    /** ========= Ajustes generales ========= */
    public string $whatsapp_number = '';
    public string $casino_url = '';

    /** ========= Marca blanca ========= */
    public ?string $brand_primary = null;
    public ?string $brand_secondary = null;
    public ?string $current_logo_url = null;
    public $logo; // TemporaryUploadedFile

    /** ========= Usuarios del tenant (CRUD inline) ========= */
    public array $roles = [
        'admin'    => 'Administrador',
        'operator' => 'Operador',
    ];

    // listado / búsqueda simple
    public string $search = '';
    public $users; // colección refrescada en cada acción

    // formulario crear/editar
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'operator';
    public ?string $password = null;
    public ?string $password_confirmation = null;

    // (si querés mantener la lógica de invitación por reset link)
    public bool $useInviteFlow = false; // al crear: si true, manda Password::sendResetLink

    /** ========= Mount ========= */
    public function mount()
    {
        $tenant = auth()->user()->tenant;

        // generales
        $this->whatsapp_number = $tenant->whatsapp_number ?? '';
        $this->casino_url      = $tenant->casino_url ?? '';

        // branding
        $this->brand_primary    = $tenant->brand_primary ?? '#4f46e5';
        $this->brand_secondary  = $tenant->brand_secondary ?? '#06b6d4';
        $this->current_logo_url = $tenant->logo_url;

        // usuarios
        $this->refreshUsers();
    }

    /** ========= Validaciones ========= */
    public function rules(): array
    {
        return [
            'whatsapp_number' => ['required','string','regex:/^\+\d{7,15}$/'],
            'casino_url'      => ['required','url'],
        ];
    }

    protected function brandRules(): array
    {
        return [
            'brand_primary'   => ['required','regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'brand_secondary' => ['required','regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            // permitir PNG y SVG (nota: 'image' no admite svg)
            'logo'            => ['nullable','mimetypes:image/png,image/svg+xml','max:1024'],
        ];
    }

    protected function userRules(): array
    {
        $tenantId = Auth::user()->tenant_id;

        $uniqueEmail = Rule::unique('users','email')->where(fn($q) => $q->where('tenant_id', $tenantId));
        if ($this->editingId) {
            $uniqueEmail = $uniqueEmail->ignore($this->editingId);
        }

        $rules = [
            'name'  => ['required','string','max:255'],
            'email' => ['required','email',$uniqueEmail],
            'role'  => ['required', Rule::in(['admin', 'operator'])],
        ];

        $passRules = ['string','min:8','confirmed'];
        // crear => pass requerido; editar => opcional
        $rules['password'] = $this->editingId
            ? array_merge(['nullable'], $passRules)
            : array_merge(['required'], $passRules);

        return $rules;
    }

    /** ========= Guardar: Generales ========= */
    public function save()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;

        DB::transaction(function () use ($tenant) {
            $tenant->whatsapp_number = $this->whatsapp_number;
            $tenant->casino_url      = $this->casino_url;
            $tenant->save();

            if (function_exists('activity')) {
                activity()->performedOn($tenant)->causedBy(auth()->user())
                    ->withProperties([
                        'whatsapp_number' => $this->whatsapp_number,
                        'casino_url'      => $this->casino_url,
                    ])->log('Configuración general del tenant actualizada');
            }
        });

        session()->flash('success','Configuración actualizada correctamente');
    }

    /** ========= Guardar: Marca blanca ========= */
    public function saveBrand()
    {
        $this->validate($this->brandRules());
        $tenant = auth()->user()->tenant;

        DB::transaction(function () use ($tenant) {
            if (Schema::hasColumn('tenants','brand_primary')) {
                $tenant->brand_primary = $this->brand_primary;
            }
            if (Schema::hasColumn('tenants','brand_secondary')) {
                $tenant->brand_secondary = $this->brand_secondary;
            }

            if ($this->logo) {
                $ext = strtolower($this->logo->getClientOriginalExtension());
                $ext = $ext === 'svg' ? 'svg' : 'png';
                $stored = $this->logo->storeAs("tenants/{$tenant->id}/brand", "logo.{$ext}", 'public');
                $publicUrl = Storage::disk('public')->url($stored);

                if (Schema::hasColumn('tenants','logo_url')) {
                    $tenant->logo_url = $publicUrl;
                }
                $this->current_logo_url = $publicUrl;
            }

            $tenant->save();

            if (function_exists('activity')) {
                activity()->performedOn($tenant)->causedBy(auth()->user())
                    ->withProperties([
                        'brand_primary'   => $this->brand_primary,
                        'brand_secondary' => $this->brand_secondary,
                        'logo_url'        => $this->current_logo_url,
                    ])->log('Marca blanca del tenant actualizada');
            }
        });

        session()->flash('success','Marca blanca guardada correctamente');
    }

    public function removeLogo()
    {
        $tenant = auth()->user()->tenant;

        DB::transaction(function () use ($tenant) {
            if ($tenant->logo_url && str_contains($tenant->logo_url, '/storage/')) {
                $rel = str($tenant->logo_url)->after('/storage/')->toString();
                if (Storage::disk('public')->exists($rel)) {
                    Storage::disk('public')->delete($rel);
                }
            }
            if (Schema::hasColumn('tenants','logo_url')) {
                $tenant->logo_url = null;
            }
            $tenant->save();

            if (function_exists('activity')) {
                activity()->performedOn($tenant)->causedBy(auth()->user())->log('Logo del tenant eliminado');
            }
        });

        $this->current_logo_url = null;
        session()->flash('success','Logo eliminado');
    }

    /** ========= CRUD Usuarios (inline) ========= */
    public function createUser(): void
    {
        $this->resetUserForm();
        $this->editingId = null;
    }

    public function editUser(int $id): void
    {
        $u = $this->tenantUsersQuery()->findOrFail($id);
        $this->editingId = $u->id;
        $this->name  = $u->name;
        $this->email = $u->email;
        $this->role  = $u->role;
        $this->password = null;
        $this->password_confirmation = null;
    }

    public function storeUser(): void
    {
        $this->validate($this->userRules());

        $tenantId = Auth::user()->tenant_id;

        $user = new User();
        $user->tenant_id = $tenantId;
        $user->name      = $this->name;
        $user->email     = strtolower($this->email);
        $user->role      = $this->role;

        if ($this->useInviteFlow) {
            // password random + reset link por email
            $user->password = Hash::make(str()->random(32));
            $user->save();
            Password::sendResetLink(['email' => $user->email]);
        } else {
            $user->password = Hash::make($this->password);
            $user->save();
        }

        if (function_exists('activity')) {
            activity()->performedOn($user->tenant)->causedBy(auth()->user())
                ->withProperties(['email' => $user->email, 'role' => $user->role])
                ->log('Usuario del tenant creado');
        }

        session()->flash('success','Usuario creado correctamente');
        $this->resetUserForm();
        $this->refreshUsers();
    }

    public function updateUser(): void
    {
        if (! $this->editingId) return;

        $this->validate($this->userRules());

        $u = $this->tenantUsersQuery()->findOrFail($this->editingId);
        $u->name  = $this->name;
        $u->email = strtolower($this->email);
        $u->role  = $this->role;

        if ($this->password) {
            $u->password = Hash::make($this->password);
        }

        $u->save();

        if (function_exists('activity')) {
            activity()->performedOn($u->tenant)->causedBy(auth()->user())
                ->withProperties(['email' => $u->email, 'role' => $u->role])
                ->log('Usuario del tenant actualizado');
        }

        session()->flash('success','Usuario actualizado');
        $this->resetUserForm();
        $this->refreshUsers();
    }

    public function deleteUser(int $id): void
    {
        $auth = Auth::user();

        if ($id === $auth->id) {
            session()->flash('error','No podés eliminar tu propio usuario.');
            return;
        }

        $u = $this->tenantUsersQuery()->findOrFail($id);

        // Evitar dejar el tenant sin admins
        if ($u->role === 'admin') {
            $admins = $this->tenantUsersQuery()->where('role', 'admin')->count();
            if ($admins <= 1) {
                session()->flash('error','Debe quedar al menos un Administrador en el tenant.');
                return;
            }
        }

        $u->delete();

        if (function_exists('activity')) {
            activity()->performedOn($u->tenant)->causedBy(auth()->user())
                ->withProperties(['email' => $u->email])
                ->log('Usuario del tenant eliminado');
        }

        session()->flash('success','Usuario eliminado');
        $this->resetUserForm();
        $this->refreshUsers();
    }

    /** ========= Helpers ========= */
    protected function tenantUsersQuery()
    {
        return User::query()->where('tenant_id', Auth::user()->tenant_id);
    }

    protected function refreshUsers(): void
    {
        $this->users = $this->tenantUsersQuery()
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name','like','%'.$this->search.'%')
                       ->orWhere('email','like','%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function updatedSearch(): void
    {
        $this->refreshUsers();
    }

    protected function resetUserForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->role = 'operator';
        $this->password = null;
        $this->password_confirmation = null;
    }

    /** ========= Render ========= */
    public function render()
    {
        return view('livewire.agent.tenant-settings');
    }
}