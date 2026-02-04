<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mensajes de Jugadores</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Gestiona la comunicación con tus jugadores
            @if($totalUnreadCount > 0)
                <span class="ml-2 px-2 py-1 bg-red-600 text-white text-xs rounded-full">
                    {{ $totalUnreadCount }} sin leer
                </span>
            @endif
        </p>
        </div>
        @livewire('agent.broadcast-message')
    </div>

    <!-- Grid: Lista de jugadores + Conversación -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Lista de jugadores (izquierda) -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <!-- Búsqueda -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Buscar jugador..."
                        class="w-full px-3 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                    />
                </div>
                
                <!-- Lista de jugadores -->
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[600px] overflow-y-auto">
                    @php
                        $searchTerm = mb_strtolower($search, 'UTF-8');
                        $filteredAndSortedPlayers = $players
                            ->filter(function($player) use ($searchTerm) {
                                if (empty($searchTerm)) return true;
                                return Str::contains(mb_strtolower($player->username, 'UTF-8'), $searchTerm) || 
                                    Str::contains($player->phone, $searchTerm);
                            })
                            ->sortByDesc(function($player) {
                                return $player->messages->max('created_at') ?? $player->created_at;
                            });
                    @endphp
                    @forelse($players as $player)
                    <button 
                        wire:click="selectPlayer({{ $player->id }})"
                        class="w-full text-left p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $selectedPlayerId == $player->id ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                        
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($player->username, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $player->username }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $player->phone }}</p>
                                </div>
                            </div>
                            
                            @if($player->unread_count > 0)
                            <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full font-bold">
                                {{ $player->unread_count }}
                            </span>
                            @endif
                        </div>
                        
                        @php $latestMsg = $player->messages->sortByDesc('created_at')->first(); @endphp
                        @if($latestMsg)
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ Str::limit($latestMsg->message, 50) }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ $latestMsg->created_at->diffForHumans() }}
                        </p>
                        @endif
                    </button>
                    @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p>{{ $search ? 'No se encontraron jugadores para "' . $search . '"' : 'No hay mensajes aún' }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Conversación (derecha) -->
        <div class="lg:col-span-2">
            @if($selectedPlayer)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <!-- Header del jugador -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($selectedPlayer->username, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $selectedPlayer->username }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedPlayer->phone }}</p>
                        </div>
                    </div>
                    
                    {{-- <a href="{{ route('agent.players.show', $selectedPlayer->id) }}" 
                       class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                        Ver Perfil
                    </a> --}}
                </div>

                <!-- Mensajes -->
                <div class="p-4 space-y-3 max-h-[450px] overflow-y-auto bg-gray-50 dark:bg-gray-900" 
                     wire:poll.live5s
                     id="agent-messages-container">
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->isFromAgent() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%]">
                                <!-- Mensaje del Sistema -->
                                @if($msg->isFromSystem())
                                <div class="px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <p class="text-xs text-blue-900 dark:text-blue-100">{!! nl2br(e($msg->message)) !!}</p>
                                            @if($msg->image_path)
                                            <a href="{{ $msg->image_url }}" target="_blank" class="block mt-2">
                                                <img src="{{ $msg->image_url }}" class="max-w-xs max-h-48 rounded-lg cursor-pointer hover:opacity-90 transition" alt="Imagen adjunta">
                                            </a>
                                            @endif
                                        </div>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        Sistema • {{ $msg->created_at->format('d/m H:i') }}
                                    </p>
                                </div>
                                
                                <!-- Mensaje del Jugador -->
                                @elseif($msg->isFromPlayer())
                                <div class="px-3 py-2 rounded-lg bg-white dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-900 dark:text-white">{!! nl2br(e($msg->message)) !!}</p>
                                        @if($msg->image_path)
                                        <a href="{{ $msg->image_url }}" target="_blank" class="block mt-2">
                                            <img src="{{ $msg->image_url }}" class="max-w-xs max-h-48 rounded-lg cursor-pointer hover:opacity-90 transition" alt="Imagen adjunta">
                                        </a>
                                        @endif
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $selectedPlayer->username }} • {{ $msg->created_at->format('d/m H:i') }}
                                    </p>
                                </div>
                                
                                <!-- Mensaje del Agente -->
                                @elseif($msg->isFromAgent())
                                <div class="px-3 py-2 rounded-lg bg-blue-600 text-white shadow">
                                    <p class="text-sm">{!! nl2br(e($msg->message)) !!}</p>
                                        @if($msg->image_path)
                                        <a href="{{ $msg->image_url }}" target="_blank" class="block mt-2">
                                            <img src="{{ $msg->image_url }}" class="max-w-xs max-h-48 rounded-lg cursor-pointer hover:opacity-90 transition" alt="Imagen adjunta">
                                        </a>
                                        @endif
                                    <p class="text-xs text-blue-100 mt-1 text-right">
                                        Tú • {{ $msg->created_at->format('d/m H:i') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Templates de respuesta rápida -->
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 font-medium">Respuestas rápidas:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($quickReplies as $label => $message)
                        <button 
                            wire:click="sendQuickReply('{{ $message }}')"
                            class="px-3 py-1 text-xs bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full border border-gray-300 dark:border-gray-600 transition">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Input de respuesta -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <form wire:submit.prevent="sendMessage">
                        <div class="space-y-3">
                            <!-- Preview de imagen -->
                            @if($messageImage)
                            <div class="relative inline-block">
                                <img src="{{ $messageImage->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-lg border border-gray-300">
                                <button type="button" wire:click="$set('messageImage', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                                    ×
                                </button>
                            </div>
                            @endif
                            
                            <div class="flex gap-3">
                                <!-- Botón adjuntar imagen -->
                                <label class="p-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg cursor-pointer transition self-end">
                                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <input type="file" wire:model="messageImage" accept="image/*" capture="environment" class="hidden">
                                </label>
                                
                                <textarea 
                                    wire:model="newMessage" 
                                    rows="2"
                                    placeholder="Escribe tu respuesta..."
                                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 resize-none"
                                ></textarea>
                                
                                <button 
                                    type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition self-end">
                                    Enviar
                                </button>
                            </div>
                            
                            @error('messageImage')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            @error('newMessage')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-lg">Selecciona un jugador para ver la conversación</p>
            </div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    let shouldScroll = true;
    
    // Auto-scroll al último mensaje (solo si corresponde)
    function scrollToBottom() {
        if (!shouldScroll) return;
        
        const container = document.getElementById('agent-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    // Detectar si el usuario está leyendo mensajes anteriores
    const container = document.getElementById('agent-messages-container');
    if (container) {
        container.addEventListener('scroll', function() {
            const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;
            shouldScroll = isAtBottom;
        });
    }
    
    // Scroll cuando se envía un mensaje (siempre bajar)
    $wire.on('message-sent', () => {
        shouldScroll = true;
        setTimeout(scrollToBottom, 100);
    });
    
    // Scroll cuando llegan mensajes nuevos (solo si está cerca del fondo)
    Livewire.hook('morph.updated', () => {
        setTimeout(scrollToBottom, 100);
    });
    
    // Scroll inicial al seleccionar jugador (siempre bajar)
    $wire.on('playerSelected', () => {
        shouldScroll = true;
        setTimeout(scrollToBottom, 200);
    });
    
    setTimeout(scrollToBottom, 300);
</script>
@endscript