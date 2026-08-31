<script setup>
import TextInput from '@/Components/TextInput.vue';
import IconButton from '@/Components/IconButton.vue';
import RemoveIcon from '@/Components/RemoveIcon.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import { useRowIds } from '@/composables/useRowIds';

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

function addSkillGroup() {
    props.form.skill_groups.push({ id: nextRowId(), label: '', skills: [] });
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
                class="space-y-3 rounded-md border border-ink/15 bg-white p-4"
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
                        :key="skill.id"
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

        <AddRowButton class="mt-4" @click="addSkillGroup">Add skill group</AddRowButton>
    </div>
</template>
