<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ResumeCard from '@/Components/ResumeCard.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    resumes: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <Link
                        :href="route('resumes.create')"
                        class="flex aspect-[3/4] flex-col items-center justify-center gap-2 border border-dashed border-black/30 text-black/50 transition-colors hover:border-black hover:text-black"
                    >
                        <span class="text-3xl leading-none">+</span>
                        <span class="text-sm font-medium">New resume</span>
                    </Link>

                    <ResumeCard
                        v-for="resume in resumes"
                        :key="resume.id"
                        :resume="resume"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
