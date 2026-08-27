<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import PagedResumePreview from '@/Components/PagedResumePreview.vue';
import ResumeForm from '@/Components/ResumeForm.vue';

const page = usePage();
const DRAFT_KEY = 'getyourcv_draft_resume';

const form = useForm({
    full_name: '',
    position: '',
    email: '',
    about: '',
    links: [],
    skill_groups: [],
    experiences: [],
});

onMounted(() => {
    if (!page.props.auth.user) {
        return;
    }

    const raw = sessionStorage.getItem(DRAFT_KEY);
    if (!raw) {
        return;
    }

    sessionStorage.removeItem(DRAFT_KEY);

    try {
        Object.assign(form, JSON.parse(raw));
    } catch {
        // malformed draft, nothing to restore
    }
});

function draftData() {
    return {
        full_name: form.full_name,
        position: form.position,
        email: form.email,
        about: form.about,
        links: form.links,
        skill_groups: form.skill_groups,
        experiences: form.experiences,
    };
}

function submit() {
    if (!page.props.auth.user) {
        sessionStorage.setItem(DRAFT_KEY, JSON.stringify(draftData()));
        router.visit(route('register', { redirect: route('resumes.create', {}, false) }));
        return;
    }

    form.transform((data) => ({
        ...data,
        title: data.full_name || 'Untitled resume',
    })).post(route('resumes.store'));
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

            <div class="relative mt-10 grid gap-10 xl:grid-cols-2 xl:gap-12">
                <div
                    class="hidden xl:absolute xl:inset-y-0 xl:left-1/2 xl:block xl:w-px xl:-translate-x-1/2 xl:bg-ink/20"
                    aria-hidden="true"
                ></div>

                <form @submit.prevent="submit">
                    <ResumeForm :form="form" submit-label="Save resume" />
                </form>

                <div class="xl:sticky xl:top-10 xl:self-start xl:pl-10">
                    <PagedResumePreview :resume="form" />
                </div>
            </div>
        </main>
    </div>
</template>
