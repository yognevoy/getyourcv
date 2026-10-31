<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';
import { LAND_RINGS, MAP_W, MAP_H, REGIONS } from '@/Components/Landing/mapData.js';

defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

const DOT_SPACING = 3;

function inPolygon(x, y, points) {
    let inside = false;
    for (let i = 0, j = points.length - 1; i < points.length; j = i++) {
        const [xi, yi] = points[i];
        const [xj, yj] = points[j];
        if ((yi > y) !== (yj > y) && x < ((xj - xi) * (y - yi)) / (yj - yi) + xi) {
            inside = !inside;
        }
    }
    return inside;
}

function inLandmass(x, y) {
    return LAND_RINGS.some((ring) => inPolygon(x, y, ring));
}

const mapDots = [];
for (let x = 0; x <= MAP_W; x += DOT_SPACING) {
    for (let y = 0; y <= MAP_H; y += DOT_SPACING) {
        if (inLandmass(x, y)) mapDots.push({ x, y });
    }
}

const CONNECTIONS = [
    [0, 1],
    [0, 2],
    [0, 3],
    [1, 2],
    [1, 3],
    [2, 3],
];

const el = ref(null);
const visible = ref(false);
let observer = null;

onMounted(() => {
    if (prefersReducedMotion || !el.value) {
        visible.value = true;
        return;
    }
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                visible.value = true;
                observer.disconnect();
            }
        },
        { threshold: 0.15 },
    );
    observer.observe(el.value);
});

onUnmounted(() => observer?.disconnect());
</script>

<template>
    <section class="relative isolate overflow-hidden px-6 py-14 sm:py-20" :class="dark ? 'bg-ink text-paper' : ''">
        <DotPatchOverlay side="right" :dark="dark" />
        <div class="relative mx-auto max-w-4xl text-center">
            <h2 class="text-[clamp(1.75rem,4vw,2.75rem)] font-semibold tracking-tight">
                Built for HR anywhere in the world
            </h2>
            <p class="mx-auto mt-4 max-w-md" :class="dark ? 'text-paper/65' : 'text-ink/65'">
                Your link loads fast wherever the recruiter opens it from - no regional
                restrictions, no waiting.
            </p>

            <div ref="el" class="mt-10">
                <svg :viewBox="`0 0 ${MAP_W} ${MAP_H}`" class="mx-auto w-full max-w-2xl" role="img" aria-label="World map with resume link load times by region">
                    <g v-for="([a, b], i) in CONNECTIONS" :key="`conn-${i}`">
                        <path
                            :id="`gyc-map-conn-${i}`"
                            :d="`M ${REGIONS[a].x} ${REGIONS[a].y} L ${REGIONS[b].x} ${REGIONS[b].y}`"
                            fill="none"
                            :stroke="dark ? '#fdfdfd' : '#0a0a0a'"
                            stroke-width="0.75"
                            stroke-opacity="0.15"
                        />
                        <circle v-if="!prefersReducedMotion && visible" r="1.8" :fill="dark ? '#fdfdfd' : '#0a0a0a'" fill-opacity="0.85">
                            <animateMotion :dur="`${3.5 + i * 0.6}s`" repeatCount="indefinite" :begin="`${i * 0.5}s`">
                                <mpath :href="`#gyc-map-conn-${i}`" />
                            </animateMotion>
                        </circle>
                    </g>
                    <circle
                        v-for="(dot, i) in mapDots"
                        :key="i"
                        :cx="dot.x"
                        :cy="dot.y"
                        r="0.8"
                        :fill="dark ? '#fdfdfd' : '#0a0a0a'"
                        fill-opacity="0.25"
                    />
                    <g v-for="region in REGIONS" :key="region.place">
                        <circle
                            :cx="region.x"
                            :cy="region.y"
                            r="3"
                            :class="prefersReducedMotion ? '' : 'map-ping'"
                            fill="none"
                            :stroke="dark ? '#fdfdfd' : '#0a0a0a'"
                            stroke-width="1"
                        />
                        <circle :cx="region.x" :cy="region.y" r="2.5" :fill="dark ? '#fdfdfd' : '#0a0a0a'" />
                        <text :x="region.x" :y="region.y - 8" text-anchor="middle" font-size="8" :fill="dark ? '#fdfdfd' : '#0a0a0a'">
                            {{ region.ms }}ms
                        </text>
                    </g>
                </svg>
            </div>
        </div>
    </section>
</template>

<style scoped>
.map-ping {
    animation: gyc-map-ping 2.4s ease-out infinite;
    transform-origin: center;
}

@keyframes gyc-map-ping {
    0% {
        r: 3;
        opacity: 0.8;
    }
    100% {
        r: 11;
        opacity: 0;
    }
}
</style>
