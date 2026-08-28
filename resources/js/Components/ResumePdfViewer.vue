<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';
import axios from 'axios';
import { isBlank } from '@/utils/isBlank';
import { renderPdfContent } from '@/utils/pdfCanvasRenderer';

const PREVIEW_DEBOUNCE_MS = 600;

const props = defineProps({
    payload: {
        type: Object,
        required: true,
    },
});

const containerRef = ref(null);
const status = ref('loading'); // loading | ready | empty | error

let debounceTimer = null;
let abortController = null;
let renderToken = 0;

function schedulePreview() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(generatePreview, PREVIEW_DEBOUNCE_MS);
}

function clearContainer() {
    if (containerRef.value) {
        containerRef.value.innerHTML = '';
    }
}

async function generatePreview() {
    abortController?.abort();
    
    if (isBlank(props.payload)) {
        renderToken += 1;
        clearContainer();
        status.value = 'empty';
        return;
    }

    abortController = new AbortController();
    const token = ++renderToken;

    status.value = 'loading';

    try {
        const response = await axios.post(route('resumes.preview'), props.payload, {
            responseType: 'arraybuffer',
            signal: abortController.signal,
        });

        if (token === renderToken) {
            await renderPdf(response.data, token);
        }
    } catch (error) {
        if (axios.isCancel(error) || token !== renderToken) {
            return;
        }

        clearContainer();
        status.value = error.response?.status === 422 ? 'empty' : 'error';
    }
}

async function renderPdf(data, token) {
    if (!containerRef.value) {
        return;
    }

    const pages = await renderPdfContent(data, containerRef.value.clientWidth);

    if (token !== renderToken || !containerRef.value) {
        return;
    }

    containerRef.value.innerHTML = '';
    pages.forEach((canvas) => containerRef.value.appendChild(canvas));
    status.value = 'ready';
}

watch(() => props.payload, schedulePreview, { deep: true, immediate: true });

onBeforeUnmount(() => {
    clearTimeout(debounceTimer);
    abortController?.abort();
});
</script>

<template>
    <div class="relative min-h-[600px]">
        <div ref="containerRef" class="space-y-4"></div>

        <div
            v-if="status === 'empty'"
            class="flex aspect-[210/297] items-center justify-center border border-dashed border-ink/20 text-center text-sm text-ink/50"
        >
            Fill in the form to see the preview.
        </div>

        <div
            v-else-if="status === 'error'"
            class="flex aspect-[210/297] items-center justify-center border border-dashed border-ink/20 text-center text-sm text-ink/50"
        >
            Could not generate the preview. Your changes are still saved in the form.
        </div>

        <div
            v-if="status === 'loading'"
            class="pointer-events-none absolute inset-x-0 top-0 flex justify-center pt-4"
        >
            <span class="rounded-full border border-ink/20 bg-paper px-3 py-1 text-xs text-ink/60">
                Updating preview…
            </span>
        </div>
    </div>
</template>
