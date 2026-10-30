<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid';
import draggable from 'vuedraggable';
import TextInput from '@/Components/TextInput.vue';
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
withIds(props.form.courses);

function addCourse() {
    props.form.courses.push({ id: nextRowId(), title: '', provider: '' });
}

function removeCourse(index) {
    props.form.courses.splice(index, 1);
}
</script>

<template>
    <div>
        <h2 class="mb-4 text-base font-semibold text-ink">Courses</h2>

        <div class="rounded-md border border-ink/15 bg-white p-4">
            <draggable
                v-model="form.courses"
                item-key="id"
                handle=".drag-handle"
                tag="div"
                class="divide-y divide-ink/10 sm:space-y-2 sm:divide-y-0"
                :animation="150"
                    :force-fallback="true"
            >
                <template #item="{ element: course, index: i }">
                    <div class="flex flex-wrap items-stretch gap-2 py-3 first:pt-0 sm:py-0">
                        <DragHandle />
                        <TextInput
                            v-model="course.title"
                            placeholder="Course title"
                            maxlength="50"
                            class="order-1 min-w-0 flex-1 sm:w-1/3 sm:flex-none"
                        />
                        <IconButton label="Remove course" class="order-2 sm:order-3" @click="removeCourse(i)">
                            <XMarkIcon class="h-4 w-4" />
                        </IconButton>
                        <TextInput
                            v-model="course.provider"
                            placeholder="Provider"
                            maxlength="70"
                            class="order-3 min-w-0 w-full sm:order-2 sm:w-auto sm:flex-1"
                        />
                    </div>
                </template>
            </draggable>

            <AddRowButton class="mt-3" @click="addCourse">Add course</AddRowButton>
        </div>
    </div>
</template>
