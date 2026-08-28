<script setup>
import { Head } from '@inertiajs/vue3';
import DotGridBackground from '@/Components/DotGridBackground.vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import ResumePdfEmbed from '@/Components/ResumePdfEmbed.vue';

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

        <main class="mx-auto w-full max-w-3xl flex-1 px-6 pb-16 pt-8">
            <div v-if="!available" class="rounded-md border border-ink/15 bg-white px-8 py-16 text-center">
                <p class="text-sm text-ink/50">This resume is no longer available.</p>
            </div>

            <template v-else>
                <div class="mb-4 flex justify-end">
                    <a
                        :href="route('resumes.public-file', resume.slug)"
                        download
                        class="rounded-md border border-ink px-4 py-2 text-sm font-medium text-ink transition hover:bg-ink hover:text-paper"
                    >
                        Download PDF
                    </a>
                </div>

                <ResumePdfEmbed :src="route('resumes.public-file', resume.slug)" />
            </template>
        </main>

        <PublicFooter />
    </div>
</template>
