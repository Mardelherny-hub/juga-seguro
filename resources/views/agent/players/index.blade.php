<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jugadores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('agent.players.players-list')
        </div>
    </div>

    {{-- Modales (se cargan dinámicamente cuando se disparan los eventos) --}}
    @livewire('agent.players.player-detail')
    @livewire('agent.players.edit-player')
    @livewire('agent.players.player-actions')
    @livewire('agent.players.create-player')
</x-layouts.app>