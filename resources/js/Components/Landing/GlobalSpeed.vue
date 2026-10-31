<script setup>
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';

defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const REGIONS = [
    { place: 'New York', ms: 48, x: 70, y: 75 },
    { place: 'London', ms: 52, x: 200, y: 50 },
    { place: 'Singapore', ms: 61, x: 325, y: 95 },
    { place: 'São Paulo', ms: 57, x: 115, y: 155 },
];

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

const MAP_W = 400;
const MAP_H = 200;
const DOT_SPACING = 7;

const LANDMASSES = [
    [[35, 40], [50, 20], [80, 15], [115, 18], [140, 30], [150, 45], [140, 60], [125, 55], [110, 65], [100, 80], [90, 100], [75, 115], [60, 110], [45, 95], [35, 75], [28, 55]],
    [[95, 125], [120, 120], [140, 130], [148, 150], [140, 175], [125, 195], [110, 190], [100, 170], [92, 150]],
    [[180, 35], [200, 20], [220, 25], [230, 40], [222, 55], [205, 65], [190, 60], [178, 50]],
    [[195, 65], [225, 60], [245, 75], [248, 110], [240, 140], [225, 165], [210, 160], [200, 130], [195, 100]],
    [[230, 20], [270, 12], [315, 15], [350, 25], [368, 45], [360, 65], [340, 60], [325, 75], [332, 95], [320, 115], [305, 110], [290, 95], [270, 85], [250, 70], [235, 50], [228, 35]],
    [[335, 145], [360, 140], [378, 155], [375, 175], [355, 185], [335, 178], [328, 160]],
];

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
    return LANDMASSES.some((polygon) => inPolygon(x, y, polygon));
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

            <div class="mt-10">
                <svg :viewBox="`0 0 ${MAP_W} ${MAP_H}`" class="mx-auto w-full max-w-xl" role="img" aria-label="World map with resume link load times by region">
                    <g v-for="([a, b], i) in CONNECTIONS" :key="`conn-${i}`">
                        <path
                            :id="`gyc-map-conn-${i}`"
                            :d="`M ${REGIONS[a].x} ${REGIONS[a].y} L ${REGIONS[b].x} ${REGIONS[b].y}`"
                            fill="none"
                            :stroke="dark ? '#fdfdfd' : '#0a0a0a'"
                            stroke-width="0.75"
                            stroke-opacity="0.15"
                        />
                        <circle v-if="!prefersReducedMotion" r="1.8" :fill="dark ? '#fdfdfd' : '#0a0a0a'" fill-opacity="0.85">
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
                        r="1.4"
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
                        <text
                            :x="region.x"
                            :y="region.y - 8"
                            text-anchor="middle"
                            font-size="8"
                            :fill="dark ? '#fdfdfd' : '#0a0a0a'"
                        >
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
