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
        class="border border-black/20 px-3 py-2 text-black transition-colors focus:border-black focus:outline-none focus:ring-0 disabled:border-black/10 disabled:bg-black/5 disabled:text-black/40"
        v-model="model"
        ref="input"
    />
</template>
