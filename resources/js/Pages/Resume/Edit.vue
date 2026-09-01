<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ResumePdfViewer from '@/Components/ResumePdfViewer.vue';
import ResumeForm from '@/Components/ResumeForm.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

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

function submit(event) {
    const status = event.submitter.value;

    form.transform((data) => ({
        ...data,
        title: data.full_name || 'Untitled resume',
        status,
    })).put(route('resumes.update', props.resume.id));
}

const previewPayload = computed(() => ({
    full_name: form.full_name,
    position: form.position,
    email: form.email,
    about: form.about,
    links: form.links,
    skill_groups: form.skill_groups,
    experiences: form.experiences,
}));
</script>

<template>
    <Head title="Edit resume" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-screen-2xl px-4 py-10 sm:px-6 lg:px-10">
            <div class="max-w-2xl">
                <Link :href="route('dashboard')" class="text-sm text-ink/50 hover:text-ink">
                    &larr; Dashboard
                </Link>
                <div class="mt-2 flex items-center gap-3">
                    <h1 class="text-2xl font-semibold tracking-tight">Edit resume</h1>
                    <StatusBadge :status="resume.archived ? 'archived' : resume.status" />
                </div>
                <p class="mt-1 text-sm text-ink/60">
                    Update the sections below - the preview on the right updates as you type.
                </p>
            </div>

            <div class="relative mt-10 grid gap-10 xl:grid-cols-2 xl:gap-12">
                <div
                    class="hidden xl:absolute xl:inset-y-0 xl:left-1/2 xl:block xl:w-px xl:-translate-x-1/2 xl:bg-ink/20"
                    aria-hidden="true"
                ></div>

                <form @submit.prevent="submit">
                    <ResumeForm :form="form" publish-label="Save changes" />
                </form>

                <div class="xl:sticky xl:top-10 xl:self-start xl:pl-10">
                    <ResumePdfViewer :payload="previewPayload" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
