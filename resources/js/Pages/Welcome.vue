<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';

const hero = ref(null);
const canvas = ref(null);

const HEADING_LINES = ['Your resume, shipped.', 'Ready to go.'];

const WHY_CHOOSE_US_ITEMS = [
    'Build your resume in 5 minutes.',
    'Only one template - so you never waste time picking one.',
    'Sharpen your wording with AI.',
    "We kept it simple, so extra features don't get in your way.",
    'Export to PDF or share a link, always free.',
];

const whyChooseUsOpen = ref(false);

const SPACING = 32;
const DOT_RADIUS = 1.5;
const REPEL_RADIUS = 110;
const MAX_DISPLACEMENT = 22;
const EASE = 0.12;
const SETTLE_EPSILON = 0.05;

let ctx = null;
let dots = [];
let width = 0;
let height = 0;
let dpr = 1;
let mouseX = -9999;
let mouseY = -9999;
let mouseActive = false;
let rafId = null;
let loopRunning = false;
let resizeObserver = null;

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

function buildDots() {
    dots = [];

    const cols = Math.ceil(width / SPACING) + 1;
    const rows = Math.ceil(height / SPACING) + 1;
    const cx = width / 2;
    const cy = height * 0.45;
    const rx = width * 0.55;
    const ry = height * 0.55;

    for (let i = 0; i < cols; i++) {
        for (let j = 0; j < rows; j++) {
            const x = i * SPACING;
            const y = j * SPACING;
            const nx = rx ? (x - cx) / rx : 0;
            const ny = ry ? (y - cy) / ry : 0;
            const dist = Math.sqrt(nx * nx + ny * ny);
            const alpha = Math.max(0, 1 - dist) * 0.45;

            if (alpha <= 0.01) continue;

            dots.push({ hx: x, hy: y, x, y, alpha });
        }
    }
}

function resize() {
    if (!hero.value || !canvas.value) return;

    const rect = hero.value.getBoundingClientRect();
    width = rect.width;
    height = rect.height;
    dpr = window.devicePixelRatio || 1;

    canvas.value.width = width * dpr;
    canvas.value.height = height * dpr;
    canvas.value.style.width = `${width}px`;
    canvas.value.style.height = `${height}px`;

    ctx = canvas.value.getContext('2d');
    ctx.scale(dpr, dpr);

    buildDots();
}

