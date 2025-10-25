<div x-data="{ 
    notifications: [],
    nextId: 1,
    add(message, type) {
        const id = this.nextId++;
        this.notifications.push({ id, message, type, persistent: type === 'transaction' });
    },
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}" 
     @notify.window="
        const isPersistent = $event.detail.persistent || false;
        add($event.detail.message, $event.detail.type);
        if (!isPersistent) {
            const id = nextId - 1;
            setTimeout(() => remove(id), 4000);
        }
     "
     class="fixed top-4 right-4 z-[100] max-w-sm w-full space-y-3">
    
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="rounded-lg shadow-2xl p-4 border-2"
             :class="{
                'bg-green-500 border-green-600': notification.type === 'success',
                'bg-red-500 border-red-600': notification.type === 'error',
                'bg-yellow-500 border-yellow-600': notification.type === 'warning',
                'bg-blue-500 border-blue-600': notification.type === 'info',
                'bg-orange-500 border-orange-600 animate-pulse': notification.type === 'transaction'
             }">
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <svg x-show="notification.type === 'success'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="notification.type === 'error'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="notification.type === 'transaction'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <!-- Message -->
                <div class="flex-1">
                    <p class="text-white font-semibold text-sm" x-text="notification.message"></p>
                </div>

                <!-- Close Button -->
                <button @click="remove(notification.id)" class="flex-shrink-0 text-white hover:text-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>