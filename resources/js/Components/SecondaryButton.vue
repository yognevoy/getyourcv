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
        class="inline-flex items-center border border-black bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-black transition-colors duration-150 ease-in-out hover:bg-black hover:text-white focus:outline-none disabled:opacity-40"
    >
        <slot />
    </button>
</template>
