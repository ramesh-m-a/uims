<div
    x-data="{
        init() {
            document.addEventListener('livewire:navigated', () => {
                this.register();
            });

            document.addEventListener('livewire:init', () => {
                this.register();
            });

            this.register();
        },

        register() {
            if (this._registered) return;
            this._registered = true;

            window.Livewire?.on('toast', (payload) => {
                Alpine.store('toast').push(payload.type, payload.message);
            });
        }
    }"
    class="fixed top-4 right-4 z-[9999] space-y-2"
>
    <template x-for="toast in $store.toast.items" :key="toast.id">
        <div
            class="rounded-md px-4 py-3 shadow-lg text-white"
            :class="{
                'bg-green-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error',
                'bg-yellow-600': toast.type === 'warning',
                'bg-blue-600': toast.type === 'info',
            }"
        >
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>
