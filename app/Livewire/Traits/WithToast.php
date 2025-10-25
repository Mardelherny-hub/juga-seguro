<?php

namespace App\Livewire\Traits;

trait WithToast
{
    protected function showToast(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', 
            type: $type,
            message: $message
        );
    }
}