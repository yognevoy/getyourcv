<script setup>
import DotFieldCanvas from '@/Components/Landing/DotFieldCanvas.vue';

defineProps({
    side: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'right'].includes(value),
    },
    dark: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div
        class="pointer-events-none absolute inset-y-0 w-1/2 overflow-hidden"
        :class="side === 'left' ? 'left-0 fade-left' : 'right-0 fade-right'"
    >
        <DotFieldCanvas :color="dark ? 'paper' : 'ink'" />
    </div>
</template>

<style scoped>
.fade-left {
    mask-image: linear-gradient(to right, black 0%, black 45%, transparent 90%);
    -webkit-mask-image: linear-gradient(to right, black 0%, black 45%, transparent 90%);
}

.fade-right {
    mask-image: linear-gradient(to left, black 0%, black 45%, transparent 90%);
    -webkit-mask-image: linear-gradient(to left, black 0%, black 45%, transparent 90%);
}
</style>
