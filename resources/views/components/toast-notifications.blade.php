<div x-data="{ 
    notifications: [],
    nextId: 1,
    add(message, type) {
        const id = this.nextId++;
        this.notifications.push({ id, message, type });
    },
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}" 
     @notify.window="
        add($event.detail.message, $event.detail.type);
        const id = nextId - 1;
        setTimeout(() => remove(id), 4000);
     "
     style="position: fixed; top: 1rem; right: 1rem; z-index: 9999; max-width: 24rem; width: 100%;">
    
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="true"
             x-transition
             style="margin-bottom: 0.75rem; border-radius: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); padding: 1rem; border-width: 2px; border-style: solid;"
             :style="{
                backgroundColor: notification.type === 'success' ? '#dcfce7' : 
                                notification.type === 'error' ? '#fee2e2' : 
                                notification.type === 'warning' ? '#fef3c7' : 
                                '#dbeafe',
                borderColor: notification.type === 'success' ? '#22c55e' : 
                            notification.type === 'error' ? '#ef4444' : 
                            notification.type === 'warning' ? '#eab308' : 
                            '#3b82f6'
             }">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <!-- Icon -->
                <div style="flex-shrink: 0;">
                    <svg x-show="notification.type === 'success'" style="width: 1.5rem; height: 1.5rem; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="notification.type === 'error'" style="width: 1.5rem; height: 1.5rem; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="notification.type === 'warning'" style="width: 1.5rem; height: 1.5rem; color: #eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <svg x-show="notification.type === 'info'" style="width: 1.5rem; height: 1.5rem; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <!-- Message -->
                <div style="flex: 1;">
                    <p style="font-weight: 600; font-size: 0.875rem; color: #1f2937;" x-text="notification.message"></p>
                </div>

                <!-- Close Button -->
                <button @click="remove(notification.id)" style="flex-shrink: 0; cursor: pointer; background: none; border: none; padding: 0;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>