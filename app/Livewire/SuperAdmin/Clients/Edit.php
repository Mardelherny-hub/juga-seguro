<?php

namespace App\Livewire\SuperAdmin\Clients;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Tenant $tenant;
    public $name;
    public $domain;
    public $custom_domain;
    public $database;
    public $primary_color;
    public $secondary_color;
    public $logo;
    public $current_logo_url;
    public $is_active;
    // URL Casino
    public $casino_url;

    // Campos de suscripción
    public $subscription_type;
    public $monthly_fee;
    public $chips_balance;
    public $chip_price;

    public $showDnsInstructions = false;
    public $dnsInstructions = '';

    public $admin_name = '';
    public $admin_email = '';
    public $admin_password = '';

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->name = $tenant->name;
        $this->domain = $tenant->domain;
        $this->custom_domain = $tenant->custom_domain;
        $this->database = $tenant->database;
        $this->primary_color = $tenant->primary_color;
        $this->secondary_color = $tenant->secondary_color;
        $this->current_logo_url = $tenant->logo_url;
        $this->is_active = $tenant->is_active;
        $this->casino_url = $tenant->casino_url;
        $this->subscription_type = $tenant->subscription_type;
        $this->monthly_fee = $tenant->monthly_fee;
        $this->chips_balance = $tenant->chips_balance;
        $this->chip_price = $tenant->chip_price;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $this->tenant->id,
            'custom_domain' => 'nullable|string|max:255|unique:tenants,custom_domain,' . $this->tenant->id . '|regex:/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}$/i',
            'database' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'casino_url' => 'nullable|url|max:255',
            'subscription_type' => 'required|in:monthly,prepaid',
            'monthly_fee' => 'required_if:subscription_type,monthly|nullable|numeric|min:0',
            'chip_price' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre del cliente es obligatorio.',
        'domain.required' => 'El dominio es obligatorio.',
        'domain.unique' => 'Este dominio ya está en uso.',
        'custom_domain.unique' => 'Este dominio personalizado ya está en uso.',
        'custom_domain.regex' => 'El dominio no tiene un formato válido.',
        'database.required' => 'El nombre de la base de datos es obligatorio.',
        'logo.image' => 'El archivo debe ser una imagen.',
        'logo.max' => 'La imagen no puede pesar más de 2MB.',
    ];

    public function save()
    {
        $this->validate();

        // Procesar el logo si existe uno nuevo
        $logoUrl = $this->current_logo_url;
        if ($this->logo) {
            $logoUrl = $this->logo->store('logos', 'public');
            $logoUrl = asset('storage/' . $logoUrl);
        }
        
        // Detectar si cambió el dominio personalizado
        $domainChanged = $this->custom_domain && $this->custom_domain !== $this->tenant->custom_domain;

        // Actualizar el tenant
        $this->tenant->update([
            'name' => $this->name,
            'domain' => $this->domain,
            'custom_domain' => $this->custom_domain ? strtolower($this->custom_domain) : null,
            'database' => $this->database,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'logo_url' => $logoUrl,
            'is_active' => $this->is_active,
            'casino_url' => $this->casino_url,
            'subscription_type' => $this->subscription_type,
            'monthly_fee' => $this->subscription_type === 'monthly' ? $this->monthly_fee : null,
            'chip_price' => $this->chip_price,
        ]);
        
        // Si cambió el dominio, mostrar instrucciones
        if ($domainChanged) {
            $this->showDnsInstructions = true;
            $this->dnsInstructions = $this->generateDnsInstructions($this->tenant);
            session()->flash('dns_instructions', $this->dnsInstructions);
        }

        session()->flash('message', "Cliente {$this->tenant->name} actualizado exitosamente.");

        return $this->redirect(route('super-admin.clients.index'), navigate: true);
    }

    public function removeLogo()
    {
        $this->current_logo_url = null;
    }

    public function render()
    {
        return view('livewire.super-admin.clients.edit')
            ->layout('components.layouts.super-admin');
    }

    public function changeAdminPassword()
    {
        $this->validate([
            'admin_password' => 'required|min:8',
        ], [
            'admin_password.required' => 'La contraseña es obligatoria',
            'admin_password.min' => 'La contraseña debe tener al menos 8 caracteres',
        ]);

        $admin = $this->tenant->users()->where('role', 'admin')->first();

        if (!$admin) {
            session()->flash('error', 'No se encontró el administrador');
            return;
        }

        $admin->update([
            'password' => \Illuminate\Support\Facades\Hash::make($this->admin_password),
        ]);

        activity()
            ->performedOn($admin)
            ->causedBy(auth()->user())
            ->log('Contraseña de administrador cambiada por Super Admin');

        $this->admin_password = '';
        session()->flash('message', 'Contraseña del administrador actualizada correctamente.');
    }

    private function generateDnsInstructions(Tenant $tenant): string
    {
        $serverIp = config('app.server_ip', 'XXX.XXX.XXX.XXX');
        $domain = $tenant->custom_domain;
        
        $rootDomain = preg_replace('/^www\./', '', $domain);
        
        return "
    ===========================================
    INSTRUCCIONES DNS PARA: {$domain}
    ===========================================

    El cliente debe configurar estos registros DNS:

    REGISTRO A:
    -----------
    Tipo:  A
    Host:  @
    Valor: {$serverIp}
    TTL:   3600

    REGISTRO A (WWW):
    -----------
    Tipo:  A
    Host:  www
    Valor: {$serverIp}
    TTL:   3600

    NOTAS:
    - La propagación DNS puede tardar hasta 48 horas
    - Subdominio disponible: {$tenant->domain}." . config('app.domain') . "
    ===========================================
        ";
    }
}