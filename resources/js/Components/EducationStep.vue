<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid';
import TextInput from '@/Components/TextInput.vue';
import DateInput from '@/Components/DateInput.vue';
import IconButton from '@/Components/IconButton.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import { useRowIds } from '@/composables/useRowIds';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const { nextRowId, withIds } = useRowIds();
withIds(props.form.educations);

function addEducation() {
    props.form.educations.push({
        id: nextRowId(),
        institution: '',
        field: '',
        period_from: '',
        period_to: '',
    });
}

function removeEducation(index) {
    props.form.educations.splice(index, 1);
}
</script>

<template>
    <div>
        <h2 class="mb-4 text-base font-semibold text-ink">Education</h2>

        <TransitionGroup name="row" tag="div" class="space-y-4">
            <div
                v-for="(education, i) in form.educations"
                :key="education.id"
                class="flex items-start gap-2 rounded-md border border-ink/15 bg-white p-4"
            >
                <div class="min-w-0 flex-1 space-y-2">
                    <div class="grid gap-2 sm:grid-cols-2">
                        <TextInput v-model="education.institution" placeholder="Institution" />
                        <TextInput v-model="education.field" placeholder="Field of study" />
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <DateInput v-model="education.period_from" placeholder="Start date" class="min-w-[9rem] flex-1" />
                        <DateInput v-model="education.period_to" placeholder="End date" class="min-w-[9rem] flex-1" />
                    </div>
                </div>

                <IconButton label="Remove education" align-top @click="removeEducation(i)">
                    <XMarkIcon class="h-4 w-4" />
                </IconButton>
            </div>
        </TransitionGroup>

        <AddRowButton class="mt-4" @click="addEducation">Add education</AddRowButton>
    </div>
</template>
