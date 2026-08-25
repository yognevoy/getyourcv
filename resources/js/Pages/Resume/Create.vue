<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ResumeForm from '@/Components/ResumeForm.vue';
import ResumeTemplate from '@/Shared/ResumeTemplate.vue';

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

    <div class="min-h-screen bg-white text-black">
        <PublicHeader />

        <main class="mx-auto max-w-6xl px-6 py-10">
            <div class="max-w-2xl">
                <h1 class="text-2xl font-semibold tracking-tight">Build your resume</h1>
                <p class="mt-1 text-sm text-black/60">
                    Fill in the sections below - the preview on the right updates as you type.
                    We'll only ask you to sign in when you hit Save.
                </p>
            </div>

            <div class="mt-10 grid gap-10 lg:grid-cols-2">
                <form class="space-y-12" @submit.prevent="submit">
                    <ResumeForm :form="form" />

                    <PrimaryButton type="submit" :disabled="form.processing" class="w-full sm:w-auto">
                        Save resume
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
        </main>
    </div>
</template>
