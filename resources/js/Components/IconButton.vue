<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <button
        type="button"
        :aria-label="label"
        :title="label"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-ink/20 text-ink/50 transition-colors hover:border-ink hover:text-ink disabled:pointer-events-none disabled:opacity-30"
    >
        <slot />
    </button>
</template>
