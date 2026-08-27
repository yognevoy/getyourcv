<script setup>
import { computed, ref, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import IconButton from '@/Components/IconButton.vue';
import RemoveIcon from '@/Components/RemoveIcon.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
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

// Maps a validation error key (e.g. "links.0.url") back to the step that owns it,
// so a failed submit jumps the user to the first step with an error instead of
// leaving them staring at a step with no visible error.
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
const formTop = ref(null);

function next() {
    if (!isLastStep.value) {
        currentStep.value += 1;
    }
}

function back() {
    if (currentStep.value > 0) {
        currentStep.value -= 1;
    }
}

watch(currentStep, () => {
    formTop.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

watch(
    () => props.form.errors,
    (errors) => {
        const firstField = Object.keys(errors)[0];

        if (firstField) {
            currentStep.value = stepForField(firstField);
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
    props.form.experiences.push({
        company: '',
        title: '',
        period_from: '',
        period_to: '',
        is_current: false,
        bullets: [],
    });
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
    <div ref="formTop">
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
                            :key="gi"
                            class="space-y-3 rounded-md border border-ink/15 p-4"
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
                </template>

                <template v-else>
                    <TransitionGroup name="row" tag="div" class="space-y-4">
                        <div
                            v-for="(experience, ei) in form.experiences"
                            :key="ei"
                            class="space-y-4 rounded-md border border-ink/15 p-4"
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
                                <label class="flex items-center gap-1.5 text-sm text-ink/70">
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
                                        class="rounded-md border border-ink/20 px-2 py-2 text-sm transition-colors focus:border-ink focus:outline-none"
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
                @click="!isLastStep && next()"
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
