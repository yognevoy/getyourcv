<script setup>
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';
import { randomResumeSlug } from '@/utils/randomResumeSlug.js';
import { ArrowDownTrayIcon, LinkIcon } from '@heroicons/vue/24/outline';

defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const exampleSlug = randomResumeSlug();
</script>

<template>
    <section class="relative isolate overflow-hidden px-6 py-14 text-center sm:py-20" :class="dark ? 'bg-ink text-paper' : ''">
        <DotPatchOverlay side="left" :dark="dark" />
        <div class="relative mx-auto max-w-3xl">
            <h2 class="text-[clamp(1.75rem,4vw,2.75rem)] font-semibold tracking-tight">
                What you see is exactly what HR downloads
            </h2>
            <p class="mx-auto mt-4 max-w-md" :class="dark ? 'text-paper/65' : 'text-ink/65'">
                Share it however fits the moment - a link that stays current, or a PDF that
                matches it exactly.
            </p>

            <div class="mt-10 flex flex-col items-center gap-10 sm:flex-row sm:justify-center sm:gap-16">
                <div class="flex flex-col items-center">
                    <LinkIcon class="h-8 w-8" :class="dark ? 'text-paper' : 'text-ink'" />
                    <p class="mt-3 text-sm font-medium" :class="dark ? 'text-paper/80' : 'text-ink/80'">Share a link</p>
                    <div class="mt-3 flex items-center gap-2 text-sm">
                        <span :class="dark ? 'text-paper/60' : 'text-ink/60'">getyourcv.app/r/{{ exampleSlug }}</span>
                    </div>
                </div>

                <div class="hidden text-sm sm:block" :class="dark ? 'text-paper/30' : 'text-ink/30'">or</div>

                <div class="flex flex-col items-center">
                    <ArrowDownTrayIcon class="h-8 w-8" :class="dark ? 'text-paper' : 'text-ink'" />
                    <p class="mt-3 text-sm font-medium" :class="dark ? 'text-paper/80' : 'text-ink/80'">Download a PDF</p>
                    <p class="mt-3 text-sm" :class="dark ? 'text-paper/60' : 'text-ink/60'">Exact same layout, ready to send</p>
                </div>
            </div>
        </div>
    </section>
</template>
