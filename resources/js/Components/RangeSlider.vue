<script setup>
defineProps({
    modelValue: {
        type: Number,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    min: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 200,
    },
});

const emit = defineEmits(['update:modelValue']);

function onInput(event) {
    emit('update:modelValue', Number(event.target.value));
}
</script>

<template>
    <div>
        <label class="flex justify-between text-[10px] uppercase tracking-wider text-ink/40">
            <span>{{ label }}</span>
            <span>{{ modelValue }}%</span>
        </label>
        <input type="range" :min="min" :max="max" :value="modelValue" class="range-hairline mt-2 w-full" @input="onInput" />
    </div>
</template>

<style scoped>
.range-hairline {
    -webkit-appearance: none;
    appearance: none;
    height: 2px;
    background: rgba(0, 0, 0, 0.15);
}
.range-hairline::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 9999px;
    background: radial-gradient(circle, black 4px, transparent 4px);
    cursor: pointer;
}
.range-hairline::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 9999px;
    background: radial-gradient(circle, black 4px, transparent 4px);
    border: none;
    cursor: pointer;
}
</style>
