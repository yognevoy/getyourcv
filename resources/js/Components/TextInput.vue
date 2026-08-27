<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border border-ink/20 px-3 py-2 text-ink transition-colors focus:border-ink focus:outline-none focus:ring-0 disabled:border-ink/10 disabled:bg-ink/5 disabled:text-ink/40"
        v-model="model"
        ref="input"
    />
</template>
