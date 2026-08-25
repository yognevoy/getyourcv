<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const config = {
    draft: { label: 'Draft', classes: 'border-black/30 text-black/50' },
    published: { label: 'Published', classes: 'border-black text-black' },
    hidden: { label: 'Hidden', classes: 'border-black/30 text-black/50' },
    archived: { label: 'Archived', classes: 'border-yellow-600 bg-yellow-50 text-yellow-800' },
};

const badge = computed(() => config[props.status] ?? { label: props.status, classes: 'border-black/30 text-black/50' });
</script>

<template>
    <span
        class="inline-block border px-2 py-0.5 text-[11px] font-medium uppercase tracking-wide"
        :class="badge.classes"
    >
        {{ badge.label }}
    </span>
</template>
