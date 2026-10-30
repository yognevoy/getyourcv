<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid';
import draggable from 'vuedraggable';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import IconButton from '@/Components/IconButton.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import DragHandle from '@/Components/DragHandle.vue';
import { useRowIds } from '@/composables/useRowIds';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const { nextRowId, withIds } = useRowIds();
withIds(props.form.projects);

function addProject() {
    props.form.projects.push({ id: nextRowId(), title: '', description: '' });
}

function removeProject(index) {
    props.form.projects.splice(index, 1);
}
</script>

<template>
    <div>
        <h2 class="mb-4 text-base font-semibold text-ink">Projects & Contributions</h2>

        <div class="rounded-md border border-ink/15 bg-white p-4">
            <draggable
                v-model="form.projects"
                item-key="id"
                handle=".drag-handle"
                tag="div"
                class="divide-y divide-ink/10 sm:space-y-4 sm:divide-y-0"
                :animation="150"
                :force-fallback="true"
            >
                <template #item="{ element: project, index: i }">
                    <div class="flex items-stretch gap-2 py-3 first:pt-0 sm:py-0">
                        <DragHandle align-top />
                        <div class="min-w-0 flex-1 space-y-2">
                            <TextInput v-model="project.title" placeholder="Project title" maxlength="50" class="w-full" />
                            <Textarea v-model="project.description" placeholder="Description" maxlength="300" />
                        </div>
                        <IconButton label="Remove project" align-top @click="removeProject(i)">
                            <XMarkIcon class="h-4 w-4" />
                        </IconButton>
                    </div>
                </template>
            </draggable>

            <AddRowButton class="mt-3" @click="addProject">Add project</AddRowButton>
        </div>
    </div>
</template>
