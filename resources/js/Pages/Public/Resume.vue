<script setup>
import { Head } from '@inertiajs/vue3';
import DotGridBackground from '@/Components/DotGridBackground.vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import PagedResumePreview from '@/Components/PagedResumePreview.vue';

const props = defineProps({
    available: {
        type: Boolean,
        required: true,
    },
    resume: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <Head :title="available ? resume.full_name || 'Resume' : 'Resume unavailable'" />

    <div class="isolate flex min-h-screen flex-col bg-paper text-ink">
        <DotGridBackground />

        <PublicHeader />

        <main class="mx-auto w-full max-w-3xl flex-1 px-6 py-16">
            <div v-if="!available" class="rounded-md border border-ink/15 bg-white px-8 py-16 text-center">
                <p class="text-sm text-ink/50">This resume is no longer available.</p>
            </div>

            <template v-else>
                <PagedResumePreview :resume="resume" max-width="3xl" />
            </template>
        </main>

        <PublicFooter />
    </div>
</template>
