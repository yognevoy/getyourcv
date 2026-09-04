<script setup>
import { computed } from 'vue';

const props = defineProps({
    dailyViews: {
        type: Array,
        required: true,
    },
    days: {
        type: Number,
        default: 30,
    },
});

const WIDTH = 160;
const HEIGHT = 40;
const PAD = 4;

const visibleDays = computed(() => props.dailyViews.slice(-props.days));
const maxViews = computed(() => Math.max(1, ...visibleDays.value.map((day) => day.views)));

const points = computed(() => {
    const usableWidth = WIDTH - PAD * 2;
    const usableHeight = HEIGHT - PAD * 2;

    return visibleDays.value.map((day, index) => {
        const x = PAD + (usableWidth * index) / (visibleDays.value.length - 1);
        const y = PAD + usableHeight - (day.views / maxViews.value) * usableHeight;

        return { x, y };
    });
});

const path = computed(() => points.value.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x.toFixed(1)},${p.y.toFixed(1)}`).join(' '));

const end = computed(() => points.value[points.value.length - 1]);
</script>

<template>
    <svg
        :viewBox="`0 0 ${WIDTH} ${HEIGHT}`"
        width="150"
        height="40"
        role="img"
        :aria-label="`Sparkline of daily views, last ${days} days, ending at ${visibleDays[visibleDays.length - 1].views} views today`"
    >
        <path :d="path" fill="none" stroke="#0a0a0a" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
        <circle :cx="end.x" :cy="end.y" r="3" fill="#0a0a0a" stroke="#fdfdfd" stroke-width="1.5" />
    </svg>
</template>
