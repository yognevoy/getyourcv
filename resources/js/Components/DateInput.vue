<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: 'Select month',
    },
    presentable: {
        type: Boolean,
        default: false,
    },
});

const model = defineModel({
    type: String,
    default: '',
});

const current = defineModel('current', {
    type: Boolean,
    default: false,
});

const MONTHS_FULL = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
];
const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

const open = ref(false);

const selected = computed(() => {
    if (!model.value) {
        return null;
    }

    const [year, month] = model.value.split('-').map(Number);

    return { year, month };
});

const viewYear = ref(selected.value?.year ?? new Date().getFullYear());

const label = computed(() => {
    if (props.presentable && current.value) {
        return 'Present';
    }

    return selected.value ? `${MONTHS_FULL[selected.value.month - 1]} ${selected.value.year}` : '';
});

function toggle() {
    if (props.disabled) {
        return;
    }

    viewYear.value = selected.value?.year ?? new Date().getFullYear();
    open.value = !open.value;
}

function pickMonth(monthIndex) {
    current.value = false;
    model.value = `${viewYear.value}-${String(monthIndex + 1).padStart(2, '0')}-01`;
    open.value = false;
}

function pickPresent() {
    current.value = true;
    open.value = false;
}

function clear() {
    model.value = '';
    current.value = false;
    open.value = false;
}
</script>

<template>
    <div class="relative">
        <button
            type="button"
            class="flex w-full items-center justify-between gap-2 rounded-md border border-ink/20 px-3 py-2 text-left text-ink transition-colors focus:border-ink focus:outline-none disabled:border-ink/10 disabled:bg-ink/5 disabled:text-ink/40"
            :disabled="disabled"
            @click="toggle"
        >
            <span :class="label ? 'text-ink' : 'text-ink/40'">{{ label || placeholder }}</span>
            <svg class="h-3.5 w-3.5 shrink-0 text-ink/40" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <div v-show="open" class="fixed inset-0 z-40" @click="open = false"></div>

        <Transition
            enter-active-class="transition ease-out duration-150"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 w-64 origin-top rounded-md border border-ink/20 bg-paper p-3"
                style="display: none"
            >
                <div class="mb-2 flex items-center justify-between">
                    <button
                        type="button"
                        class="flex h-7 w-7 items-center justify-center rounded-md text-ink/50 transition-colors hover:text-ink"
                        aria-label="Previous year"
                        @click="viewYear -= 1"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M10 3L5 8l5 5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <span class="text-sm font-medium text-ink">{{ viewYear }}</span>

                    <button
                        type="button"
                        class="flex h-7 w-7 items-center justify-center rounded-md text-ink/50 transition-colors hover:text-ink"
                        aria-label="Next year"
                        @click="viewYear += 1"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 3l5 5-5 5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-4 gap-1">
                    <button
                        v-for="(monthLabel, index) in MONTHS_SHORT"
                        :key="monthLabel"
                        type="button"
                        class="rounded-md border border-transparent py-1.5 text-sm text-ink/70 transition-colors hover:border-ink/20"
                        :class="{
                            'border-ink bg-ink text-paper hover:border-ink':
                                !current && selected && selected.year === viewYear && selected.month === index + 1,
                        }"
                        @click="pickMonth(index)"
                    >
                        {{ monthLabel }}
                    </button>
                </div>

                <div v-if="presentable || model || current" class="mt-2 flex gap-1">
                    <button
                        v-if="presentable"
                        type="button"
                        class="flex-1 rounded-md border py-1.5 text-xs transition-colors"
                        :class="
                            current
                                ? 'border-ink bg-ink text-paper'
                                : 'border-dashed border-ink/30 text-ink/60 hover:border-ink hover:text-ink'
                        "
                        @click="pickPresent"
                    >
                        Present
                    </button>

                    <button
                        v-if="model || current"
                        type="button"
                        class="flex-1 rounded-md border border-dashed border-ink/30 py-1.5 text-xs text-ink/60 transition-colors hover:border-ink hover:text-ink"
                        @click="clear"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
