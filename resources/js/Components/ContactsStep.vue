<script setup>
import { ref } from 'vue';
import { CameraIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import draggable from 'vuedraggable';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import IconButton from '@/Components/IconButton.vue';
import AddRowButton from '@/Components/AddRowButton.vue';
import DragHandle from '@/Components/DragHandle.vue';
import PhotoUploadModal from '@/Components/PhotoUploadModal.vue';
import { useRowIds } from '@/composables/useRowIds';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const showPhotoModal = ref(false);

function onPhotoUploaded({ photoPath, previewUrl }) {
    props.form.photo_path = photoPath;
    props.form.photo_preview_url = previewUrl;
}

function removePhoto() {
    props.form.photo_path = '';
    props.form.photo_preview_url = '';
}

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
            <div class="relative inline-block">
                <button
                    type="button"
                    class="flex h-20 w-20 flex-col items-center justify-center gap-1 overflow-hidden rounded-md border transition-colors"
                    :class="form.photo_preview_url ? 'border-ink/15' : 'border-dashed border-ink/25 hover:border-ink/50'"
                    @click="showPhotoModal = true"
                >
                    <img v-if="form.photo_preview_url" :src="form.photo_preview_url" alt="" class="h-full w-full object-cover" />
                    <CameraIcon v-else class="h-5 w-5 text-ink/40" />
                </button>
                <button
                    v-if="form.photo_preview_url"
                    type="button"
                    aria-label="Remove photo"
                    class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full border border-ink/20 bg-white text-ink/50 transition-colors hover:border-ink hover:bg-ink hover:text-paper"
                    @click="removePhoto"
                >
                    <XMarkIcon class="h-3 w-3" />
                </button>
            </div>

            <PhotoUploadModal
                :show="showPhotoModal"
                :edit-photo-url="form.photo_preview_url"
                @close="showPhotoModal = false"
                @uploaded="onPhotoUploaded"
            />

            <div>
                <InputLabel for="full_name" value="Full name" />
                <TextInput id="full_name" v-model="form.full_name" maxlength="28" class="mt-1 block w-full" required />
                <InputError class="mt-1" :message="form.errors.full_name" />
            </div>

            <div>
                <InputLabel for="position" value="Position" />
                <TextInput id="position" v-model="form.position" maxlength="28" class="mt-1 block w-full" />
                <InputError class="mt-1" :message="form.errors.position" />
            </div>

            <div>
                <InputLabel for="location" value="Location" />
                <TextInput id="location" v-model="form.location" maxlength="28" class="mt-1 block w-full" />
                <InputError class="mt-1" :message="form.errors.location" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" v-model="form.email" maxlength="36" class="mt-1 block w-full" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <InputLabel value="Links" />

                <draggable
                    v-model="form.links"
                    item-key="id"
                    handle=".drag-handle"
                    tag="div"
                    class="mt-2 divide-y divide-ink/10 sm:space-y-2 sm:divide-y-0"
                    :animation="150"
                    :force-fallback="true"
                >
                    <template #item="{ element: link, index: i }">
                        <div class="flex flex-wrap items-stretch gap-2 py-3 first:pt-0 sm:py-0">
                            <DragHandle />
                            <TextInput
                                v-model="link.label"
                                :placeholder="linkLabelPlaceholder(link)"
                                maxlength="20"
                                class="order-1 min-w-0 flex-1 sm:w-1/3 sm:flex-none"
                            />
                            <IconButton label="Remove link" class="order-2 sm:order-3" @click="removeLink(i)">
                                <XMarkIcon class="h-4 w-4" />
                            </IconButton>
                            <TextInput
                                v-model="link.url"
                                placeholder="https://..."
                                maxlength="255"
                                class="order-3 min-w-0 w-full sm:order-2 sm:w-auto sm:flex-1"
                            />
                        </div>
                    </template>
                </draggable>

                <AddRowButton v-if="form.links.length < 3" class="mt-3" @click="addLink">Add link</AddRowButton>
            </div>
        </div>
    </div>
</template>
