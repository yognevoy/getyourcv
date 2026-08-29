<script setup>
import RemoveIcon from '@/Components/RemoveIcon.vue';
</script>

<template>
    <button
        type="button"
        aria-label="Close"
        title="Close"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-ink/50 transition-colors hover:text-ink disabled:pointer-events-none disabled:opacity-30"
    >
        <RemoveIcon />
    </button>
</template>
