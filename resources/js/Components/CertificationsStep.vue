<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid';
import TextInput from '@/Components/TextInput.vue';
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
withIds(props.form.certifications);

function addCertification() {
    props.form.certifications.push({ id: nextRowId(), title: '', provider: '' });
}

function removeCertification(index) {
    props.form.certifications.splice(index, 1);
}
</script>

<template>
    <div>
        <h2 class="mb-4 text-base font-semibold text-ink">Certifications</h2>

        <div class="rounded-md border border-ink/15 bg-white p-4">
            <TransitionGroup name="row" tag="div" class="divide-y divide-ink/10 sm:space-y-2 sm:divide-y-0">
                <div
                    v-for="(certification, i) in form.certifications"
                    :key="certification.id"
                    class="flex flex-wrap items-stretch gap-2 py-3 first:pt-0 sm:py-0"
                >
                    <TextInput
                        v-model="certification.title"
                        placeholder="Exam"
                        class="order-1 min-w-0 flex-1 sm:w-1/3 sm:flex-none"
                    />
                    <IconButton label="Remove certification" class="order-2 sm:order-3" @click="removeCertification(i)">
                        <XMarkIcon class="h-4 w-4" />
                    </IconButton>
                    <TextInput
                        v-model="certification.provider"
                        placeholder="Provider"
                        class="order-3 min-w-0 w-full sm:order-2 sm:w-auto sm:flex-1"
                    />
                </div>
            </TransitionGroup>

            <AddRowButton class="mt-3" @click="addCertification">Add certification</AddRowButton>
        </div>
    </div>
</template>
