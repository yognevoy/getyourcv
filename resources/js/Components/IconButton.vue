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
        class="flex h-9 w-9 shrink-0 items-center justify-center border border-black/20 text-black/50 transition-colors hover:border-black hover:text-black"
    >
        <slot />
    </button>
</template>
