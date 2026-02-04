document.addEventListener('alpine:init', () => {

    /* ============================
     | TOAST STORE (SAFE SINGLE INIT)
     ============================ */
    if (!Alpine.store('toast')) {
        Alpine.store('toast', {
            items: [],

            push(type, message) {
                const id = Date.now() + Math.random()

                this.items.push({
                    id,
                    type,
                    message,
                })

                setTimeout(() => {
                    this.items = this.items.filter(t => t.id !== id)
                }, 4000)
            }
        })
    }

    /* ============================
     | CONFIRM STORE
     ============================ */
    if (!Alpine.store('confirm')) {
        Alpine.store('confirm', {
            show: false,
            title: '',
            message: '',
            onConfirm: null,

            open({ title, message, onConfirm }) {
                this.title = title
                this.message = message
                this.onConfirm = onConfirm
                this.show = true
            },

            close() {
                this.show = false
                this.onConfirm = null
            },

            confirm() {
                if (typeof this.onConfirm === 'function') {
                    this.onConfirm()
                }
                this.close()
            }
        })
    }

})
