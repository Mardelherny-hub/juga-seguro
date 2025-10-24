<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Historial de Transacciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('agent.transactions.transaction-history')
        </div>
    </div>

    {{-- Modales --}}
    @livewire('agent.transactions.transaction-detail')
    @livewire('agent.transactions.transaction-approval')
    @livewire('agent.transactions.transaction-rejection')
</x-layouts.app>