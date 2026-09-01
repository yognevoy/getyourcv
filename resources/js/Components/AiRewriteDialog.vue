<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import CloseButton from '@/Components/CloseButton.vue';
import Modal from '@/Components/Modal.vue';
import Spinner from '@/Components/Spinner.vue';

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
const activeTab = ref(VARIANTS[0].key);

watch(
    () => props.show,
    (show) => {
        if (show) {
            activeTab.value = VARIANTS[0].key;
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
    <Modal :show="show" max-width="lg" @close="emit('close')">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-ink">AI rewrite</h2>
                <CloseButton @click="emit('close')" />
            </div>

            <div v-if="loading" class="mt-6 flex flex-col items-center gap-3 py-8">
                <Spinner size="lg" />
                <p class="text-sm text-ink/50">Generating variants…</p>
            </div>

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

            <div v-else-if="variants" class="mt-6">
                <div class="flex border-b border-ink/15">
                    <button
                        v-for="v in VARIANTS"
                        :key="v.key"
                        type="button"
                        class="flex-1 border-b-2 px-2 py-2 text-xs font-medium uppercase tracking-wide transition-colors"
                        :class="activeTab === v.key ? 'border-ink text-ink' : 'border-transparent text-ink/40 hover:text-ink/70'"
                        @click="activeTab = v.key"
                    >
                        {{ v.label }}
                    </button>
                </div>
                <p class="mt-4 min-h-[4rem] whitespace-pre-wrap text-sm text-ink">{{ variants[activeTab] }}</p>
                <button
                    type="button"
                    class="mt-4 text-sm text-ink underline-offset-2 hover:underline"
                    @click="apply(variants[activeTab])"
                >
                    Use this
                </button>
            </div>
        </div>
    </Modal>
</template>
