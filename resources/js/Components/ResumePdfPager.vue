<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    pages: {
        type: Array,
        required: true,
    },
});

const currentPage = ref(0);

function mountCanvas(el, canvas) {
    if (el && canvas && el.firstChild !== canvas) {
        el.innerHTML = '';
        el.appendChild(canvas);
    }
}

function goToPage(index) {
    currentPage.value = Math.min(Math.max(index, 0), props.pages.length - 1);
}

function onKeydown(event) {
    if (event.key === 'ArrowRight') {
        goToPage(currentPage.value + 1);
    } else if (event.key === 'ArrowLeft') {
        goToPage(currentPage.value - 1);
    }
}

watch(
    () => props.pages,
    (pages) => {
        currentPage.value = Math.min(currentPage.value, Math.max(pages.length - 1, 0));
    },
);
</script>

<template>
    <div class="group relative" tabindex="0" @keydown="onKeydown">
        <div :ref="(el) => mountCanvas(el, pages[currentPage])"></div>

        <button
            type="button"
            class="absolute inset-y-0 left-0 flex w-12 items-center justify-center text-3xl leading-none text-ink/70 opacity-0 transition-opacity group-hover:opacity-100 disabled:hidden"
            :disabled="currentPage === 0"
            aria-label="Previous page"
            @click="goToPage(currentPage - 1)"
        >
            &lsaquo;
        </button>
        <button
            type="button"
            class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-3xl leading-none text-ink/70 opacity-0 transition-opacity group-hover:opacity-100 disabled:hidden"
            :disabled="currentPage === pages.length - 1"
            aria-label="Next page"
            @click="goToPage(currentPage + 1)"
        >
            &rsaquo;
        </button>

        <span
            v-if="pages.length > 1"
            class="pointer-events-none absolute bottom-2 right-2 text-[11px] tabular-nums text-ink/40"
        >
            {{ currentPage + 1 }}/{{ pages.length }}
        </span>
    </div>
</template>
