<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jugadores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('players.players-list')
        </div>
    </div>

    {{-- Modales (se cargan dinámicamente cuando se disparan los eventos) --}}
    @livewire('players.player-detail')
    @livewire('players.edit-player')
    @livewire('players.player-actions')
</x-app-layout>