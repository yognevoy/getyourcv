<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import CloseButton from '@/Components/CloseButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    text: {
        type: String,
        required: true,
    },
    target: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['close', 'apply']);

const VARIANTS = [
    { key: 'shorter', label: 'Shorter' },
    { key: 'stronger', label: 'Stronger' },
    { key: 'with_numbers', label: 'With numbers' },
];

const loading = ref(false);
const error = ref(null);
const variants = ref(null);

watch(
    () => props.show,
    (show) => {
        if (show) {
            fetchVariants();
        }
    },
);

async function fetchVariants() {
    loading.value = true;
    error.value = null;
    variants.value = null;

    try {
        const response = await axios.post(route('ai.rewrite'), {
            text: props.text,
            target: props.target,
        });

        variants.value = response.data.variants;
    } catch (e) {
        error.value = e.response?.data?.message || 'Something went wrong. Please try again.';
    } finally {
        loading.value = false;
    }
}

function apply(text) {
    emit('apply', text);
    emit('close');
}
</script>

<template>
    <Modal :show="show" max-width="lg" :bordered="false" @close="emit('close')">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-ink">AI rewrite</h2>
                <CloseButton @click="emit('close')" />
            </div>

            <p v-if="loading" class="mt-6 text-sm text-ink/50">
                Generating variants…
            </p>

            <div v-else-if="error" class="mt-6 space-y-3">
                <p class="text-sm text-ink/60">{{ error }}</p>
                <button
                    type="button"
                    class="text-sm text-ink underline-offset-2 hover:underline"
                    @click="fetchVariants"
                >
                    Try again
                </button>
            </div>

            <ul v-else-if="variants" class="mt-6 space-y-4">
                <li
                    v-for="variant in VARIANTS"
                    :key="variant.key"
                    class="rounded-md border border-ink/15 p-3"
                >
                    <span class="block text-xs font-medium uppercase tracking-wide text-ink/50">
                        {{ variant.label }}
                    </span>
                    <p class="mt-1 whitespace-pre-wrap text-sm text-ink">{{ variants[variant.key] }}</p>
                    <button
                        type="button"
                        class="mt-2 text-sm text-ink underline-offset-2 hover:underline"
                        @click="apply(variants[variant.key])"
                    >
                        Use this
                    </button>
                </li>
            </ul>
        </div>
    </Modal>
</template>
