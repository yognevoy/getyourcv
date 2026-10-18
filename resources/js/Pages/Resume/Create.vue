<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, watch } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import ResumePdfViewer from '@/Components/ResumePdfViewer.vue';
import ResumeForm from '@/Components/ResumeForm.vue';
import { DRAFT_KEY, PENDING_SAVE_KEY } from '@/composables/usePendingDraft';

const page = usePage();

const isAuthorized = computed(() => !!page.props.auth.user);

const form = useForm({
    full_name: '',
    position: '',
    location: '',
    email: '',
    about: '',
    photo_path: '',
    photo_preview_url: '',
    links: [],
    skill_groups: [],
    experiences: [],
    projects: [],
    educations: [],
    courses: [],
    certifications: [],
    languages: [],
});

onMounted(() => {
    const raw = sessionStorage.getItem(DRAFT_KEY);
    if (!raw) {
        return;
    }

    try {
        Object.assign(form, JSON.parse(raw));
    } catch {
        return;
    }
});

function draftData() {
    return {
        full_name: form.full_name,
        position: form.position,
        location: form.location,
        email: form.email,
        about: form.about,
        photo_path: form.photo_path,
        links: form.links,
        skill_groups: form.skill_groups,
        experiences: form.experiences,
        projects: form.projects,
        educations: form.educations,
        courses: form.courses,
        certifications: form.certifications,
        languages: form.languages,
    };
}

const previewPayload = computed(() => draftData());

watch(
    previewPayload,
    (data) => {
        const draft = { ...data, photo_preview_url: form.photo_preview_url };
        sessionStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
    },
    { deep: true },
);

function submit(event) {
    const status = event.submitter.value;

    if (!isAuthorized.value) {
        sessionStorage.setItem(PENDING_SAVE_KEY, status);
        router.visit(route('register'));
        return;
    }

    form.transform(({ photo_preview_url, ...data }) => ({
        ...data,
        title: data.full_name || 'Untitled resume',
        status,
    })).post(route('resumes.store'), {
        onSuccess: () => sessionStorage.removeItem(DRAFT_KEY),
    });
}
</script>

<template>
    <Head title="Create your resume" />

    <div class="min-h-screen bg-paper text-ink">
        <PublicHeader />

        <main class="mx-auto max-w-screen-2xl px-6 py-10 lg:px-10">
            <div class="max-w-2xl">
                <h1 class="text-2xl font-semibold tracking-tight">Build your resume</h1>
                <p class="mt-1 text-sm text-ink/60">
                    Fill in the sections below - the preview on the right updates as you type.
                    We'll only ask you to sign in when you hit Save.
                </p>
            </div>

            <div class="relative mt-10 grid grid-cols-1 gap-10 xl:grid-cols-2 xl:gap-12">
                <div
                    class="hidden xl:absolute xl:inset-y-0 xl:left-1/2 xl:block xl:w-px xl:-translate-x-1/2 xl:bg-ink/20"
                    aria-hidden="true"
                ></div>

                <form @submit.prevent="submit">
                    <ResumeForm :form="form" publish-label="Save resume" />
                </form>

                <div class="xl:sticky xl:top-10 xl:self-start xl:pl-10">
                    <ResumePdfViewer :payload="previewPayload" />
                </div>
            </div>
        </main>
    </div>
</template>
