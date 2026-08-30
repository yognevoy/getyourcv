<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
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
    <div class="space-y-4">
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
                <div v-for="(link, i) in form.links" :key="link.id" class="flex items-stretch gap-2">
                    <TextInput v-model="link.label" :placeholder="linkLabelPlaceholder(link)" class="w-1/3" />
                    <TextInput v-model="link.url" placeholder="https://..." class="flex-1" />
                    <IconButton label="Remove link" @click="removeLink(i)">
                        <RemoveIcon />
                    </IconButton>
                </div>
            </TransitionGroup>

            <AddRowButton @click="addLink">Add link</AddRowButton>
        </div>
    </div>
</template>
