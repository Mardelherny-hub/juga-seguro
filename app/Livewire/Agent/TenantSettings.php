<?php

namespace App\Livewire\Agent;

use Livewire\Component;

class TenantSettings extends Component
{
    public $whatsapp_number = '';
    public $casino_url = '';
    
    public function mount()
    {
        $tenant = auth()->user()->tenant;
        $this->whatsapp_number = $tenant->whatsapp_number ?? '';
        $this->casino_url = $tenant->casino_url ?? '';
    }

    protected function rules()
    {
        return [
            'whatsapp_number' => 'nullable|string|min:10|max:20',
            'casino_url' => 'nullable|url',
        ];
    }

    protected $messages = [
        'whatsapp_number.min' => 'El número debe tener al menos 10 dígitos',
        'casino_url.url' => 'Ingresa una URL válida (ej: https://casino.com)',
    ];

    public function save()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;
        $tenant->update([
            'whatsapp_number' => $this->whatsapp_number,
            'casino_url' => $this->casino_url,
        ]);

        // Activity log
        activity()
            ->performedOn($tenant)
            ->causedBy(auth()->user())
            ->log('Configuración de WhatsApp y Casino actualizada');

        session()->flash('success', 'Configuración actualizada correctamente');
    }

    public function render()
    {
        return view('livewire.agent.tenant-settings');
    }
}