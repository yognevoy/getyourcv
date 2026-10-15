<script setup>
import { Bars3Icon } from '@heroicons/vue/20/solid';

defineProps({
    alignTop: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <span
        :class="[
            'drag-handle flex w-9 shrink-0 cursor-grab touch-none justify-center self-stretch text-ink/40 transition-colors hover:text-ink active:cursor-grabbing',
            alignTop ? 'items-start pt-3' : 'items-center',
        ]"
        title="Drag to reorder"
    >
        <Bars3Icon class="h-4 w-4" />
    </span>
</template>
