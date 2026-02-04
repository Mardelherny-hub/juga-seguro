<div>
    <!-- Modal Flotante estilo WhatsApp -->
    @if($isOpen)
    <div class="fixed bottom-4 right-4 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-lg shadow-2xl z-50 flex flex-col"
         style="height: 600px; max-height: calc(100vh - 2rem);">
        
        <!-- Header del Modal -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-t-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Mensajes</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Centro de soporte</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                @if($whatsappLink)
                <a href="{{ $whatsappLink }}" 
                   target="_blank"
                   rel="noopener noreferrer"
                   title="Contactar por WhatsApp"
                   class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
                @endif
                
                <button wire:click="closeChat" 
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mensajes -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900" 
             wire:poll.5s
             id="chat-messages"
             style="background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgwLDAsMCwwLjAyKSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+'); background-size: 40px 40px;">
            
            @forelse($messages as $msg)
                <div class="flex {{ $msg->isFromPlayer() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%]">
                        <!-- Mensaje del Sistema -->
                        @if($msg->isFromSystem())
                        <div class="px-3 py-2 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800">
                            <p class="text-xs text-yellow-900 dark:text-yellow-100">{!! nl2br(e($msg->message)) !!}</p>
                            @if($msg->image_path)
                            <a href="{{ $msg->image_url }}" target="_blank" class="block mt-2">
                                <img src="{{ $msg->image_url }}" class="max-w-full max-h-48 rounded-lg cursor-pointer hover:opacity-90 transition object-contain" alt="Imagen adjunta">
                            </a>
                            @endif
                            <p class="text-[10px] text-yellow-600 dark:text-yellow-400 mt-1">
                                {{ $msg->created_at->format('H:i') }}
                            </p>
                        </div>
                        
                        <!-- Mensaje del Jugador -->
                        @elseif($msg->isFromPlayer())
                        <div class="px-3 py-2 rounded-lg bg-blue-600 text-white shadow">
                            <p class="text-sm">{!! nl2br(e($msg->message)) !!}</p>
                            @if($msg->image_path)
                            <a href="{{ $msg->image_url }}" target="_blank" class="block mt-2">
                                <img src="{{ $msg->image_url }}" class="max-w-full max-h-48 rounded-lg cursor-pointer hover:opacity-90 transition object-contain" alt="Imagen adjunta">
                            </a>
                            @endif
                            <p class="text-[10px] text-blue-100 mt-1 text-right">
                                {{ $msg->created_at->format('H:i') }}
                            </p>
                        </div>
                        
                        <!-- Mensaje del Agente -->
                        @elseif($msg->isFromAgent())
                        <div class="px-3 py-2 rounded-lg bg-white dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-900 dark:text-white">{!! nl2br(e($msg->message)) !!}</p>
                            @if($msg->image_path)
                            <a href="{{ $msg->image_url }}" target="_blank" class="block mt-2">
                                <img src="{{ $msg->image_url }}" class="max-w-full max-h-48 rounded-lg cursor-pointer hover:opacity-90 transition object-contain" alt="Imagen adjunta">
                            </a>
                            @endif
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">
                                Soporte • {{ $msg->created_at->format('H:i') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-sm">No hay mensajes</p>
                </div>
            @endforelse
        </div>

        <!-- Input de mensaje -->
        <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-b-lg">
            <form wire:submit.prevent="sendMessage">
                <div class="space-y-2">
                    <!-- Preview de imagen -->
                    @if($messageImage)
                    <div class="relative inline-block">
                        <img src="{{ $messageImage->temporaryUrl() }}" class="h-16 w-16 object-cover rounded-lg border border-gray-300">
                        <button type="button" wire:click="$set('messageImage', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                            ×
                        </button>
                    </div>
                    @endif
                    
                    <div class="flex gap-2">
                        <!-- Botón adjuntar imagen -->
                        <label class="p-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-full cursor-pointer transition flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="file" wire:model="messageImage" accept="image/*" class="hidden">
                        </label>
                        
                        <input 
                            wire:model="newMessage" 
                            type="text"
                            placeholder="Escribe un mensaje..."
                            class="flex-1 px-3 py-2 rounded-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 text-sm"
                            @keydown.enter.prevent="$wire.sendMessage()"
                        />
                        
                        <button 
                            type="submit"
                            class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </div>
                    
                    @error('messageImage')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                    @error('newMessage')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

@script
<script>
    let userIsScrolling = false;
    
    // Detectar si el usuario está leyendo mensajes anteriores
    const container = document.getElementById('chat-messages');
    if (container) {
        container.addEventListener('scroll', function() {
            const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;
            userIsScrolling = !isAtBottom;
        });
    }
    
    // Auto-scroll solo si el usuario está cerca del fondo
    function scrollToBottomIfNeeded() {
        if (userIsScrolling) return;
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    // Scroll forzado (cuando envía mensaje)
    function scrollToBottom() {
        userIsScrolling = false;
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    // Al enviar mensaje, siempre bajar
    $wire.on('message-sent', () => {
        setTimeout(scrollToBottom, 100);
    });
    
    // Al recibir mensajes, solo bajar si está cerca del fondo
    Livewire.hook('morph.updated', () => {
        setTimeout(scrollToBottomIfNeeded, 100);
    });
    
    // Scroll inicial cuando se abre
    setTimeout(scrollToBottom, 200);
</script>
@endscript