// Draws one frame and eases dots toward their (possibly cursor-displaced) target.
// Returns whether anything is still moving, so the caller can stop the rAF loop when settled.
function renderFrame() {
    if (!ctx) return false;

    ctx.clearRect(0, 0, width, height);

    let moving = false;

    for (const dot of dots) {
        let tx = dot.hx;
        let ty = dot.hy;

        if (mouseActive) {
            const dx = dot.hx - mouseX;
            const dy = dot.hy - mouseY;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < REPEL_RADIUS) {
                const force = (1 - dist / REPEL_RADIUS) * MAX_DISPLACEMENT;
                const angle = Math.atan2(dy, dx);
                tx = dot.hx + Math.cos(angle) * force;
                ty = dot.hy + Math.sin(angle) * force;
            }
        }

        const dx2 = tx - dot.x;
        const dy2 = ty - dot.y;

        if (Math.abs(dx2) > SETTLE_EPSILON || Math.abs(dy2) > SETTLE_EPSILON) {
            dot.x += dx2 * EASE;
            dot.y += dy2 * EASE;
            moving = true;
        }

        ctx.beginPath();
        ctx.arc(dot.x, dot.y, DOT_RADIUS, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(10, 10, 10, ${dot.alpha})`;
        ctx.fill();
    }

    return moving;
}

function loop() {
    const moving = renderFrame();

    if (moving || mouseActive) {
        rafId = requestAnimationFrame(loop);
    } else {
        loopRunning = false;
        rafId = null;
    }
}

function ensureLoop() {
    if (!loopRunning) {
        loopRunning = true;
        loop();
    }
}

function handleMouseMove(e) {
    const rect = hero.value.getBoundingClientRect();
    mouseX = e.clientX - rect.left;
    mouseY = e.clientY - rect.top;
    mouseActive = true;
    ensureLoop();
}

function handleMouseLeave() {
    mouseActive = false;
    ensureLoop();
}

function handleResize() {
    resize();
    renderFrame();
}

onMounted(() => {
    if (!hero.value || !canvas.value) return;

    resize();
    renderFrame();

    resizeObserver = new ResizeObserver(handleResize);
    resizeObserver.observe(hero.value);

    if (prefersReducedMotion) return;

    hero.value.addEventListener('mousemove', handleMouseMove);
    hero.value.addEventListener('mouseleave', handleMouseLeave);
});

onUnmounted(() => {
    if (rafId !== null) cancelAnimationFrame(rafId);
    resizeObserver?.disconnect();

    if (!hero.value) return;

    hero.value.removeEventListener('mousemove', handleMouseMove);
    hero.value.removeEventListener('mouseleave', handleMouseLeave);
});

</script>

<template>
    <Head title="Build your resume">
        <meta
            name="description"
            content="A resume builder to create and share your CV without wasting time."
        />
        <meta
            name="keywords"
            content="resume builder, CV builder, online resume, resume link, AI resume, create resume, professional resume"
        />
    </Head>

    <div class="flex min-h-screen flex-col bg-paper text-ink">
        <PublicHeader />

        <main
            ref="hero"
            class="relative flex flex-1 flex-col items-center justify-center overflow-hidden px-6 text-center"
        >
            <canvas ref="canvas" class="pointer-events-none absolute inset-0" aria-hidden="true"></canvas>

            <h1 class="relative max-w-3xl text-[clamp(1.75rem,8.5vw,3rem)] font-semibold tracking-tighter sm:text-6xl md:text-7xl">
                <span v-for="(line, i) in HEADING_LINES" :key="i" class="heading-line">
                    <span class="heading-base">{{ line }}</span>
                    <span :class="['heading-overlay', `heading-overlay--line-${i + 1}`]" aria-hidden="true">{{
                        line
                    }}</span>
                </span>
            </h1>
            <p class="relative mt-5 max-w-xl text-base text-ink/70">
                Fill it in once and share a link with any HR, anywhere.
            </p>

            <Link
                :href="route('resumes.create')"
                class="relative mt-10 rounded-md border border-ink bg-ink px-8 py-3 text-sm font-medium text-paper transition-colors hover:bg-paper hover:text-ink"
            >
                Get your CV
            </Link>
        </main>

        <div class="flex flex-col items-center px-6 pb-2">
            <button
                type="button"
                @click="whyChooseUsOpen = !whyChooseUsOpen"
                class="text-sm font-light text-ink/50 hover:text-ink"
            >
                Why choose us?
            </button>
        </div>

        <div class="overflow-hidden bg-charcoal text-paper transition-[height] duration-300 ease-in-out" :style="{ height: whyChooseUsOpen ? '4rem' : '0px' }">
            <div class="fade-edges flex h-16 items-center justify-start gap-3 overflow-x-auto whitespace-nowrap px-6 text-sm sm:justify-center">
                <template v-for="(item, i) in WHY_CHOOSE_US_ITEMS" :key="item">
                    <span>{{ item }}</span>
                    <span v-if="i < WHY_CHOOSE_US_ITEMS.length - 1" class="text-paper/40">/</span>
                </template>
            </div>
        </div>

        <PublicFooter />
    </div>
</template>

<style scoped>
.fade-edges {
    mask-image: linear-gradient(to right, transparent, black 24px, black calc(100% - 24px), transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 24px, black calc(100% - 24px), transparent);
}

.heading-line {
    position: relative;
    display: block;
    width: fit-content;
    margin-inline: auto;
}

.heading-overlay {
    position: absolute;
    inset: 0;
    color: #fafafa;
    background-color: #0a0a0a;
    clip-path: inset(0 100% 0 0);
    opacity: 0;
}

.heading-overlay--line-1 {
    animation: gyc-select-sweep-line1 1.5s ease-in-out forwards;
}

.heading-overlay--line-2 {
    animation: gyc-select-sweep-line2 1.5s ease-in-out forwards;
}

@keyframes gyc-select-sweep-line1 {
    0% {
        clip-path: inset(0 100% 0 0);
        opacity: 1;
    }
    30% {
        clip-path: inset(0 0% 0 0);
        opacity: 1;
    }
    70% {
        clip-path: inset(0 0% 0 0);
        opacity: 1;
    }
    100% {
        clip-path: inset(0 0% 0 0);
        opacity: 0;
    }
}

@keyframes gyc-select-sweep-line2 {
    0% {
        clip-path: inset(0 100% 0 0);
        opacity: 0;
    }
    30% {
        clip-path: inset(0 100% 0 0);
        opacity: 1;
    }
    55% {
        clip-path: inset(0 0% 0 0);
        opacity: 1;
    }
    70% {
        clip-path: inset(0 0% 0 0);
        opacity: 1;
    }
    100% {
        clip-path: inset(0 0% 0 0);
        opacity: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    .heading-overlay,
    .heading-overlay--line-1,
    .heading-overlay--line-2 {
        animation: none;
        clip-path: inset(0 100% 0 0);
        opacity: 0;
    }
}
</style>
