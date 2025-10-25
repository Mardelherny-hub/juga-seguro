<div wire:poll.5s="updateBalance">
    <p class="text-xs text-gray-300">Saldo actual</p>
    <p class="text-2xl font-bold text-white">${{ number_format($balance, 2) }}</p>
</div>