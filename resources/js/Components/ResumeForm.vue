<script setup>
import { computed, ref, watch } from 'vue';
import ContactsStep from '@/Components/ContactsStep.vue';
import AboutStep from '@/Components/AboutStep.vue';
import SkillsStep from '@/Components/SkillsStep.vue';
import ExperienceStep from '@/Components/ExperienceStep.vue';
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

        if (firstField) {
            currentStep.value = stepForField(firstField);
        }
    },
    { deep: true },
);
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
            <section :key="currentStep" class="min-h-[22rem]">
                <ContactsStep v-if="currentStep === 0" :form="form" />
                <AboutStep v-else-if="currentStep === 1" :form="form" />
                <SkillsStep v-else-if="currentStep === 2" :form="form" />
                <ExperienceStep v-else :form="form" />
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
