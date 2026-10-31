<script setup>
import { ref } from 'vue';
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';
import { randomResumeSlug } from '@/utils/randomResumeSlug.js';

defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const exampleSlug = randomResumeSlug();
const copied = ref(false);
let resetTimer = null;

function copyLink() {
    const text = `https://getyourcv.app/r/${exampleSlug}`;

    if (navigator.clipboard?.writeText) {
        navigator.clipboard.writeText(text).catch(() => {});
    }

    copied.value = true;
    clearTimeout(resetTimer);
    resetTimer = setTimeout(() => {
        copied.value = false;
    }, 1600);
}
</script>

<template>
    <section class="relative isolate overflow-hidden px-6 py-14 text-center sm:py-20" :class="dark ? 'bg-ink text-paper' : ''">
        <DotPatchOverlay side="right" :dark="dark" />
        <div class="relative mx-auto max-w-md">
            <h2 class="text-[clamp(1.75rem,4vw,2.75rem)] font-semibold tracking-tight">
                One link does the whole job
            </h2>
            <p class="mx-auto mt-4 max-w-md" :class="dark ? 'text-paper/65' : 'text-ink/65'">
                Send it once. Keep editing - the same link always shows your latest resume.
            </p>

            <div
                class="mt-10 flex items-center gap-2 rounded-md border px-4 py-3 text-left text-sm"
                :class="dark ? 'border-paper/30' : 'border-ink'"
            >
                <span class="truncate" :class="dark ? 'text-paper/80' : 'text-ink/80'">getyourcv.app/r/{{ exampleSlug }}</span>
                <button
                    type="button"
                    @click="copyLink"
                    class="ml-auto flex-none rounded-md border px-3 py-1.5 text-xs font-medium transition-colors"
                    :class="dark ? 'border-paper bg-paper text-ink hover:bg-ink hover:text-paper' : 'border-ink bg-ink text-paper hover:bg-paper hover:text-ink'"
                >
                    {{ copied ? 'Copied!' : 'Copy link' }}
                </button>
            </div>
            <p class="mt-3 text-xs" :class="dark ? 'text-paper/40' : 'text-ink/40'">
                No re-sending a new file every time you make an edit.
            </p>
        </div>
    </section>
</template>
