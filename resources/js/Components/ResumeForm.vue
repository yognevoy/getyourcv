<script setup>
import { computed, ref, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import IconButton from '@/Components/IconButton.vue';
import RemoveIcon from '@/Components/RemoveIcon.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import Textarea from '@/Components/Textarea.vue';
import DateInput from '@/Components/DateInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitLabel: {
        type: String,
        default: 'Save',
    },
});

const STEPS = ['Contacts', 'About', 'Skills', 'Experience'];

const LINK_LABEL_PLACEHOLDERS = ['GitHub', 'LinkedIn', 'Portfolio', 'Telegram', 'Twitter'];
const SKILL_PLACEHOLDERS = ['PHP', 'Docker', 'PostgreSQL', 'Git', 'AWS'];
const MONTHS_FULL = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
];

function formatMonthYear(value) {
    if (!value) {
        return null;
    }

    const [year, month] = value.split('-').map(Number);

    return `${MONTHS_FULL[month - 1]} ${year}`;
}

let rowKeyCounter = 0;
const rowKeys = new WeakMap();

function rowKey(item) {
    if (!rowKeys.has(item)) {
        rowKeys.set(item, rowKeyCounter++);
    }

    return rowKeys.get(item);
}

function linkLabelPlaceholder(link) {
    return LINK_LABEL_PLACEHOLDERS[rowKey(link) % LINK_LABEL_PLACEHOLDERS.length];
}

function skillPlaceholder(skill) {
    return SKILL_PLACEHOLDERS[rowKey(skill) % SKILL_PLACEHOLDERS.length];
}

function bulletsOfType(experience, type) {
    return experience.bullets
        .map((bullet, index) => ({ bullet, index }))
        .filter((item) => item.bullet.type === type);
}

function experienceSummaryTitle(experience) {
    if (experience.title && experience.company) {
        return `${experience.title} at ${experience.company}`;
    }

    return experience.title || experience.company || 'New experience';
}

function experienceSummaryPeriod(experience) {
    const from = formatMonthYear(experience.period_from);
    const to = experience.is_current ? 'Present' : formatMonthYear(experience.period_to);

    if (!from && !to) {
        return 'No dates yet';
    }

    return `${from || '…'} – ${to || '…'}`;
}

const expandedExperienceKeys = ref(
    new Set(
        props.form.experiences.length <= 1
            ? props.form.experiences.map((experience) => rowKey(experience))
            : [],
    ),
);

function isExperienceExpanded(experience) {
    return expandedExperienceKeys.value.has(rowKey(experience));
}

function expandExperience(experience) {
    expandedExperienceKeys.value.add(rowKey(experience));
}

function toggleExperience(experience) {
    const key = rowKey(experience);

    if (expandedExperienceKeys.value.has(key)) {
        expandedExperienceKeys.value.delete(key);
    } else {
        expandedExperienceKeys.value.add(key);
    }
}

function stepForField(field) {
    if (field.startsWith('links') || ['full_name', 'position', 'email'].includes(field)) return 0;
    if (field === 'about') return 1;
    if (field.startsWith('skill_groups')) return 2;
    if (field.startsWith('experiences')) return 3;
    return 0;
}

const currentStep = ref(0);
const isLastStep = computed(() => currentStep.value === STEPS.length - 1);
const progressPercent = computed(() => ((currentStep.value + 1) / STEPS.length) * 100);
const nextLabel = computed(() => (isLastStep.value ? props.submitLabel : `Next: ${STEPS[currentStep.value + 1]}`));

function next() {
    if (!isLastStep.value) {
        currentStep.value += 1;
    }
}

function onNextClick(event) {
    if (isLastStep.value) {
        return;
    }

    event.preventDefault();
    next();
}

function back() {
    if (currentStep.value > 0) {
        currentStep.value -= 1;
    }
}

watch(
    () => props.form.errors,
    (errors) => {
        const firstField = Object.keys(errors)[0];

        if (!firstField) {
            return;
        }

        currentStep.value = stepForField(firstField);

        const experienceMatch = firstField.match(/^experiences\.(\d+)\./);
        const experience = experienceMatch && props.form.experiences[Number(experienceMatch[1])];

        if (experience) {
            expandExperience(experience);
        }
    },
    { deep: true },
);

function addLink() {
    props.form.links.push({ label: '', url: '' });
}

function removeLink(index) {
    props.form.links.splice(index, 1);
}

function addSkillGroup() {
    props.form.skill_groups.push({ label: '', skills: [] });
}

