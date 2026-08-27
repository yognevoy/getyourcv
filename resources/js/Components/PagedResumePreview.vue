<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import ResumeTemplate from '@/Shared/ResumeTemplate.vue';
import IconButton from '@/Components/IconButton.vue';
import ChevronIcon from '@/Components/ChevronIcon.vue';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
});

const maxWidthClass = computed(() => {
    return {
        '2xl': 'max-w-2xl',
        '3xl': 'max-w-3xl',
    }[props.maxWidth];
});

// A4 proportions (210x297mm). Page height is derived from the container's own
// rendered width via ResizeObserver, so the page stays a fixed *sheet* instead
// of growing with the resume content - overflow is paginated, not stretched.
const pageEl = ref(null);
const contentEl = ref(null);
const pageHeight = ref(0);
const contentHeight = ref(0);
const pageIndex = ref(0);

const pageCount = computed(() => Math.max(1, Math.ceil(contentHeight.value / (pageHeight.value || 1))));

watch(pageCount, (count) => {
    if (pageIndex.value > count - 1) {
        pageIndex.value = count - 1;
    }
});

let observer = null;

onMounted(() => {
    observer = new ResizeObserver((entries) => {
        for (const entry of entries) {
            if (entry.target === pageEl.value) {
                pageHeight.value = entry.contentRect.height;
            } else if (entry.target === contentEl.value) {
                contentHeight.value = entry.contentRect.height;
            }
        }
    });

    observer.observe(pageEl.value);
    observer.observe(contentEl.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
});

function prevPage() {
    if (pageIndex.value > 0) {
        pageIndex.value -= 1;
    }
}

function nextPage() {
    if (pageIndex.value < pageCount.value - 1) {
        pageIndex.value += 1;
    }
}
</script>

<template>
    <div>
        <div
            class="relative flex w-full flex-col overflow-hidden border border-ink/10 bg-white xl:max-h-[75vh]"
            :class="maxWidthClass"
        >
            <div class="scrollbar-hidden min-h-0 flex-1 xl:overflow-y-auto">
                <div
                    ref="pageEl"
                    class="relative w-full overflow-hidden"
                    style="aspect-ratio: 210 / 297"
                >
                    <div
                        ref="contentEl"
                        class="transition-transform duration-200 ease-out"
                        :style="{ transform: `translateY(-${pageIndex * pageHeight}px)` }"
                    >
                        <ResumeTemplate :resume="resume" :bordered="false" />
                    </div>
                </div>
            </div>
        </div>

        <div v-if="pageCount > 1" class="mt-3 flex items-center justify-center gap-4">
            <IconButton label="Previous page" :disabled="pageIndex === 0" @click="prevPage">
                <ChevronIcon />
            </IconButton>
            <span class="text-xs text-ink/50">Page {{ pageIndex + 1 }} of {{ pageCount }}</span>
            <IconButton label="Next page" :disabled="pageIndex === pageCount - 1" @click="nextPage">
                <ChevronIcon class="rotate-180" />
            </IconButton>
        </div>
    </div>
</template>

<style scoped>
/* Keep the preview scrollable while hiding the scrollbar chrome itself. */
.scrollbar-hidden {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.scrollbar-hidden::-webkit-scrollbar {
    display: none;
}
</style>
