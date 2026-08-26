import { ref } from 'vue';

const toasts = ref([]);
let nextId = 1;

export function showToast(message) {
    const id = nextId++;

    toasts.value.push({ id, message });

    setTimeout(() => {
        toasts.value = toasts.value.filter((toast) => toast.id !== id);
    }, 3000);
}

export function useToast() {
    return { toasts, showToast };
}
