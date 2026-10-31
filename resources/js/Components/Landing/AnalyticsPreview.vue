<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';

defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const DAILY_VIEWS = [1, 0, 2, 1, 3, 2, 4, 3, 5, 4, 6, 5, 7, 8];

const WIDTH = 320;
const HEIGHT = 80;
const PAD = 6;
const DRAW_DURATION_MS = 1100;

const maxViews = Math.max(1, ...DAILY_VIEWS);

const points = computed(() => {
    const usableWidth = WIDTH - PAD * 2;
    const usableHeight = HEIGHT - PAD * 2;

    return DAILY_VIEWS.map((views, index) => ({
        x: PAD + (usableWidth * index) / (DAILY_VIEWS.length - 1),
        y: PAD + usableHeight - (views / maxViews) * usableHeight,
    }));
});

const path = computed(() => points.value.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x.toFixed(1)},${p.y.toFixed(1)}`).join(' '));
const end = computed(() => points.value[points.value.length - 1]);

const pathLength = computed(() =>
    points.value.slice(1).reduce((total, p, i) => {
        const prev = points.value[i];
        return total + Math.hypot(p.x - prev.x, p.y - prev.y);
    }, 0),
);

const chartEl = ref(null);
const visible = ref(false);
const endVisible = ref(false);
let observer = null;
let endTimer = null;

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

onMounted(() => {
    if (prefersReducedMotion || !chartEl.value) {
        visible.value = true;
        endVisible.value = true;
        return;
    }

    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                visible.value = true;
                endTimer = setTimeout(() => {
                    endVisible.value = true;
                }, DRAW_DURATION_MS);
                observer.disconnect();
            }
        },
        { threshold: 0.3 },
    );
    observer.observe(chartEl.value);
});

onUnmounted(() => {
    observer?.disconnect();
    clearTimeout(endTimer);
});
</script>

<template>
    <section class="relative isolate overflow-hidden px-6 py-14 sm:py-20" :class="dark ? 'bg-ink text-paper' : ''">
        <DotPatchOverlay side="left" :dark="dark" />
        <div class="relative mx-auto grid max-w-4xl items-center gap-12 lg:grid-cols-2">
            <div class="text-left">
                <h2 class="text-[clamp(1.75rem,4vw,2.75rem)] font-semibold tracking-tight">
                    See who's actually opening your resume
                </h2>
                <p class="mt-4 max-w-md" :class="dark ? 'text-paper/65' : 'text-ink/65'">
                    Every link open is counted. See your views trend day by day.
                </p>
            </div>

            <div ref="chartEl" class="rounded-lg border p-7" :class="dark ? 'border-paper/30' : 'border-ink'">
                <div class="flex items-baseline justify-between">
                    <span class="text-sm font-medium uppercase tracking-[0.15em]" :class="dark ? 'text-paper/40' : 'text-ink/40'">
                        Views, last 14 days
                    </span>
                    <span class="text-2xl font-semibold tracking-tight">{{ DAILY_VIEWS.reduce((a, b) => a + b, 0) }}</span>
                </div>
                <svg :viewBox="`0 0 ${WIDTH} ${HEIGHT}`" class="mt-4 w-full" role="img" aria-label="Sample views sparkline, trending up">
                    <path
                        :d="path"
                        fill="none"
                        :stroke="dark ? '#fdfdfd' : '#0a0a0a'"
                        stroke-width="2"
                        stroke-linejoin="round"
                        stroke-linecap="round"
                        :stroke-dasharray="pathLength"
                        :stroke-dashoffset="visible ? 0 : pathLength"
                        style="transition: stroke-dashoffset 1.1s ease-out"
                    />
                    <circle
                        :cx="end.x"
                        :cy="end.y"
                        r="3.5"
                        :fill="dark ? '#fdfdfd' : '#0a0a0a'"
                        :stroke="dark ? '#0a0a0a' : '#fdfdfd'"
                        stroke-width="1.5"
                        :opacity="endVisible ? 1 : 0"
                        style="transition: opacity 0.3s ease-out"
                    />
                </svg>
            </div>
        </div>
    </section>
</template>
