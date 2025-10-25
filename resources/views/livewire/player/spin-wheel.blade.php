<div>
    <div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">🎰 Ruleta de Premios</h1>
        <p class="text-gray-600 dark:text-gray-400">¡Gira la ruleta una vez al día y gana increíbles premios!</p>
    </div>

    <!-- Mensajes flash -->
    @if(session()->has('error'))
    <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg text-center">
        {{ session('error') }}
    </div>
    @endif

    <!-- Ruleta -->
    <div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl shadow-2xl p-8 mb-8">
        <div class="max-w-md mx-auto">
            <!-- Canvas de la ruleta -->
            <div class="relative">
                <div class="wheel-container" wire:ignore>
                    <div id="wheel" class="wheel {{ $isSpinning ? 'spinning' : '' }}">
                        @foreach($prizes as $index => $prize)
                        <div class="wheel-segment segment-{{ $index }}" 
                             style="transform: rotate({{ $index * (360 / count($prizes)) }}deg);">
                            <div class="segment-content">
                                <span class="prize-label">{{ $prize['label'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Indicador/Flecha -->
                    <div class="wheel-pointer"></div>
                </div>
            </div>

            <!-- Botón de giro -->
            <div class="text-center mt-8">
                @if(!$hasSpunToday)
                <button 
                    wire:click="spin"
                    wire:loading.attr="disabled"
                    :disabled="$wire.isSpinning"
                    class="px-8 py-4 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold text-xl rounded-full shadow-lg transform transition hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>🎲 GIRAR RULETA</span>
                    <span wire:loading>Girando...</span>
                </button>
                @else
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-6 text-white">
                    <p class="text-lg font-semibold mb-2">Ya giraste hoy</p>
                    <p class="text-sm opacity-90">Vuelve mañana para otro intento</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de resultado -->
    @if($showResult && $lastPrize)
    <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center transform scale-in">
            <!-- Icono según premio -->
            @if($lastPrize['type'] === 'cash')
            <div class="w-24 h-24 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-5xl">💰</span>
            </div>
            <h2 class="text-3xl font-bold text-green-600 mb-2">¡Felicitaciones!</h2>
            <p class="text-xl text-gray-900 dark:text-white mb-6">Ganaste <span class="font-bold">${{ $lastPrize['amount'] }}</span></p>
            
            @elseif($lastPrize['type'] === 'bonus')
            <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-5xl">🎁</span>
            </div>
            <h2 class="text-3xl font-bold text-blue-600 mb-2">¡Bono Especial!</h2>
            <p class="text-xl text-gray-900 dark:text-white mb-6">Ganaste <span class="font-bold">${{ $lastPrize['amount'] }}</span> en bonos</p>
            
            @elseif($lastPrize['type'] === 'free_spin')
            <div class="w-24 h-24 mx-auto mb-6 bg-purple-100 rounded-full flex items-center justify-center">
                <span class="text-5xl">🎰</span>
            </div>
            <h2 class="text-3xl font-bold text-purple-600 mb-2">¡Giro Extra!</h2>
            <p class="text-xl text-gray-900 dark:text-white mb-6">Podrás girar de nuevo mañana</p>
            
            @else
            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-5xl">😅</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-600 mb-2">Sigue Intentando</h2>
            <p class="text-lg text-gray-900 dark:text-white mb-6">No ganaste esta vez, pero mañana tendrás otra oportunidad</p>
            @endif

            <button 
                wire:click="closeResult"
                class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold rounded-lg transition">
                Cerrar
            </button>
        </div>
    </div>
    @endif

    <!-- Historial de giros -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Historial de Giros</h2>
        </div>
        
        @forelse($history as $spin)
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-3xl">
                        @if($spin->prize_type === 'cash') 💰
                        @elseif($spin->prize_type === 'bonus') 🎁
                        @elseif($spin->prize_type === 'free_spin') 🎰
                        @else 😅
                        @endif
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $spin->prize_description }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $spin->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                @if($spin->prize_amount > 0)
                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                    +${{ number_format($spin->prize_amount, 2) }}
                </p>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            <p>Aún no has girado la ruleta</p>
        </div>
        @endforelse
    </div>
</div>

<style>
.wheel-container {
    position: relative;
    width: 400px;
    height: 400px;
    margin: 0 auto;
}

.wheel {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 12px solid #fbbf24;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    transition: transform 4s cubic-bezier(0.17, 0.67, 0.12, 0.99);
}

.wheel.spinning {
    transform: rotate(1800deg); /* 5 vueltas */
}

.wheel-segment {
    position: absolute;
    width: 50%;
    height: 50%;
    top: 50%;
    left: 50%;
    transform-origin: 0% 0%;
    clip-path: polygon(0 0, 100% 0, 100% 100%);
}

.wheel-segment:nth-child(odd) {
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
}

.wheel-segment:nth-child(even) {
    background: linear-gradient(135deg, #ec4899, #f43f5e);
}

.segment-content {
    position: absolute;
    top: 20%;
    left: 50%;
    transform: translateX(-50%) rotate({{ 360 / count($prizes) / 2 }}deg);
    text-align: center;
}

.prize-label {
    display: block;
    color: white;
    font-weight: bold;
    font-size: 14px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.wheel-pointer {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 20px solid transparent;
    border-right: 20px solid transparent;
    border-top: 30px solid #fbbf24;
    z-index: 10;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}

.scale-in {
    animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0.8);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

@script
<script>
    $wire.on('spin-complete', (event) => {
        setTimeout(() => {
            $wire.showPrizeResult();
        }, 4000); // Esperar a que termine la animación
    });
</script>
@endscript

</div>