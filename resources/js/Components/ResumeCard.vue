<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import MenuIcon from '@/Components/MenuIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    resume: {
        type: Object,
        required: true,
    },
});

const menuActions = [
    'Edit',
    'Duplicate',
    'Get link',
    'Download PDF',
    'Delete',
    'Archive',
    'Versions',
];
</script>

<template>
    <div class="group relative flex aspect-[3/4] flex-col justify-between border border-black p-5">
        <span
            class="pointer-events-none absolute right-0 top-0 h-6 w-6 scale-0 bg-black opacity-0 transition-all duration-150 group-hover:scale-100 group-hover:opacity-100"
            style="clip-path: polygon(100% 0, 100% 100%, 0 0)"
        ></span>

        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-base font-semibold text-black">
                    {{ resume.title }}
                </p>
                <p class="truncate text-sm text-black/50">
                    {{ resume.position || resume.full_name }}
                </p>
            </div>

            <Dropdown align="left" width="48">
                <template #trigger>
                    <button
                        type="button"
                        aria-label="Resume actions"
                        class="flex h-8 w-8 shrink-0 items-center justify-center text-black/50 transition-colors hover:text-black"
                    >
                        <MenuIcon />
                    </button>
                </template>

                <template #content>
                    <button
                        v-for="action in menuActions"
                        :key="action"
                        type="button"
                        disabled
                        class="block w-full cursor-not-allowed px-4 py-2 text-start text-sm leading-5 text-black/30"
                    >
                        {{ action }}
                    </button>
                </template>
            </Dropdown>
        </div>

        <div class="flex items-center justify-between">
            <StatusBadge :status="resume.status" />
            <span class="text-xs text-black/40">
                {{ new Date(resume.updated_at).toLocaleDateString() }}
            </span>
        </div>
    </div>
</template>
