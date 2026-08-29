<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import Spinner from '@/Components/Spinner.vue';
import { renderPdfContent } from '@/utils/pdfCanvasRenderer';

const props = defineProps({
    src: {
        type: String,
        required: true,
    },
});

const containerRef = ref(null);
const status = ref('loading'); // loading | ready | error

onMounted(async () => {
    try {
        const response = await axios.get(props.src, { responseType: 'arraybuffer' });
        const pages = await renderPdfContent(response.data, containerRef.value.clientWidth);

        containerRef.value.innerHTML = '';
        pages.forEach((canvas) => containerRef.value.appendChild(canvas));
        status.value = 'ready';
    } catch {
        status.value = 'error';
    }
});
</script>

<template>
    <div class="relative min-h-[600px]">
        <div ref="containerRef" class="space-y-4"></div>

        <div
            v-if="status === 'error'"
            class="flex aspect-[210/297] items-center justify-center border border-dashed border-ink/20 bg-white text-center text-sm text-ink/50"
        >
            Could not load this resume.
        </div>

        <div
            v-else-if="status === 'loading'"
            class="flex aspect-[210/297] flex-col items-center justify-center gap-3 border border-dashed border-ink/20 bg-white text-sm text-ink/40"
        >
            <Spinner size="lg" />
            <span>Loading resume…</span>
        </div>
    </div>
</template>
