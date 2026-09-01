<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import LinkButton from '@/Components/LinkButton.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        default: '',
    },
    confirmLabel: {
        type: String,
        default: 'Confirm',
    },
    danger: {
        type: Boolean,
        default: false,
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <Modal :show="show" max-width="sm" @close="emit('cancel')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-ink">
                {{ title }}
            </h2>

            <p v-if="message" class="mt-1 text-sm text-ink/60">
                {{ message }}
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <LinkButton @click="emit('cancel')">
                    Cancel
                </LinkButton>

                <DangerButton
                    v-if="danger"
                    :disabled="processing"
                    :class="{ 'opacity-40': processing }"
                    @click="emit('confirm')"
                >
                    {{ confirmLabel }}
                </DangerButton>
                <PrimaryButton
                    v-else
                    :disabled="processing"
                    :class="{ 'opacity-40': processing }"
                    @click="emit('confirm')"
                >
                    {{ confirmLabel }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
