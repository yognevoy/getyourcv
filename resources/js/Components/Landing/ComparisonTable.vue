<script setup>
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';

defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const ROWS = [
    { label: 'Templates to pick from', typical: '20+, decide yourself', us: '1, already decided' },
    { label: 'PDF that matches the preview', typical: 'Often drifts', us: 'Pixel-identical' },
    { label: 'Version history', typical: 'No history, no undo', us: 'Auto-saved, restore anytime' },
    { label: 'AI rewrite', typical: 'Generic tone presets', us: 'Rewrites against your actual role' },
    { label: 'Sharing with HR', typical: 'Attach a file', us: 'Send a link, always current' },
];
</script>

<template>
    <section class="relative isolate overflow-hidden px-6 py-14 sm:py-20" :class="dark ? 'bg-ink text-paper' : ''">
        <DotPatchOverlay side="right" :dark="dark" />
        <div class="relative mx-auto max-w-4xl">
            <h2 class="max-w-xl text-[clamp(1.75rem,4vw,2.75rem)] font-semibold tracking-tight">
                Why not just use a typical resume builder?
            </h2>

            <div class="mt-10 overflow-hidden rounded-lg border" :class="dark ? 'border-paper/30' : 'border-ink'">
                <div
                    class="grid grid-cols-[1.2fr,1fr,1fr] text-sm font-medium"
                    :class="dark ? 'border-b border-paper/15 bg-paper/10 text-paper' : 'border-b border-ink/15 bg-charcoal text-paper'"
                >
                    <div class="px-5 py-3"></div>
                    <div class="px-5 py-3">Typical builder</div>
                    <div class="px-5 py-3">getyourcv</div>
                </div>
                <div
                    v-for="(row, i) in ROWS"
                    :key="row.label"
                    class="grid grid-cols-[1.2fr,1fr,1fr] text-sm"
                    :class="i < ROWS.length - 1 ? (dark ? 'border-b border-paper/10' : 'border-b border-ink/10') : ''"
                >
                    <div class="px-5 py-4 font-medium">{{ row.label }}</div>
                    <div class="px-5 py-4" :class="dark ? 'text-paper/50' : 'text-ink/50'">{{ row.typical }}</div>
                    <div class="px-5 py-4 font-medium">{{ row.us }}</div>
                </div>
            </div>
        </div>
    </section>
</template>
