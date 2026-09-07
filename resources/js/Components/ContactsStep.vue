<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import IconButton from '@/Components/IconButton.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import { useRowIds } from '@/composables/useRowIds';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const LINK_LABEL_PLACEHOLDERS = ['GitHub', 'LinkedIn', 'Portfolio', 'Telegram', 'Twitter'];

const { nextRowId, withIds } = useRowIds();
withIds(props.form.links);

function linkLabelPlaceholder(link) {
    return LINK_LABEL_PLACEHOLDERS[link.id % LINK_LABEL_PLACEHOLDERS.length];
}

function addLink() {
    props.form.links.push({ id: nextRowId(), label: '', url: '' });
}

function removeLink(index) {
    props.form.links.splice(index, 1);
}
</script>

<template>
    <div>
        <h2 class="mb-4 text-base font-semibold text-ink">Contacts</h2>

        <div class="space-y-4 rounded-md border border-ink/15 bg-white p-4">
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

            <div class="pt-2">
                <InputLabel value="Links" />

                <TransitionGroup name="row" tag="div" class="mt-2 divide-y divide-ink/10 sm:space-y-2 sm:divide-y-0">
                    <div
                        v-for="(link, i) in form.links"
                        :key="link.id"
                        class="flex flex-wrap items-stretch gap-2 py-3 first:pt-0 sm:py-0"
                    >
                        <TextInput
                            v-model="link.label"
                            :placeholder="linkLabelPlaceholder(link)"
                            class="order-1 min-w-0 flex-1 sm:w-1/3 sm:flex-none"
                        />
                        <IconButton label="Remove link" class="order-2 sm:order-3" @click="removeLink(i)">
                            <XMarkIcon class="h-4 w-4" />
                        </IconButton>
                        <TextInput
                            v-model="link.url"
                            placeholder="https://..."
                            class="order-3 min-w-0 w-full sm:order-2 sm:w-auto sm:flex-1"
                        />
                    </div>
                </TransitionGroup>

                <AddRowButton class="mt-3" @click="addLink">Add link</AddRowButton>
            </div>
        </div>
    </div>
</template>
