<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import MenuIcon from '@/Components/MenuIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { showToast } from '@/composables/useToast';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
});

const stubActions = ['Download PDF', 'Archive', 'Versions'];

const confirmingDelete = ref(false);
const deleting = ref(false);

function openEdit() {
    router.visit(route('resumes.edit', props.resume.id));
}

async function copyLink() {
    const url = `${window.location.origin}/r/${props.resume.slug}`;

    try {
        await navigator.clipboard.writeText(url);
        showToast('Link copied to clipboard');
    } catch {
        showToast('Could not copy link');
    }
}

function deleteResume() {
    deleting.value = true;

    router.delete(route('resumes.destroy', props.resume.id), {
        preserveScroll: true,
        onSuccess: () => showToast('Resume moved to trash'),
        onFinish: () => {
            deleting.value = false;
            confirmingDelete.value = false;
        },
    });
}
</script>

<template>
    <div
        class="group relative flex aspect-[3/4] cursor-pointer flex-col justify-between border border-black p-5"
        @click="openEdit"
    >
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

            <Dropdown align="left" width="48" @click.stop>
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
                    <DropdownLink :href="route('resumes.edit', resume.id)">
                        Edit
                    </DropdownLink>
                    <DropdownLink :href="route('resumes.duplicate', resume.id)" method="post" as="button">
                        Duplicate
                    </DropdownLink>
                    <button
                        type="button"
                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-black transition duration-150 ease-in-out hover:bg-black/5 focus:bg-black/5 focus:outline-none"
                        @click="copyLink"
                    >
                        Get link
                    </button>
                    <button
                        v-for="action in stubActions"
                        :key="action"
                        type="button"
                        disabled
                        class="block w-full cursor-not-allowed px-4 py-2 text-start text-sm leading-5 text-black/30"
                    >
                        {{ action }}
                    </button>
                    <button
                        type="button"
                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-black transition duration-150 ease-in-out hover:bg-black/5 focus:bg-black/5 focus:outline-none"
                        @click="confirmingDelete = true"
                    >
                        Delete
                    </button>
                </template>
            </Dropdown>
        </div>

        <ConfirmDialog
            :show="confirmingDelete"
            title="Move this resume to trash?"
            :message="`“${resume.title}” will be moved to the trash. You can restore it later.`"
            confirm-label="Delete"
            danger
            :processing="deleting"
            @click.stop
            @confirm="deleteResume"
            @cancel="confirmingDelete = false"
        />

        <div class="flex items-center justify-between">
            <StatusBadge :status="resume.status" />
            <span class="text-xs text-black/40">
                {{ new Date(resume.updated_at).toLocaleDateString() }}
            </span>
        </div>
    </div>
</template>
