<script setup>
import { ref, watch } from 'vue';
import { EllipsisVerticalIcon, SparklesIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import TextInput from '@/Components/TextInput.vue';
import DateInput from '@/Components/DateInput.vue';
import Textarea from '@/Components/Textarea.vue';
import IconButton from '@/Components/IconButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import AiRewriteDialog from '@/Components/AiRewriteDialog.vue';
import { useRowIds } from '@/composables/useRowIds';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

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

const { nextRowId, withIds } = useRowIds();
withIds(props.form.experiences).forEach((experience) => withIds(experience.bullets));

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

// A card full of everything (title, company, dates, both bullet lists) reads
// as clutter once there is more than one experience - collapse to a one-line
// summary by default and let the person open the ones they need.
const expandedExperienceKeys = ref(
    new Set(
        props.form.experiences.length <= 1
            ? props.form.experiences.map((experience) => experience.id)
            : [],
    ),
);

function isExperienceExpanded(experience) {
    return expandedExperienceKeys.value.has(experience.id);
}

function expandExperience(experience) {
    expandedExperienceKeys.value.add(experience.id);
}

function toggleExperience(experience) {
    if (expandedExperienceKeys.value.has(experience.id)) {
        expandedExperienceKeys.value.delete(experience.id);
    } else {
        expandedExperienceKeys.value.add(experience.id);
    }
}

// A validation error can land on a field inside a collapsed card - expand
// that specific experience so the error is actually visible.
watch(
    () => props.form.errors,
    (errors) => {
        const firstField = Object.keys(errors)[0];
        const experienceMatch = firstField && firstField.match(/^experiences\.(\d+)\./);
        const experience = experienceMatch && props.form.experiences[Number(experienceMatch[1])];

        if (experience) {
            expandExperience(experience);
        }
    },
    { deep: true },
);

function addExperience() {
    const experience = {
        id: nextRowId(),
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
    props.form.experiences[experienceIndex].bullets.push({ id: nextRowId(), type, text: '' });
}

function removeBullet(experienceIndex, bulletIndex) {
    props.form.experiences[experienceIndex].bullets.splice(bulletIndex, 1);
}

const activeBullet = ref(null);

function openAiDialog(bullet) {
    activeBullet.value = bullet;
}

function closeAiDialog() {
    activeBullet.value = null;
}

function applyRewrite(text) {
    if (activeBullet.value) {
        activeBullet.value.text = text;
    }
}
</script>

<template>
    <div>
        <h2 class="mb-4 text-base font-semibold text-ink">Experience</h2>

        <TransitionGroup name="row" tag="div" class="space-y-4">
            <div
                v-for="(experience, ei) in form.experiences"
                :key="experience.id"
                class="rounded-md border border-ink/15 bg-white"
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
                        <XMarkIcon class="h-4 w-4" />
                    </IconButton>
                </div>

                <div
                    class="grid transition-[grid-template-rows] duration-200 ease-out"
                    :style="{ gridTemplateRows: isExperienceExpanded(experience) ? '1fr' : '0fr' }"
                >
                    <div :class="isExperienceExpanded(experience) ? 'overflow-visible' : 'overflow-hidden'">
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
                                        :key="item.bullet.id"
                                        class="flex items-stretch gap-2"
                                    >
                                        <Textarea v-model="item.bullet.text" class="flex-1" />
                                        <Dropdown align="left" class="self-stretch">
                                            <template #trigger>
                                                <IconButton label="Bullet actions" align-top class="h-full">
                                                    <EllipsisVerticalIcon class="h-4 w-4" />
                                                </IconButton>
                                            </template>
                                            <template #content>
                                                <button
                                                    type="button"
                                                    :disabled="!item.bullet.text || item.bullet.text.trim().length < 3"
                                                    class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none disabled:cursor-not-allowed disabled:text-ink/30"
                                                    @click="openAiDialog(item.bullet)"
                                                >
                                                    <SparklesIcon class="h-4 w-4" />
                                                    AI enhance
                                                </button>
                                                <button
                                                    type="button"
                                                    class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none"
                                                    @click="removeBullet(ei, item.index)"
                                                >
                                                    Remove
                                                </button>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </TransitionGroup>

                                <AddRowButton :full-width="false" @click="addBullet(ei, 'responsibility')">Add responsibility</AddRowButton>
                            </div>

                            <div class="space-y-2">
                                <span class="block text-xs font-medium uppercase tracking-wide text-ink/50">Achievements</span>

                                <TransitionGroup name="row" tag="div" class="space-y-2">
                                    <div
                                        v-for="item in bulletsOfType(experience, 'achievement')"
                                        :key="item.bullet.id"
                                        class="flex items-stretch gap-2"
                                    >
                                        <Textarea v-model="item.bullet.text" class="flex-1" />
                                        <Dropdown align="left" class="self-stretch">
                                            <template #trigger>
                                                <IconButton label="Bullet actions" align-top class="h-full">
                                                    <EllipsisVerticalIcon class="h-4 w-4" />
                                                </IconButton>
                                            </template>
                                            <template #content>
                                                <button
                                                    type="button"
                                                    :disabled="!item.bullet.text || item.bullet.text.trim().length < 3"
                                                    class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none disabled:cursor-not-allowed disabled:text-ink/30"
                                                    @click="openAiDialog(item.bullet)"
                                                >
                                                    <SparklesIcon class="h-4 w-4" />
                                                    AI enhance
                                                </button>
                                                <button
                                                    type="button"
                                                    class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none"
                                                    @click="removeBullet(ei, item.index)"
                                                >
                                                    Remove
                                                </button>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </TransitionGroup>

                                <AddRowButton :full-width="false" @click="addBullet(ei, 'achievement')">Add achievement</AddRowButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </TransitionGroup>

        <AddRowButton class="mt-4" @click="addExperience">Add experience</AddRowButton>

        <AiRewriteDialog
            :show="activeBullet !== null"
            :text="activeBullet?.text || ''"
            :target="activeBullet?.type || ''"
            @close="closeAiDialog"
            @apply="applyRewrite"
        />
    </div>
</template>
