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
    <div class="flex aspect-[3/4] flex-col justify-between border border-black p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-base font-semibold text-black">
                    {{ resume.title }}
                </p>
                <p class="truncate text-sm text-black/50">
                    {{ resume.position || resume.full_name }}
                </p>
            </div>

            <Dropdown align="right" width="48">
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