function removeSkillGroup(index) {
    props.form.skill_groups.splice(index, 1);
}

function addSkill(groupIndex) {
    props.form.skill_groups[groupIndex].skills.push({ value: '' });
}

function removeSkill(groupIndex, skillIndex) {
    props.form.skill_groups[groupIndex].skills.splice(skillIndex, 1);
}

function addExperience() {
    const experience = {
        company: '',
        title: '',
        period_from: '',
        period_to: '',
        is_current: false,
        bullets: [],
    };

    props.form.experiences.push(experience);
    expandExperience(experience);
}

function removeExperience(index) {
    props.form.experiences.splice(index, 1);
}

function addBullet(experienceIndex, type) {
    props.form.experiences[experienceIndex].bullets.push({ type, text: '' });
}

function removeBullet(experienceIndex, bulletIndex) {
    props.form.experiences[experienceIndex].bullets.splice(bulletIndex, 1);
}
</script>

<template>
    <div>
        <div
            class="mb-10 h-1 w-full overflow-hidden rounded-full bg-ink/10"
            role="progressbar"
            :aria-valuenow="currentStep + 1"
            aria-valuemin="1"
            :aria-valuemax="STEPS.length"
            :aria-valuetext="`Step ${currentStep + 1} of ${STEPS.length}: ${STEPS[currentStep]}`"
        >
            <div
                class="h-full rounded-full bg-ink transition-[width] duration-300 ease-out"
                :style="{ width: `${progressPercent}%` }"
            ></div>
        </div>

        <Transition name="step" mode="out-in">
            <section :key="currentStep" class="min-h-[22rem] space-y-4">
                <template v-if="currentStep === 0">
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
                            <div v-for="(link, i) in form.links" :key="rowKey(link)" class="flex items-stretch gap-2">
                                <TextInput v-model="link.label" :placeholder="linkLabelPlaceholder(link)" class="w-1/3" />
                                <TextInput v-model="link.url" placeholder="https://..." class="flex-1" />
                                <IconButton label="Remove link" @click="removeLink(i)">
                                    <RemoveIcon />
                                </IconButton>
                            </div>
                        </TransitionGroup>

                        <AddRowButton @click="addLink">Add link</AddRowButton>
                    </div>
                </template>

                <template v-else-if="currentStep === 1">
                    <InputLabel for="about" value="About" />
                    <textarea
                        id="about"
                        v-model="form.about"
                        rows="10"
                        placeholder="A couple of sentences about you as a professional."
                        class="w-full rounded-md border border-ink/20 px-3 py-2 text-ink transition-colors focus:border-ink focus:outline-none"
                    />
                    <InputError :message="form.errors.about" />
                </template>

                <template v-else-if="currentStep === 2">
                    <TransitionGroup name="row" tag="div" class="space-y-4">
                        <div
                            v-for="(group, gi) in form.skill_groups"
                            :key="rowKey(group)"
                            class="space-y-3 rounded-md border border-ink/15 p-4"
                        >
                            <div class="flex items-stretch gap-2">
                                <TextInput
                                    v-model="group.label"
                                    placeholder="Languages, Frameworks..."
                                    class="flex-1"
                                />
                                <IconButton label="Remove skill group" @click="removeSkillGroup(gi)">
                                    <RemoveIcon />
                                </IconButton>
                            </div>

                            <TransitionGroup name="row" tag="div" class="ml-2 space-y-2 border-l border-ink/15 pl-4">
                                <div
                                    v-for="(skill, si) in group.skills"
                                    :key="rowKey(skill)"
                                    class="flex items-stretch gap-2"
                                >
                                    <TextInput v-model="skill.value" :placeholder="skillPlaceholder(skill)" class="flex-1" />
                                    <IconButton label="Remove skill" @click="removeSkill(gi, si)">
                                        <RemoveIcon />
                                    </IconButton>
                                </div>
                            </TransitionGroup>

                            <AddRowButton :full-width="false" @click="addSkill(gi)">Add skill</AddRowButton>
                        </div>
                    </TransitionGroup>

                    <AddRowButton @click="addSkillGroup">Add skill group</AddRowButton>
                </template>

                <template v-else>
                    <TransitionGroup name="row" tag="div" class="space-y-4">
                        <div
                            v-for="(experience, ei) in form.experiences"
                            :key="rowKey(experience)"
                            class="rounded-md border border-ink/15"
                        >
                            <div class="flex items-stretch gap-2 p-4">
                                <button
                                    type="button"
                                    class="flex min-w-0 flex-1 items-center gap-3 text-left"
                                    :aria-expanded="isExperienceExpanded(experience)"
                                    @click="toggleExperience(experience)"
                                >
                                    <svg
                                        class="h-3 w-3 shrink-0 text-ink/40 transition-transform"
                                        :class="{ 'rotate-90': isExperienceExpanded(experience) }"
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path d="M6 3l5 5-5 5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-ink">
                                            {{ experienceSummaryTitle(experience) }}
                                        </span>
                                        <span class="block truncate text-xs text-ink/60">
                                            {{ experienceSummaryPeriod(experience) }}
                                        </span>
                                    </span>
                                </button>

                                <IconButton label="Remove experience" @click="removeExperience(ei)">
                                    <RemoveIcon />
                                </IconButton>
                            </div>

                            <div
                                class="grid transition-[grid-template-rows] duration-200 ease-out"
                                :style="{ gridTemplateRows: isExperienceExpanded(experience) ? '1fr' : '0fr' }"
                            >
                                <div class="overflow-hidden">
                                    <div class="space-y-4 border-t border-ink/15 p-4">
                                        <div class="grid gap-2 sm:grid-cols-2">
                                            <TextInput v-model="experience.title" placeholder="Title" />
                                            <TextInput v-model="experience.company" placeholder="Company" />
                                        </div>

                                        <div class="flex flex-wrap items-center gap-3">
                                            <DateInput
                                                v-model="experience.period_from"
                                                placeholder="Start date"
                                                class="min-w-[9rem] flex-1"
                                            />
                                            <DateInput
                                                v-model="experience.period_to"
                                                v-model:current="experience.is_current"
                                                presentable
                                                placeholder="End date"
                                                class="min-w-[9rem] flex-1"
                                            />
                                        </div>

                                        <div class="space-y-2">
                                            <span class="block text-xs font-medium uppercase tracking-wide text-ink/50">Responsibilities</span>

                                            <TransitionGroup name="row" tag="div" class="space-y-2">
                                                <div
                                                    v-for="item in bulletsOfType(experience, 'responsibility')"
                                                    :key="rowKey(item.bullet)"
                                                    class="flex items-stretch gap-2"
                                                >
                                                    <Textarea v-model="item.bullet.text" class="flex-1" />
                                                    <IconButton label="Remove responsibility" align-top @click="removeBullet(ei, item.index)">
                                                        <RemoveIcon />
                                                    </IconButton>
                                                </div>
                                            </TransitionGroup>

                                            <AddRowButton :full-width="false" @click="addBullet(ei, 'responsibility')">Add responsibility</AddRowButton>
                                        </div>

                                        <div class="space-y-2">
                                            <span class="block text-xs font-medium uppercase tracking-wide text-ink/50">Achievements</span>

                                            <TransitionGroup name="row" tag="div" class="space-y-2">
                                                <div
                                                    v-for="item in bulletsOfType(experience, 'achievement')"
                                                    :key="rowKey(item.bullet)"
                                                    class="flex items-stretch gap-2"
                                                >
                                                    <Textarea v-model="item.bullet.text" class="flex-1" />
                                                    <IconButton label="Remove achievement" align-top @click="removeBullet(ei, item.index)">
                                                        <RemoveIcon />
                                                    </IconButton>
                                                </div>
                                            </TransitionGroup>

                                            <AddRowButton :full-width="false" @click="addBullet(ei, 'achievement')">Add achievement</AddRowButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>

                    <AddRowButton @click="addExperience">Add experience</AddRowButton>
                </template>
            </section>
        </Transition>

        <div class="mt-8 flex items-center justify-between border-t border-ink/10 pt-6">
            <SecondaryButton v-if="currentStep > 0" type="button" @click="back">
                Back
            </SecondaryButton>
            <span v-else></span>

            <PrimaryButton
                :type="isLastStep ? 'submit' : 'button'"
                :disabled="isLastStep && form.processing"
                @click="onNextClick"
            >
                {{ nextLabel }}
            </PrimaryButton>
        </div>
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

.row-move {
    transition: transform 0.18s ease;
}

.step-enter-active,
.step-leave-active {
    transition:
        opacity 0.15s ease,
        transform 0.15s ease;
}

.step-enter-from {
    opacity: 0;
    transform: translateX(8px);
}

.step-leave-to {
    opacity: 0;
    transform: translateX(-8px);
}
</style>
