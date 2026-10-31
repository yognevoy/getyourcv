<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    color: {
        type: String,
        default: 'ink',
        validator: (value) => ['ink', 'paper'].includes(value),
    },
});

const canvas = ref(null);
const dotColor = props.color === 'paper' ? '250, 250, 250' : '10, 10, 10';

const SPACING = 32;
const DOT_RADIUS = 1.5;
const BASE_ALPHA = 0.14;
const ALPHA_VARIANCE = 0.08;
const FADE_MS = 500;
const STAGGER_SPAN_MS = 900;

let container = null;
let ctx = null;
let dots = [];
let width = 0;
let height = 0;
let dpr = 1;
let resizeObserver = null;
let visibilityObserver = null;
let rafId = null;
let animStart = null;
let played = false;

const prefersReducedMotion =
    typeof window !== 'undefined' && window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

function buildDots() {
    dots = [];

    const cols = Math.ceil(width / SPACING) + 1;
    const rows = Math.ceil(height / SPACING) + 1;

    for (let i = 0; i < cols; i++) {
        for (let j = 0; j < rows; j++) {
            const alpha = BASE_ALPHA + (Math.random() - 0.5) * ALPHA_VARIANCE;

            dots.push({ x: i * SPACING, y: j * SPACING, alpha });
        }
    }
}

function resize() {
    if (!container || !canvas.value) return;

    width = container.scrollWidth;
    height = container.scrollHeight;
    dpr = window.devicePixelRatio || 1;

    canvas.value.width = width * dpr;
    canvas.value.height = height * dpr;
    canvas.value.style.width = `${width}px`;
    canvas.value.style.height = `${height}px`;

    ctx = canvas.value.getContext('2d');
    ctx.scale(dpr, dpr);

    buildDots();
}

function renderFrame() {
    if (!ctx) return;

    ctx.clearRect(0, 0, width, height);

    for (const dot of dots) {
        ctx.beginPath();
        ctx.arc(dot.x, dot.y, DOT_RADIUS, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(${dotColor}, ${dot.alpha})`;
        ctx.fill();
    }
}

function tick(timestamp) {
    if (!ctx) return;
    if (animStart === null) animStart = timestamp;
    const elapsed = timestamp - animStart;

    ctx.clearRect(0, 0, width, height);
    let allDone = true;

    for (const dot of dots) {
        const delay = (dot.x / Math.max(width, 1)) * STAGGER_SPAN_MS;
        const progress = Math.min(1, Math.max(0, (elapsed - delay) / FADE_MS));
        if (progress < 1) allDone = false;
        if (progress <= 0) continue;

        ctx.beginPath();
        ctx.arc(dot.x, dot.y, DOT_RADIUS, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(${dotColor}, ${dot.alpha * progress})`;
        ctx.fill();
    }

    rafId = allDone ? null : requestAnimationFrame(tick);
}

function playReveal() {
    played = true;
    cancelAnimationFrame(rafId);
    animStart = null;

    if (prefersReducedMotion) {
        renderFrame();
        return;
    }

    rafId = requestAnimationFrame(tick);
}

function handleResize() {
    resize();
    if (played) renderFrame();
}

onMounted(() => {
    container = canvas.value?.parentElement;
    if (!container || !canvas.value) return;

    resize();

    resizeObserver = new ResizeObserver(handleResize);
    resizeObserver.observe(container);

    if (prefersReducedMotion) {
        playReveal();
        return;
    }

    visibilityObserver = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                playReveal();
                visibilityObserver.disconnect();
            }
        },
        { threshold: 0.2 },
    );
    visibilityObserver.observe(container);
});

onUnmounted(() => {
    resizeObserver?.disconnect();
    visibilityObserver?.disconnect();
    cancelAnimationFrame(rafId);
});
</script>

<template>
    <canvas ref="canvas" class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true"></canvas>
</template>
