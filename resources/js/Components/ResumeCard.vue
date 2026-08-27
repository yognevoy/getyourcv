<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import MenuIcon from '@/Components/MenuIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { showToast } from '@/composables/useToast';
import { formatRelativeDate } from '@/utils/formatRelativeDate';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
});

const stubActions = ['Download PDF', 'Versions'];

const confirmingDelete = ref(false);
const deleting = ref(false);

const confirmingArchive = ref(false);
const archiving = ref(false);
const unarchiving = ref(false);

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

function archiveResume() {
    archiving.value = true;

    router.post(route('resumes.archive', props.resume.id), {}, {
        preserveScroll: true,
        onSuccess: () => showToast('Resume archived'),
        onFinish: () => {
            archiving.value = false;
            confirmingArchive.value = false;
        },
    });
}

function unarchiveResume() {
    unarchiving.value = true;

    router.post(route('resumes.unarchive', props.resume.id), {}, {
        preserveScroll: true,
        onSuccess: () => showToast('Resume unarchived'),
        onFinish: () => {
            unarchiving.value = false;
        },
    });
}
</script>

<template>
    <div
        class="group relative flex aspect-[3/4] cursor-pointer flex-col rounded-md border border-ink/25 p-5 transition-colors hover:border-ink focus:outline-none focus-visible:border-ink focus-visible:ring-2 focus-visible:ring-ink focus-visible:ring-offset-2"
        tabindex="0"
        role="button"
        :aria-label="`Edit ${resume.title}`"
        @click="openEdit"
        @keydown.enter.prevent="openEdit"
        @keydown.space.prevent="openEdit"
    >
        <span
            class="pointer-events-none absolute right-0 top-0 h-6 w-6 scale-0 rounded-tr-md bg-ink opacity-0 transition-all duration-150 group-hover:scale-100 group-hover:opacity-100"
            style="clip-path: polygon(100% 0, 100% 100%, 0 0)"
        ></span>

        <div class="flex flex-1 flex-col gap-3">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-base font-semibold text-ink">
                        {{ resume.title }}
                    </p>
                    <p class="truncate text-sm text-ink/50">
                        {{ resume.position || resume.full_name }}
                    </p>
                </div>

                <Dropdown align="left" width="48" @click.stop>
                    <template #trigger>
                        <button
                            type="button"
                            aria-label="Resume actions"
                            class="flex h-8 w-8 shrink-0 items-center justify-center text-ink/50 opacity-0 transition hover:text-ink focus:opacity-100 focus-visible:opacity-100 group-hover:opacity-100 group-focus-within:opacity-100"
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
                            class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none"
                            @click="copyLink"
                        >
                            Get link
                        </button>
                        <button
                            v-if="resume.status !== 'archived'"
                            type="button"
                            class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none"
                            @click="confirmingArchive = true"
                        >
                            Archive
                        </button>
                        <button
                            v-else
                            type="button"
                            :disabled="unarchiving"
                            class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none disabled:cursor-not-allowed disabled:text-ink/30"
                            @click="unarchiveResume"
                        >
                            Unarchive
                        </button>
                        <button
                            v-for="action in stubActions"
                            :key="action"
                            type="button"
                            disabled
                            class="block w-full cursor-not-allowed px-4 py-2 text-start text-sm leading-5 text-ink/30"
                        >
                            {{ action }}
                        </button>
                        <button
                            type="button"
                            class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none"
                            @click="confirmingDelete = true"
                        >
                            Delete
                        </button>
                    </template>
                </Dropdown>
            </div>

            <p v-if="resume.about" class="line-clamp-4 text-sm text-ink/60">
                {{ resume.about }}
            </p>
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

        <ConfirmDialog
            :show="confirmingArchive"
            title="Archive this resume?"
            :message="`“${resume.title}” will be hidden from public view.`"
            confirm-label="Archive"
            :processing="archiving"
            @click.stop
            @confirm="archiveResume"
            @cancel="confirmingArchive = false"
        />

        <div class="flex items-center justify-between">
            <StatusBadge :status="resume.status" />
            <span class="text-xs text-ink/40" :title="new Date(resume.updated_at).toLocaleDateString()">
                {{ formatRelativeDate(resume.updated_at) }}
            </span>
        </div>
    </div>
</template>
