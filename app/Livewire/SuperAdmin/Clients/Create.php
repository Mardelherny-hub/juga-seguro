<?php

namespace App\Livewire\SuperAdmin\Clients;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Create extends Component
{
    use WithFileUploads;

    public $name = '';
    public $domain = '';
    public $custom_domain = '';
    public $database = 'gestion_redes';
    public $primary_color = '#3B82F6';
    public $secondary_color = '#10B981';
    public $logo = null;
    public $is_active = true;

    // Campos de suscripción
    public $subscription_type = 'prepaid';
    public $monthly_fee = null;
    public $chips_balance = 0;
    public $chip_price = 100.00;

    public $showDnsInstructions = false;
    public $dnsInstructions = '';

    public $admin_name = '';
    public $admin_email = '';
    public $admin_password = '';



    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'custom_domain' => 'nullable|string|max:255|unique:tenants,custom_domain|regex:/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}$/i',
            'database' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
            'subscription_type' => 'required|in:monthly,prepaid',
            'monthly_fee' => 'required_if:subscription_type,monthly|nullable|numeric|min:0',
            'chip_price' => 'required_if:subscription_type,prepaid|nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre del cliente es obligatorio.',
        'domain.required' => 'El dominio es obligatorio.',
        'domain.unique' => 'Este dominio ya está en uso.',
        'domain.alpha_dash' => 'El subdominio solo puede contener letras, números y guiones.',
        'custom_domain.unique' => 'Este dominio personalizado ya está en uso.',
        'custom_domain.regex' => 'El dominio no tiene un formato válido (ej: www.ejemplo.com).',
        'database.required' => 'El nombre de la base de datos es obligatorio.',
        'logo.image' => 'El archivo debe ser una imagen.',
        'logo.max' => 'La imagen no puede pesar más de 2MB.',
        'admin_name.required' => 'El nombre del administrador es obligatorio.',
        'admin_email.required' => 'El email del administrador es obligatorio.',
        'admin_email.email' => 'El email debe tener un formato válido.',
        'admin_email.unique' => 'Este email ya está registrado.',
        'admin_password.required' => 'La contraseña es obligatoria.',
        'admin_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
    ];

    public function updatedName($value)
    {
        // Auto-generar domain basado en el nombre si está vacío
        if (empty($this->domain)) {
            $this->domain = Str::slug($value);
        }
    }

    public function save()
    {
        
        $this->validate();

        // Procesar el logo si existe
        $logoUrl = null;
        if ($this->logo) {
            $logoUrl = $this->logo->store('logos', 'public');
            $logoUrl = asset('storage/' . $logoUrl);
        }

        // Normalizar dominios a minúsculas
        $domain = strtolower($this->domain);
        $customDomain = $this->custom_domain ? strtolower($this->custom_domain) : null;

        // Crear el tenant
        $tenant = Tenant::create([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
            'domain' => $domain,
            'custom_domain' => $customDomain,
            'database' => $this->database,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'logo_url' => $logoUrl,
            'is_active' => $this->is_active,
            'subscription_type' => $this->subscription_type,
            'monthly_fee' => $this->subscription_type === 'monthly' ? $this->monthly_fee : null,
            'chips_balance' => $this->subscription_type === 'prepaid' ? $this->chips_balance : 0,
            'chip_price' => $this->subscription_type === 'prepaid' ? $this->chip_price : 100,
        ]);

        // ← CREAR USUARIO ADMINISTRADOR DEL CLIENTE
        $adminUser = \App\Models\User::create([
            'tenant_id' => $tenant->id,
            'name' => $this->admin_name,
            'email' => $this->admin_email,
            'password' => bcrypt($this->admin_password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Registrar en activity log
        activity()
            ->performedOn($tenant)
            ->causedBy(auth()->user())
            ->withProperties([
                'admin_created' => $adminUser->email
            ])
            ->log('Cliente y administrador creados');

        // Si tiene dominio personalizado, mostrar instrucciones DNS
        if ($customDomain) {
            $this->showDnsInstructions = true;
            $this->dnsInstructions = $this->generateDnsInstructions($tenant);
            session()->flash('dns_instructions', $this->dnsInstructions);
        }

        session()->flash('message', "Cliente {$tenant->name} y administrador creados exitosamente.");

        return $this->redirect(route('super-admin.clients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.super-admin.clients.create')
            ->layout('components.layouts.super-admin');
    }

    private function generateDnsInstructions(Tenant $tenant): string
    {
        $serverIp = config('app.server_ip', 'XXX.XXX.XXX.XXX');
        $domain = $tenant->custom_domain;
        
        // Extraer dominio raíz sin www
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