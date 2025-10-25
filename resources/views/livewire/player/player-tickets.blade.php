<div class="max-w-7xl mx-auto p-6">
    <!-- Header con botones -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Soporte</h1>
        
        <div class="flex gap-3">
            @if($whatsappLink)
            <a href="{{ $whatsappLink }}" 
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition shadow-lg">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Contactar por WhatsApp
            </a>
            @endif
            
            <button wire:click="openCreateModal"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Solicitud
            </button>
        </div>
    </div>

    <!-- Mensajes flash -->
    @if(session()->has('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Grid: Lista de tickets + Conversación -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Lista de tickets (izquierda) -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Mis Tickets</h2>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[600px] overflow-y-auto">
                    @forelse($tickets as $ticket)
                    <button wire:click="selectTicket({{ $ticket->id }})"
                            class="w-full text-left p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $selectedTicketId == $ticket->id ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $ticket->subject }}</h3>
                            @if($ticket->unreadMessagesForPlayer()->count() > 0)
                            <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                                {{ $ticket->unreadMessagesForPlayer()->count() }}
                            </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $ticket->status === 'waiting_player' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                @switch($ticket->status)
                                    @case('open') Abierto @break
                                    @case('in_progress') En progreso @break
                                    @case('waiting_player') Esperando respuesta @break
                                    @case('resolved') Resuelto @break
                                    @case('closed') Cerrado @break
                                @endswitch
                            </span>
                        </div>
                        
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </p>
                    </button>
                    @empty
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <p>No tienes tickets aún</p>
                        <p class="text-sm mt-2">Crea tu primera solicitud</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Conversación (derecha) -->
        <div class="lg:col-span-2">
            @if($selectedTicket)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <!-- Header del ticket -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $selectedTicket->subject }}</h2>
                    <div class="flex gap-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ ucfirst($selectedTicket->category) }}
                        </span>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $selectedTicket->status === 'open' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $selectedTicket->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $selectedTicket->status === 'waiting_player' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $selectedTicket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $selectedTicket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                            @switch($selectedTicket->status)
                                @case('open') Abierto @break
                                @case('in_progress') En progreso @break
                                @case('waiting_player') Esperando tu respuesta @break
                                @case('resolved') Resuelto @break
                                @case('closed') Cerrado @break
                            @endswitch
                        </span>
                    </div>
                </div>

                <!-- Mensajes -->
                <div class="p-4 space-y-4 max-h-[450px] overflow-y-auto" wire:poll.5s>
                    @foreach($selectedTicket->messages as $msg)
                        @if(!$msg->is_internal_note)
                        <div class="flex {{ $msg->isFromPlayer() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[70%]">
                                <div class="px-4 py-2 rounded-lg {{ $msg->isFromPlayer() ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' }}">
                                    <p class="text-sm">{{ $msg->message }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $msg->isFromPlayer() ? 'text-right' : '' }}">
                                    {{ $msg->isFromPlayer() ? 'Tú' : 'Soporte' }} • {{ $msg->created_at->format('d/m H:i') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                <!-- Input de respuesta -->
                @if(!in_array($selectedTicket->status, ['closed', 'resolved']))
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <form wire:submit.prevent="sendMessage">
                        <div class="flex gap-2">
                            <textarea wire:model="newMessage" 
                                      rows="2"
                                      placeholder="Escribe tu respuesta..."
                                      class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"></textarea>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                Enviar
                            </button>
                        </div>
                        @error('newMessage')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
                @else
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">
                    Este ticket está {{ $selectedTicket->status === 'closed' ? 'cerrado' : 'resuelto' }}
                </div>
                @endif
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center text-gray-500 dark:text-gray-400">
                <p class="text-lg">Selecciona un ticket para ver la conversación</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal: Crear Ticket -->
    @if($showCreateModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Crear Nueva Solicitud</h2>
                
                <form wire:submit.prevent="createTicket">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Asunto</label>
                            <input wire:model="subject" 
                                   type="text"
                                   placeholder="¿En qué podemos ayudarte?"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('subject')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría</label>
                            <select wire:model="category"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">Selecciona una categoría</option>
                                <option value="deposit">Depósito</option>
                                <option value="withdrawal">Retiro</option>
                                <option value="account">Cuenta</option>
                                <option value="bonus">Bonos</option>
                                <option value="technical">Técnico</option>
                                <option value="other">Otro</option>
                            </select>
                            @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mensaje</label>
                            <textarea wire:model="message"
                                      rows="4"
                                      placeholder="Describe tu problema o consulta..."
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button"
                                wire:click="closeCreateModal"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Crear Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>