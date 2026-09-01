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
        class="inline-flex items-center rounded-md px-4 py-2 text-xs font-semibold uppercase tracking-widest text-ink/60 transition-colors duration-150 ease-in-out hover:text-ink focus:outline-none disabled:opacity-40"
    >
        <slot />
    </button>
</template>
