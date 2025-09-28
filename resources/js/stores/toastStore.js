import { defineStore } from "pinia";

let nextId = 1;

export const useToastStore = defineStore("toast", {
    state: () => ({
        toasts: [] // { id, type, message, timeout }
    }),
    getters: {},
    actions: {
        show(message, type = 'info', timeout = 3000) {
            const id = nextId++;
            const toast = { id, type, message, timeout };
            this.toasts = [...this.toasts, toast];
            if (timeout > 0) {
                setTimeout(() => this.remove(id), timeout);
            }
            return id;
        },
        success(message, timeout = 3000) {
            return this.show(message, 'success', timeout);
        },
        error(message, timeout = 4000) {
            return this.show(message, 'error', timeout);
        },
        info(message, timeout = 3000) {
            return this.show(message, 'info', timeout);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
        clear() {
            this.toasts = [];
        }
    }
});


