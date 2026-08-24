<script setup>
defineProps({
    index: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-baseline gap-3 border-b border-black pb-2">
        <span class="font-mono text-xs text-black/40">{{ index }}</span>
        <h2 class="text-sm font-semibold uppercase tracking-wide">{{ title }}</h2>
    </div>
</template>
