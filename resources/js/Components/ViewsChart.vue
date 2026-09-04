<script setup>
import { computed, ref } from 'vue';
import { formatDate } from '@/utils/formatDate';

const props = defineProps({
    dailyViews: {
        type: Array,
        required: true,
    },
});

const CHART_HEIGHT = 140;
const PERIODS = [7, 30, 60];

const selectedPeriod = ref(30);
const zoomRange = ref(null);

const visibleStartIndex = computed(() => (zoomRange.value ? zoomRange.value.start : props.dailyViews.length - selectedPeriod.value));

const visibleDays = computed(() => {
    if (zoomRange.value) {
        return props.dailyViews.slice(zoomRange.value.start, zoomRange.value.end + 1);
    }

    return props.dailyViews.slice(-selectedPeriod.value);
});

const visibleTotal = computed(() => visibleDays.value.reduce((sum, day) => sum + day.views, 0));

const maxDailyViews = computed(() => Math.max(1, ...visibleDays.value.map((day) => day.views)));

const barColWidth = computed(() => `${100 / visibleDays.value.length}%`);

function barHeight(views) {
    return `${Math.max(2, Math.round((views / maxDailyViews.value) * CHART_HEIGHT))}px`;
}

function selectPeriod(period) {
    selectedPeriod.value = period;
    zoomRange.value = null;
}

function resetZoom() {
    zoomRange.value = null;
}

const chartHeading = computed(() => {
    if (zoomRange.value) {
        return `Views, ${formatDate(visibleDays.value[0].date)} - ${formatDate(visibleDays.value[visibleDays.value.length - 1].date)}`;
    }

    return `Views, last ${selectedPeriod.value} days`;
});

const barsEl = ref(null);
const isDragging = ref(false);
const dragStartLocal = ref(null);
const dragEndLocal = ref(null);

function localIndexFromEvent(event) {
    const rect = barsEl.value.getBoundingClientRect();
    const ratio = (event.clientX - rect.left) / rect.width;
    const index = Math.floor(ratio * visibleDays.value.length);

    return Math.min(visibleDays.value.length - 1, Math.max(0, index));
}

function onPointerDown(event) {
    isDragging.value = true;
    const index = localIndexFromEvent(event);
    dragStartLocal.value = index;
    dragEndLocal.value = index;

    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp, { once: true });
}

function onPointerMove(event) {
    dragEndLocal.value = localIndexFromEvent(event);
}

function onPointerUp() {
    window.removeEventListener('pointermove', onPointerMove);
    isDragging.value = false;

    const start = Math.min(dragStartLocal.value, dragEndLocal.value);
    const end = Math.max(dragStartLocal.value, dragEndLocal.value);

    if (end > start) {
        const offset = visibleStartIndex.value;
        zoomRange.value = { start: offset + start, end: offset + end };
    }

    dragStartLocal.value = null;
    dragEndLocal.value = null;
}

function isColumnSelected(localIndex) {
    if (!isDragging.value || dragStartLocal.value === null) {
        return false;
    }

    const start = Math.min(dragStartLocal.value, dragEndLocal.value);
    const end = Math.max(dragStartLocal.value, dragEndLocal.value);

    return localIndex >= start && localIndex <= end;
}
</script>

<template>
    <div class="border-t border-ink/10 p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Transition name="fade" mode="out-in">
                <h2 :key="chartHeading" class="text-sm font-semibold text-ink">{{ chartHeading }}</h2>
            </Transition>

            <div class="flex items-center gap-3">
                <Transition name="fade">
                    <button
                        v-if="zoomRange"
                        type="button"
                        class="text-xs font-medium text-ink/50 hover:text-ink focus:outline-none"
                        @click="resetZoom"
                    >
                        Reset zoom
                    </button>
                </Transition>
                <div class="flex rounded-md border border-ink/20 p-0.5 text-xs">
                    <button
                        v-for="period in PERIODS"
                        :key="period"
                        type="button"
                        class="rounded px-2.5 py-1 font-medium transition-colors focus:outline-none"
                        :class="!zoomRange && selectedPeriod === period ? 'bg-ink text-paper' : 'text-ink/60 hover:text-ink'"
                        @click="selectPeriod(period)"
                    >
                        {{ period }}d
                    </button>
                </div>
            </div>
        </div>

        <p class="mt-1 text-xs text-ink/40">Drag across the chart to zoom into a range.</p>

        <div
            ref="barsEl"
            class="relative mt-4 flex touch-none select-none items-end gap-0.5 overflow-hidden"
            :style="{ height: `${CHART_HEIGHT}px` }"
            role="img"
            :aria-label="`Daily views from ${formatDate(visibleDays[0].date, { withYear: true })} to ${formatDate(visibleDays[visibleDays.length - 1].date, { withYear: true })}, totaling ${visibleTotal}`"
            @pointerdown="onPointerDown"
        >
            <TransitionGroup name="bar-col">
                <div
                    v-for="(day, index) in visibleDays"
                    :key="day.date"
                    class="flex h-full shrink-0 items-end justify-center rounded transition-colors"
                    :class="{ 'bg-ink/10': isColumnSelected(index) }"
                    :style="{ width: barColWidth }"
                    :title="`${formatDate(day.date, { withYear: true })}: ${day.views} view${day.views === 1 ? '' : 's'}`"
                >
                    <div
                        class="w-full max-w-[18px] rounded-t bg-ink transition-all duration-300 ease-out hover:bg-ink/60"
                        :style="{ height: barHeight(day.views) }"
                    ></div>
                </div>
            </TransitionGroup>
        </div>
        <div class="mt-2 flex justify-between text-xs text-ink/40">
            <Transition name="fade" mode="out-in">
                <span :key="visibleDays[0].date">{{ formatDate(visibleDays[0].date) }}</span>
            </Transition>
            <Transition name="fade" mode="out-in">
                <span :key="visibleDays[visibleDays.length - 1].date">{{ formatDate(visibleDays[visibleDays.length - 1].date) }}</span>
            </Transition>
        </div>

        <table class="sr-only">
            <caption>{{ chartHeading }}</caption>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Views</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="day in visibleDays" :key="day.date">
                    <td>{{ day.date }}</td>
                    <td>{{ day.views }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.bar-col-move {
    transition: transform 300ms ease;
}

.bar-col-enter-active,
.bar-col-leave-active {
    transition: opacity 200ms ease;
}

.bar-col-enter-from,
.bar-col-leave-to {
    opacity: 0;
}

.bar-col-leave-active {
    position: absolute;
}
</style>
