<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';

const VERSIONS = [
    { ref: 'base', label: 'Base resume', link: '/r/john-doe-resume-k7m2qx', detail: 'Your full experience, nothing left out', isBase: true },
    { ref: 'frontend', label: 'Frontend role', link: '/r/john-doe-frontend-h8n3pz', detail: 'Leads with React, UI work, design systems' },
    { ref: 'lead', label: 'Team lead role', link: '/r/john-doe-lead-m4j9qw', detail: 'Leads with mentoring, planning, delivery' },
    { ref: 'freelance', label: 'Freelance pitch', link: '/r/john-doe-freelance-t2x8rc', detail: 'Leads with client results, turnaround time' },
];

const el = ref(null);
const visible = ref(false);
let observer = null;

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

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
    <section class="relative isolate overflow-hidden px-6 py-14 sm:py-20">
        <DotPatchOverlay side="left" />
        <div class="relative mx-auto max-w-4xl">
            <h2 class="max-w-xl text-[clamp(1.75rem,4vw,2.75rem)] font-semibold tracking-tight">
                One resume, reshaped for every application
            </h2>
            <p class="mt-4 max-w-md text-ink/65">
                Branch off a version per job. Each keeps its own link - edit one without
                touching the others.
            </p>

            <div ref="el" class="relative mt-12 max-w-xl pl-7">
                <div class="absolute bottom-1.5 left-[5px] top-1.5 w-px bg-ink/15"></div>

                <div
                    v-for="(version, i) in VERSIONS"
                    :key="version.ref"
                    class="relative border-b border-ink/8 py-4 transition-all duration-500 last:border-b-0"
                    :class="visible ? 'translate-y-0 opacity-100' : '-translate-y-3 opacity-0'"
                    :style="{ transitionDelay: `${i * 120}ms` }"
                >
                    <span
                        class="absolute -left-7 top-5 h-[11px] w-[11px] rounded-full border-[1.5px] border-ink"
                        :class="version.isBase ? 'bg-ink' : 'bg-white'"
                    ></span>

                    <div class="flex flex-wrap items-baseline gap-2.5">
                        <span class="text-base font-semibold tracking-tight">{{ version.label }}</span>
                        <span class="ml-auto font-mono text-xs text-ink/40">{{ version.link }}</span>
                    </div>
                    <p class="mt-1.5 text-sm text-ink/65">{{ version.detail }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
