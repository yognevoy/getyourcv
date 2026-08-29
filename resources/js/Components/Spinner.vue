<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md', // sm | md | lg
    },
});

const sizeClass = computed(() => ({
    sm: 'h-4 w-4 border-2',
    md: 'h-6 w-6 border-2',
    lg: 'h-8 w-8 border-[3px]',
}[props.size]));
</script>

<template>
    <span
        class="inline-block animate-spin rounded-full border-ink/20 border-t-ink"
        :class="sizeClass"
        aria-hidden="true"
    ></span>
</template>
