<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid';
import TextInput from '@/Components/TextInput.vue';
import IconButton from '@/Components/IconButton.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import { useRowIds } from '@/composables/useRowIds';
import { useCollapsibleRows } from '@/composables/useCollapsibleRows';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const SKILL_PLACEHOLDERS = ['PHP', 'Docker', 'PostgreSQL', 'Git', 'AWS'];

const { nextRowId, withIds } = useRowIds();
withIds(props.form.skill_groups).forEach((group) => withIds(group.skills));

function skillPlaceholder(skill) {
    return SKILL_PLACEHOLDERS[skill.id % SKILL_PLACEHOLDERS.length];
}

function skillGroupSummaryTitle(group) {
    return group.label || 'New skill group';
}

function skillGroupSummaryDetail(group) {
    const values = group.skills.map((skill) => skill.value).filter(Boolean);

    return values.length ? values.join(', ') : 'No skills yet';
}

const {
    isExpanded: isSkillGroupExpanded,
    toggle: toggleSkillGroup,
    expandOnly: expandOnlySkillGroup,
} = useCollapsibleRows(props.form.skill_groups, {
    errors: () => props.form.errors,
    errorPrefix: 'skill_groups',
});

function addSkillGroup() {
    const group = { id: nextRowId(), label: '', skills: [] };

    props.form.skill_groups.push(group);
    expandOnlySkillGroup(group);
}

function removeSkillGroup(index) {
    props.form.skill_groups.splice(index, 1);
}

function addSkill(groupIndex) {
    props.form.skill_groups[groupIndex].skills.push({ id: nextRowId(), value: '' });
}

function removeSkill(groupIndex, skillIndex) {
    props.form.skill_groups[groupIndex].skills.splice(skillIndex, 1);
}
</script>

<template>
    <div>
        <h2 class="mb-4 text-base font-semibold text-ink">Skills</h2>

        <TransitionGroup name="row" tag="div" class="space-y-4">
            <div
                v-for="(group, gi) in form.skill_groups"
                :key="group.id"
                class="rounded-md border border-ink/15 bg-white"
            >
                <div class="flex items-stretch gap-2 p-4">
                    <button
                        type="button"
                        class="flex min-w-0 flex-1 items-center gap-3 text-left"
                        :aria-expanded="isSkillGroupExpanded(group)"
                        @click="toggleSkillGroup(group)"
                    >
                        <svg
                            class="h-3 w-3 shrink-0 text-ink/40 transition-transform"
                            :class="{ 'rotate-90': isSkillGroupExpanded(group) }"
                            viewBox="0 0 16 16"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path d="M6 3l5 5-5 5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-ink">
                                {{ skillGroupSummaryTitle(group) }}
                            </span>
                            <span class="block truncate text-xs text-ink/60">
                                {{ skillGroupSummaryDetail(group) }}
                            </span>
                        </span>
                    </button>

                    <IconButton label="Remove skill group" @click="removeSkillGroup(gi)">
                        <XMarkIcon class="h-4 w-4" />
                    </IconButton>
                </div>

                <div
                    class="grid transition-[grid-template-rows] duration-200 ease-out"
                    :style="{ gridTemplateRows: isSkillGroupExpanded(group) ? '1fr' : '0fr' }"
                >
                    <div :class="isSkillGroupExpanded(group) ? 'overflow-visible' : 'overflow-hidden'">
                        <div class="space-y-3 border-t border-ink/15 p-4">
                            <TextInput
                                v-model="group.label"
                                placeholder="Languages, Frameworks..."
                                class="w-full"
                            />

                            <TransitionGroup name="row" tag="div" class="ml-2 space-y-2 border-l border-ink/15 pl-4">
                                <div
                                    v-for="(skill, si) in group.skills"
                                    :key="skill.id"
                                    class="flex items-stretch gap-2"
                                >
                                    <TextInput v-model="skill.value" :placeholder="skillPlaceholder(skill)" class="flex-1" />
                                    <IconButton label="Remove skill" @click="removeSkill(gi, si)">
                                        <XMarkIcon class="h-4 w-4" />
                                    </IconButton>
                                </div>
                            </TransitionGroup>

                            <AddRowButton :full-width="false" @click="addSkill(gi)">Add skill</AddRowButton>
                        </div>
                    </div>
                </div>
            </div>
        </TransitionGroup>

        <AddRowButton class="mt-4" @click="addSkillGroup">Add skill group</AddRowButton>
    </div>
</template>
