<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PublicHeader from '@/Components/PublicHeader.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionHeader from '@/Components/SectionHeader.vue';
import IconButton from '@/Components/IconButton.vue';
import RemoveIcon from '@/Components/RemoveIcon.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
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

function addLink() {
    form.links.push({ label: '', url: '' });
}

function removeLink(index) {
    form.links.splice(index, 1);
}

function addSkillGroup() {
    form.skill_groups.push({ label: '', skills: [] });
}

function removeSkillGroup(index) {
    form.skill_groups.splice(index, 1);
}

function addSkill(groupIndex) {
    form.skill_groups[groupIndex].skills.push({ value: '' });
}

function removeSkill(groupIndex, skillIndex) {
    form.skill_groups[groupIndex].skills.splice(skillIndex, 1);
}

function addExperience() {
    form.experiences.push({
        company: '',
        title: '',
        period_from: '',
        period_to: '',
        is_current: false,
        bullets: [],
    });
}

function removeExperience(index) {
    form.experiences.splice(index, 1);
}

function addBullet(experienceIndex, type) {
    form.experiences[experienceIndex].bullets.push({ type, text: '' });
}

function removeBullet(experienceIndex, bulletIndex) {
    form.experiences[experienceIndex].bullets.splice(bulletIndex, 1);
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
                    <section class="space-y-4">
                        <SectionHeader index="01" title="Contacts" />

                        <div>
                            <InputLabel for="full_name" value="Full name" />
                            <TextInput id="full_name" v-model="form.full_name" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="form.errors.full_name" />
                        </div>

                        <div>
                            <InputLabel for="position" value="Position" />
                            <TextInput id="position" v-model="form.position" class="mt-1 block w-full" />
                            <InputError class="mt-1" :message="form.errors.position" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" type="email" v-model="form.email" class="mt-1 block w-full" />
                            <InputError class="mt-1" :message="form.errors.email" />
                        </div>

                        <div class="space-y-2 pt-2">
                            <InputLabel value="Links" />

                            <TransitionGroup name="row" tag="div" class="space-y-2">
                                <div v-for="(link, i) in form.links" :key="i" class="flex items-start gap-2">
                                    <TextInput v-model="link.label" placeholder="GitHub" class="w-1/3" />
                                    <TextInput v-model="link.url" placeholder="https://..." class="flex-1" />
                                    <IconButton label="Remove link" @click="removeLink(i)">
                                        <RemoveIcon />
                                    </IconButton>
                                </div>
                            </TransitionGroup>

                            <AddRowButton @click="addLink">Add link</AddRowButton>
                        </div>
                    </section>

                    <section class="space-y-4">
                        <SectionHeader index="02" title="About" />
                        <textarea
                            v-model="form.about"
                            rows="5"
                            placeholder="A couple of sentences about you as a professional."
                            class="w-full border border-black/20 px-3 py-2 text-black transition-colors focus:border-black focus:outline-none"
                        />
                        <InputError :message="form.errors.about" />
                    </section>

                    <section class="space-y-4">
                        <SectionHeader index="03" title="Skills" />

                        <TransitionGroup name="row" tag="div" class="space-y-4">
                            <div
                                v-for="(group, gi) in form.skill_groups"
                                :key="gi"
                                class="space-y-3 border border-black/15 p-4"
                            >
                                <div class="flex items-start gap-2">
                                    <TextInput
                                        v-model="group.label"
                                        placeholder="Languages, Frameworks..."
                                        class="flex-1"
                                    />
                                    <IconButton label="Remove skill group" @click="removeSkillGroup(gi)">
                                        <RemoveIcon />
                                    </IconButton>
                                </div>

                                <TransitionGroup name="row" tag="div" class="space-y-2">
                                    <div
                                        v-for="(skill, si) in group.skills"
                                        :key="si"
                                        class="flex items-center gap-2"
                                    >
                                        <TextInput v-model="skill.value" placeholder="PHP" class="flex-1" />
                                        <IconButton label="Remove skill" @click="removeSkill(gi, si)">
                                            <RemoveIcon />
                                        </IconButton>
                                    </div>
                                </TransitionGroup>

                                <AddRowButton @click="addSkill(gi)">Add skill</AddRowButton>
                            </div>
                        </TransitionGroup>

                        <AddRowButton @click="addSkillGroup">Add skill group</AddRowButton>
                    </section>

                    <section class="space-y-4">
                        <SectionHeader index="04" title="Experience" />

                        <TransitionGroup name="row" tag="div" class="space-y-4">
                            <div
                                v-for="(experience, ei) in form.experiences"
                                :key="ei"
                                class="space-y-4 border border-black/15 p-4"
                            >
                                <div class="flex items-start gap-2">
                                    <div class="grid flex-1 gap-2 sm:grid-cols-2">
                                        <TextInput v-model="experience.title" placeholder="Title" />
                                        <TextInput v-model="experience.company" placeholder="Company" />
                                    </div>
                                    <IconButton label="Remove experience" @click="removeExperience(ei)">
                                        <RemoveIcon />
                                    </IconButton>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    <TextInput
                                        v-model="experience.period_from"
                                        type="date"
                                        class="min-w-[9rem] flex-1"
                                    />
                                    <TextInput
                                        v-model="experience.period_to"
                                        type="date"
                                        class="min-w-[9rem] flex-1"
                                        :disabled="experience.is_current"
                                    />
                                    <label class="flex items-center gap-1.5 text-sm text-black/70">
                                        <Checkbox v-model:checked="experience.is_current" />
                                        Current
                                    </label>
                                </div>

                                <TransitionGroup name="row" tag="div" class="space-y-2">
                                    <div
                                        v-for="(bullet, bi) in experience.bullets"
                                        :key="bi"
                                        class="flex items-start gap-2"
                                    >
                                        <select
                                            v-model="bullet.type"
                                            class="border border-black/20 px-2 py-2 text-sm transition-colors focus:border-black focus:outline-none"
                                        >
                                            <option value="responsibility">Responsibility</option>
                                            <option value="achievement">Achievement</option>
                                        </select>
                                        <TextInput v-model="bullet.text" class="flex-1" />
                                        <IconButton label="Remove bullet" @click="removeBullet(ei, bi)">
                                            <RemoveIcon />
                                        </IconButton>
                                    </div>
                                </TransitionGroup>

                                <div class="flex flex-wrap gap-2">
                                    <AddRowButton class="flex-1" @click="addBullet(ei, 'responsibility')">
                                        Add responsibility
                                    </AddRowButton>
                                    <AddRowButton class="flex-1" @click="addBullet(ei, 'achievement')">
                                        Add achievement
                                    </AddRowButton>
                                </div>
                            </div>
                        </TransitionGroup>

                        <AddRowButton @click="addExperience">Add experience</AddRowButton>
                    </section>

                    <div class="space-y-2">
                        <PrimaryButton type="submit" :disabled="form.processing" class="w-full sm:w-auto">
                            Save resume
                        </PrimaryButton>
                        <p class="text-xs text-black/50">
                            We'll ask you to sign in only when you save - nothing you've typed is lost.
                        </p>
                    </div>
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

<style scoped>
.row-enter-active,
.row-leave-active {
    transition: all 0.18s ease;
}

.row-enter-from,
.row-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
