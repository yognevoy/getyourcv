<script setup>
import { nextTick, onMounted, ref, watch } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const textarea = ref(null);

function resize() {
    if (!textarea.value) {
        return;
    }

    textarea.value.style.height = 'auto';
    textarea.value.style.height = `${textarea.value.scrollHeight}px`;
}

onMounted(resize);
watch(model, () => nextTick(resize));

defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <textarea
        ref="textarea"
        v-model="model"
        rows="1"
        class="block w-full resize-none overflow-hidden rounded-md border border-ink/20 px-3 py-2 text-ink transition-colors focus:border-ink focus:outline-none focus:ring-0"
        @input="resize"
    />
</template>
