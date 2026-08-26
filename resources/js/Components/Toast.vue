<script setup>
import { useToast } from '@/composables/useToast';

const { toasts } = useToast();
</script>

<template>
    <div class="pointer-events-none fixed right-4 top-4 z-[60] flex flex-col items-end gap-2">
        <TransitionGroup
            enter-active-class="transition ease-out duration-150"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="border border-black bg-white px-4 py-2 text-sm text-black"
            >
                {{ toast.message }}
            </div>
        </TransitionGroup>
    </div>
</template>
