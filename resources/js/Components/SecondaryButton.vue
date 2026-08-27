<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-ink bg-paper px-4 py-2 text-xs font-semibold uppercase tracking-widest text-ink transition-colors duration-150 ease-in-out hover:bg-ink hover:text-paper focus:outline-none disabled:opacity-40"
    >
        <slot />
    </button>
</template>
