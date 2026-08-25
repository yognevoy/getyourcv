<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ResumeForm from '@/Components/ResumeForm.vue';
import ResumeTemplate from '@/Shared/ResumeTemplate.vue';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    full_name: props.resume.full_name || '',
    position: props.resume.position || '',
    email: props.resume.email || '',
    about: props.resume.about || '',
    links: props.resume.links,
    skill_groups: props.resume.skill_groups,
    experiences: props.resume.experiences,
});

function submit() {
    form.transform((data) => ({
        ...data,
        title: data.full_name || 'Untitled resume',
    })).put(route('resumes.update', props.resume.id));
}
</script>

<template>
    <Head title="Edit resume" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <Link :href="route('dashboard')" class="text-sm text-black/50 hover:text-black">
                    &larr; Dashboard
                </Link>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">Edit resume</h1>
                <p class="mt-1 text-sm text-black/60">
                    Update the sections below - the preview on the right updates as you type.
                </p>
            </div>

            <div class="mt-10 grid gap-10 lg:grid-cols-2">
                <form class="space-y-12" @submit.prevent="submit">
                    <ResumeForm :form="form" />

                    <PrimaryButton type="submit" :disabled="form.processing" class="w-full sm:w-auto">
                        Save changes
                    </PrimaryButton>
                </form>

                <div class="lg:sticky lg:top-10 lg:self-start">
                    <div class="mb-3 flex items-center gap-2 text-xs uppercase tracking-wide text-black/40">
                        <span class="h-1.5 w-1.5 rounded-full bg-black"></span>
                        Live preview
                    </div>
                    <ResumeTemplate :resume="form" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
