<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TrashResumeCard from '@/Components/TrashResumeCard.vue';

defineProps({
    resumes: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Trash" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight text-paper">Trash</h1>
            <p class="mt-1 text-sm text-paper/60">
                {{ resumes.length }} {{ resumes.length === 1 ? 'resume' : 'resumes' }}
            </p>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p v-if="resumes.length === 0" class="text-center text-sm text-ink/50">
                    Trash is empty.
                </p>

                <div v-else class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <TrashResumeCard
                        v-for="resume in resumes"
                        :key="resume.id"
                        :resume="resume"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
