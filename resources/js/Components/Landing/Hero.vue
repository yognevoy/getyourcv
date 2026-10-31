<script setup>
import { Link } from '@inertiajs/vue3';
import DotFieldCanvas from '@/Components/Landing/DotFieldCanvas.vue';
import SkillWordFeed from '@/Components/Landing/SkillWordFeed.vue';
import HeroStatsLine from '@/Components/Landing/HeroStatsLine.vue';

const HEADING_LINES = ['Your resume, shipped.', 'Ready to go.'];
</script>

<template>
    <section class="relative isolate flex min-h-[70vh] items-center overflow-hidden px-6 py-12">
        <DotFieldCanvas />

        <div class="relative mx-auto grid max-w-6xl items-center gap-16 lg:grid-cols-2 lg:gap-48">
            <div class="relative text-left">
                <h1 class="text-[clamp(1.75rem,7vw,4.25rem)] font-semibold leading-[1.02] tracking-tighter">
                    <span v-for="(line, i) in HEADING_LINES" :key="i" class="heading-line">
                        <span class="heading-base">{{ line }}</span>
                        <span :class="['heading-overlay', `heading-overlay--line-${i + 1}`]" aria-hidden="true">{{
                            line
                        }}</span>
                    </span>
                </h1>
                <p class="mt-6 max-w-md text-lg text-ink/70">
                    Fill it in once and share a link with any HR, anywhere.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <Link
                        :href="route('resumes.create')"
                        class="rounded-md border border-ink bg-ink px-9 py-4 text-base font-medium text-paper transition-colors hover:bg-paper hover:text-ink"
                    >
                        Get your CV
                    </Link>
                </div>

                <HeroStatsLine />
            </div>

            <div class="relative hidden lg:block">
                <SkillWordFeed />
            </div>
        </div>
    </section>
</template>

<style scoped>
.heading-line {
    position: relative;
    display: block;
    width: fit-content;
    white-space: nowrap;
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
