<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    alignTop: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        type="button"
        :aria-label="label"
        :title="label"
        :class="[
            'flex w-9 shrink-0 justify-center self-stretch rounded-md text-ink/50 transition-colors hover:text-ink disabled:pointer-events-none disabled:opacity-30',
            alignTop ? 'items-start pt-3' : 'items-center',
        ]"
    >
        <slot />
    </button>
</template>
