<script setup>
import { ref } from 'vue';
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';

defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const QUESTIONS = [
    {
        q: 'Is it actually free?',
        a: 'Yes. Building, publishing, versioning and downloading your resume as a PDF cost nothing.',
    },
    {
        q: 'Can I keep more than one resume in my account?',
        a: "Yes. Your dashboard holds all of them as separate cards - build as many as you need, side by side.",
    },
    {
        q: 'What happens if I edit my resume after sending the link?',
        a: 'The link always shows your latest version. If you want HR to keep seeing a frozen version, save it as its own version first.',
    },
    {
        q: 'Who can see my resume link?',
        a: "Only people you send it to. It's not listed or indexed anywhere - no account browsing, no public directory.",
    },
    {
        q: 'What happens if I delete a resume?',
        a: 'It goes to the trash first, not gone immediately. You can restore it, or delete it for good whenever you choose.',
    },
    {
        q: 'Can I delete my account and everything in it?',
        a: 'Yes. Deleting your account permanently erases every resume you have, including anything still in the trash, along with the account itself.',
    },
];

const openIndex = ref(null);

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

function toggle(index) {
    openIndex.value = openIndex.value === index ? null : index;
}

function onBeforeEnter(el) {
    el.style.maxHeight = '0px';
}

function onEnter(el) {
    if (prefersReducedMotion) {
        el.style.maxHeight = '';
        return;
    }
    el.style.maxHeight = `${el.scrollHeight}px`;
}

function onBeforeLeave(el) {
    el.style.maxHeight = `${el.scrollHeight}px`;
}

function onLeave(el) {
    if (prefersReducedMotion) {
        el.style.maxHeight = '0px';
        return;
    }
    // force reflow so the browser registers the starting height before collapsing
    el.offsetHeight;
    el.style.maxHeight = '0px';
}
</script>

<template>
    <section class="relative isolate overflow-hidden px-6 py-14 sm:py-20" :class="dark ? 'bg-ink text-paper' : ''">
        <DotPatchOverlay side="right" :dark="dark" />
        <div class="relative mx-auto max-w-3xl">
            <h2 class="text-[clamp(1.75rem,4vw,2.75rem)] font-semibold tracking-tight">
                Questions people actually ask
            </h2>

            <div
                class="mt-10 divide-y border-t border-b"
                :class="dark ? 'divide-paper/15 border-paper/15' : 'divide-ink/15 border-ink/15'"
            >
                <div v-for="(item, i) in QUESTIONS" :key="item.q">
                    <button
                        type="button"
                        @click="toggle(i)"
                        class="flex w-full items-center justify-between gap-4 py-5 text-left"
                    >
                        <span class="text-base font-medium">{{ item.q }}</span>
                        <span
                            class="flex-none text-xl transition-transform duration-200"
                            :class="dark ? 'text-paper/40' : 'text-ink/40'"
                            :style="{ transform: openIndex === i ? 'rotate(135deg)' : 'rotate(0deg)' }"
                        >
                            +
                        </span>
                    </button>
                    <Transition
                        @before-enter="onBeforeEnter"
                        @enter="onEnter"
                        @before-leave="onBeforeLeave"
                        @leave="onLeave"
                    >
                        <p
                            v-if="openIndex === i"
                            class="overflow-hidden pr-8 text-sm transition-[max-height,opacity] duration-200 ease-out"
                            :class="dark ? 'text-paper/65' : 'text-ink/65'"
                        >
                            <span class="block pb-5">{{ item.a }}</span>
                        </p>
                    </Transition>
                </div>
            </div>
        </div>
    </section>
</template>
