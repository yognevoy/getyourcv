<script setup>
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import { Cropper, CircleStencil } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import { ArrowPathIcon, ArrowUpTrayIcon, MagnifyingGlassMinusIcon, MagnifyingGlassPlusIcon } from '@heroicons/vue/20/solid';
import Modal from '@/Components/Modal.vue';
import CloseButton from '@/Components/CloseButton.vue';
import IconButton from '@/Components/IconButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Spinner from '@/Components/Spinner.vue';
import RangeSlider from '@/Components/RangeSlider.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    editPhotoUrl: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['close', 'uploaded']);

const OUTPUT_SIZE = 640;

const fileInput = ref(null);
const cropperRef = ref(null);
const isDraggingOver = ref(false);
const imageSrc = ref(null);
const originalImageSrc = ref(null);
const uploading = ref(false);
const error = ref(null);

const brightness = ref(100);
const contrast = ref(100);
const saturation = ref(100);
const savedCoordinates = ref(null);

const filterStyle = computed(
    () => `brightness(${brightness.value}%) contrast(${contrast.value}%) saturate(${saturation.value}%)`,
);

watch(
    () => props.show,
    (show) => {
        if (show) {
            if (props.editPhotoUrl) {
                imageSrc.value = originalImageSrc.value || props.editPhotoUrl;
            } else {
                imageSrc.value = null;
                originalImageSrc.value = null;
            }
        } else {
            reset();
        }
    },
);

function reset() {
    imageSrc.value = null;
    uploading.value = false;
    error.value = null;
}

function onFilesSelected(files) {
    const file = files?.[0];
    if (!file || !file.type.startsWith('image/')) {
        return;
    }

    if (originalImageSrc.value) {
        URL.revokeObjectURL(originalImageSrc.value);
    }

    originalImageSrc.value = URL.createObjectURL(file);
    imageSrc.value = originalImageSrc.value;
    brightness.value = 100;
    contrast.value = 100;
    saturation.value = 100;
    savedCoordinates.value = null;
}

function onFileInputChange(event) {
    onFilesSelected(event.target.files);
    event.target.value = '';
}

function onDrop(event) {
    isDraggingOver.value = false;
    onFilesSelected(event.dataTransfer.files);
}

function zoomIn() {
    cropperRef.value?.zoom(1.2);
}

function zoomOut() {
    cropperRef.value?.zoom(1 / 1.2);
}

function onCropperChange(result) {
    savedCoordinates.value = result.coordinates;
}

function onCropperReady() {
    if (savedCoordinates.value) {
        cropperRef.value?.setCoordinates(savedCoordinates.value);
    }
}

function chooseAnother() {
    imageSrc.value = null;
    brightness.value = 100;
    contrast.value = 100;
    saturation.value = 100;
    savedCoordinates.value = null;
}

async function save() {
    const result = cropperRef.value?.getResult();
    if (!result?.canvas) {
        return;
    }

    const output = document.createElement('canvas');
    output.width = OUTPUT_SIZE;
    output.height = OUTPUT_SIZE;

    const ctx = output.getContext('2d');
    ctx.filter = filterStyle.value;
    ctx.drawImage(result.canvas, 0, 0, OUTPUT_SIZE, OUTPUT_SIZE);

    const blob = await new Promise((resolve) => output.toBlob(resolve, 'image/png'));
    if (!blob) {
        error.value = 'Could not process the photo. Please try again.';
        return;
    }

    uploading.value = true;
    error.value = null;

    const formData = new FormData();
    formData.append('photo', blob, 'photo.png');

    try {
        const response = await axios.post(route('resume-photos.store'), formData);

        emit('uploaded', {
            photoPath: response.data.photo_path,
            previewUrl: output.toDataURL('image/png'),
        });
        emit('close');
    } catch (e) {
        error.value = e.response?.data?.errors?.photo?.[0] || 'Could not upload the photo. Please try again.';
    } finally {
        uploading.value = false;
    }
}
</script>

<template>
    <Modal :show="show" max-width="2xl" @close="emit('close')">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-ink">Upload photo</h2>
                <CloseButton @click="emit('close')" />
            </div>

            <div v-if="!imageSrc" class="mt-6">
                <div
                    class="flex flex-col items-center justify-center gap-3 rounded-md border border-dashed px-6 py-12 text-center transition-colors"
                    :class="isDraggingOver ? 'border-ink bg-ink/5' : 'border-ink/20'"
                    @dragover.prevent="isDraggingOver = true"
                    @dragleave.prevent="isDraggingOver = false"
                    @drop.prevent="onDrop"
                >
                    <ArrowUpTrayIcon class="h-6 w-6 text-ink/40" />
                    <p class="text-sm text-ink/60">Drag and drop a photo here</p>
                    <button type="button" class="text-sm text-ink underline-offset-2 hover:underline" @click="fileInput.click()">
                        or choose a file
                    </button>
                    <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileInputChange" />
                </div>
            </div>

            <div v-else class="mt-6">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                    <div class="mx-auto flex flex-col items-center gap-3 sm:mx-0">
                        <div class="h-80 w-80 shrink-0 overflow-hidden rounded-md border border-ink/15" :style="{ filter: filterStyle }">
                            <Cropper
                                ref="cropperRef"
                                class="h-full w-full"
                                :src="imageSrc"
                                :stencil-component="CircleStencil"
                                :stencil-props="{ aspectRatio: 1 }"
                                image-restriction="stencil"
                                :transitions="false"
                                @change="onCropperChange"
                                @ready="onCropperReady"
                            />
                        </div>

                        <div class="flex items-center gap-2">
                            <IconButton label="Zoom out" @click="zoomOut"><MagnifyingGlassMinusIcon class="h-4 w-4" /></IconButton>
                            <IconButton label="Zoom in" @click="zoomIn"><MagnifyingGlassPlusIcon class="h-4 w-4" /></IconButton>
                            <IconButton label="Choose another photo" @click="chooseAnother"><ArrowPathIcon class="h-4 w-4" /></IconButton>
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-4 sm:w-56">
                        <p class="text-xs font-medium uppercase tracking-wide text-ink/60">Adjust</p>
                        <RangeSlider v-model="brightness" label="Brightness" :min="50" :max="150" />
                        <RangeSlider v-model="contrast" label="Contrast" :min="50" :max="150" />
                        <RangeSlider v-model="saturation" label="Saturation" :min="0" :max="200" />
                    </div>
                </div>

                <p v-if="error" class="mt-4 text-sm text-ink/60">{{ error }}</p>

                <div class="mt-6 flex items-center justify-end">
                    <PrimaryButton type="button" :disabled="uploading" @click="save">
                        <Spinner v-if="uploading" size="sm" class="mr-2" />
                        Save
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
