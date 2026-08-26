<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import ResumeTemplate from '@/Shared/ResumeTemplate.vue';

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

    <div class="flex min-h-screen flex-col bg-white text-black">
        <PublicHeader />

        <main class="mx-auto w-full max-w-2xl flex-1 px-6 py-16">
            <p v-if="!available" class="text-center text-sm text-black/50">
                This resume is no longer available.
            </p>

            <template v-else>
                <ResumeTemplate :resume="resume" />
            </template>
        </main>

        <PublicFooter />
    </div>
</template>
