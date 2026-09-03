<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { formatRelativeDate } from '@/utils/formatRelativeDate';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
    matches: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    vacancy_title: '',
    vacancy_text: '',
});

const selectedId = ref(props.matches[0]?.id ?? null);
const historyOpen = ref(false);

const howItWorksSteps = [
    { title: 'Paste a vacancy', description: 'Drop in the text of any vacancy, in full.' },
    { title: 'AI compares it to your resume', description: 'Skills and requirements are matched automatically.' },
    { title: 'See your score and gaps', description: 'Get a match score, plus exactly what you\'re missing.' },
];

const selected = computed(() => props.matches.find((match) => match.id === selectedId.value) ?? null);

const canSubmit = computed(() => form.vacancy_text.trim().length >= 30);

function submit() {
    form.post(route('resumes.match.store', props.resume.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedId.value = props.matches[0]?.id ?? null;
        },
    });
}
</script>

<template>
    <Head title="Match with a vacancy" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-screen-2xl px-4 py-10 sm:px-6 lg:px-10">
            <div class="max-w-2xl">
                <Link :href="route('resumes.edit', resume.id)" class="text-sm text-ink/50 hover:text-ink">
                    &larr; {{ resume.title }}
                </Link>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">Match with a vacancy</h1>
                <p class="mt-1 text-sm text-ink/60">
                    Paste a vacancy to see how {{ resume.full_name || 'this resume' }} lines up with it.
                </p>
            </div>

            <div class="relative mt-10 grid gap-10 xl:grid-cols-2 xl:gap-12">
                <div
                    class="hidden xl:absolute xl:inset-y-0 xl:left-1/2 xl:block xl:w-px xl:-translate-x-1/2 xl:bg-ink/20"
                    aria-hidden="true"
                ></div>

                <div>
                    <h2 class="mb-4 text-base font-semibold text-ink">Vacancy</h2>

                    <form class="space-y-4 rounded-md border border-ink/15 bg-white p-4" @submit.prevent="submit">
                        <div>
                            <InputLabel for="vacancy_title" value="Vacancy title (optional)" />
                            <TextInput
                                id="vacancy_title"
                                v-model="form.vacancy_title"
                                class="mt-1 block w-full"
                                placeholder="e.g. Senior Backend Engineer at Acme"
                            />
                            <InputError class="mt-1" :message="form.errors.vacancy_title" />
                        </div>

                        <div>
                            <InputLabel for="vacancy_text" value="Vacancy text" />
                            <textarea
                                id="vacancy_text"
                                v-model="form.vacancy_text"
                                rows="10"
                                placeholder="Paste the full vacancy text here."
                                class="mt-1 w-full rounded-md border border-ink/20 px-3 py-2 text-ink transition-colors focus:border-ink focus:outline-none"
                            />
                            <InputError class="mt-1" :message="form.errors.vacancy_text" />
                        </div>

                        <PrimaryButton type="submit" :disabled="!canSubmit || form.processing">
                            {{ form.processing ? 'Analyzing…' : 'Analyze match' }}
                        </PrimaryButton>
                    </form>
                </div>

                <div class="xl:pl-10">
                    <div class="overflow-hidden rounded-md border border-ink/15 bg-white">
                        <Transition name="fade" mode="out-in">
                            <div
                                v-if="selected"
                                :key="`hero-${selected.id}`"
                                class="flex items-center justify-between gap-6 bg-ink px-6 py-6 text-paper"
                            >
                                <div>
                                    <p class="text-6xl font-bold leading-none tracking-tight">{{ selected.score }}%</p>
                                    <p class="mt-2 text-xs uppercase tracking-widest text-paper/50">Match score</p>
                                </div>
                                <div class="text-right text-xs uppercase tracking-widest text-paper/50">
                                    <p>{{ selected.matched_skills.length }} matched</p>
                                    <p class="mt-1">{{ selected.missing_skills.length }} missing</p>
                                </div>
                            </div>
                        </Transition>

                        <div class="space-y-6 p-4">
                            <Transition name="fade" mode="out-in">
                                <div v-if="selected" :key="selected.id" class="space-y-6">
                                    <p class="text-sm text-ink/70">{{ selected.summary }}</p>

                                    <div>
                                        <h3 class="text-sm font-semibold text-ink">Matched skills</h3>
                                        <p v-if="!selected.matched_skills.length" class="mt-2 text-sm text-ink/40">None found.</p>
                                        <ul v-else class="mt-2 space-y-1.5 border-y border-ink/10 py-2">
                                            <li
                                                v-for="skill in selected.matched_skills"
                                                :key="skill"
                                                class="flex items-center gap-2 text-sm text-ink"
                                            >
                                                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M3.5 8.5l3 3 6-7" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                {{ skill }}
                                            </li>
                                        </ul>
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-ink">Missing skills</h3>
                                        <p v-if="!selected.missing_skills.length" class="mt-2 text-sm text-ink/40">None found.</p>
                                        <ul v-else class="mt-2 space-y-1.5 border-y border-ink/10 py-2">
                                            <li
                                                v-for="skill in selected.missing_skills"
                                                :key="skill"
                                                class="flex items-center gap-2 text-sm text-ink/70"
                                            >
                                                <svg class="h-3.5 w-3.5 shrink-0 text-ink/30" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M4 8h8" stroke-linecap="round" />
                                                </svg>
                                                {{ skill }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div v-else key="empty">
                                    <p class="text-sm text-ink/50">
                                        Paste a vacancy on the left to get your match score, matched skills, and gaps.
                                    </p>

                                    <ol class="relative mt-6 space-y-6 border-l border-ink/15 pl-6">
                                        <li v-for="(step, i) in howItWorksSteps" :key="step.title" class="relative">
                                            <span
                                                class="absolute -left-[27px] flex h-5 w-5 items-center justify-center rounded-full border border-ink/30 bg-white text-[11px] font-semibold text-ink/60"
                                            >
                                                {{ i + 1 }}
                                            </span>
                                            <p class="text-sm font-medium text-ink">{{ step.title }}</p>
                                            <p class="text-sm text-ink/50">{{ step.description }}</p>
                                        </li>
                                    </ol>
                                </div>
                            </Transition>

                            <div v-if="matches.length">
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2 text-left"
                                    :aria-expanded="historyOpen"
                                    @click="historyOpen = !historyOpen"
                                >
                                    <svg
                                        class="h-3 w-3 shrink-0 text-ink/40 transition-transform"
                                        :class="{ 'rotate-90': historyOpen }"
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path d="M6 3l5 5-5 5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-ink">Past matches</h3>
                                    <span class="text-xs text-ink/40">({{ matches.length }})</span>
                                </button>

                                <div
                                    class="grid transition-[grid-template-rows] duration-200 ease-out"
                                    :style="{ gridTemplateRows: historyOpen ? '1fr' : '0fr' }"
                                >
                                    <div class="overflow-hidden">
                                        <ul class="mt-2 max-h-64 divide-y divide-ink/10 overflow-y-auto border-y border-ink/10">
                                            <li v-for="match in matches" :key="match.id">
                                                <button
                                                    type="button"
                                                    class="flex w-full items-center justify-between gap-3 py-3 text-left"
                                                    @click="selectedId = match.id"
                                                >
                                                    <span
                                                        class="min-w-0 truncate text-sm"
                                                        :class="match.id === selectedId ? 'font-semibold text-ink' : 'text-ink/70'"
                                                    >
                                                        {{ match.vacancy_title || 'Untitled vacancy' }}
                                                    </span>
                                                    <span class="shrink-0 text-xs text-ink/50">
                                                        {{ match.score }}% &middot; {{ formatRelativeDate(match.created_at) }}
                                                    </span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
