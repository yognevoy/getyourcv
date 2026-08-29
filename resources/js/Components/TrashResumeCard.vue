<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import Dropdown from '@/Components/Dropdown.vue';
import MenuIcon from '@/Components/MenuIcon.vue';
import { showToast } from '@/composables/useToast';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
});

const confirmingForceDelete = ref(false);
const deleting = ref(false);

function restore() {
    router.post(route('resumes.restore', props.resume.id), {}, {
        preserveScroll: true,
        onSuccess: () => showToast('Resume restored'),
    });
}

function forceDelete() {
    deleting.value = true;

    router.delete(route('resumes.force-destroy', props.resume.id), {
        preserveScroll: true,
        onSuccess: () => showToast('Resume permanently deleted'),
        onFinish: () => {
            deleting.value = false;
            confirmingForceDelete.value = false;
        },
    });
}
</script>

<template>
    <div class="group relative flex aspect-[3/4] flex-col justify-between rounded-md border border-ink/25 p-5 transition-colors hover:border-ink">
        <span
            class="pointer-events-none absolute right-0 top-0 h-6 w-6 scale-0 rounded-tr-md bg-ink opacity-0 transition-all duration-150 group-hover:scale-100 group-hover:opacity-100"
            style="clip-path: polygon(100% 0, 100% 100%, 0 0)"
        ></span>

        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-base font-semibold text-ink">
                    {{ resume.title }}
                </p>
                <p class="truncate text-sm text-ink/50">
                    {{ resume.position || resume.full_name }}
                </p>
            </div>

            <Dropdown align="left" width="48">
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
                    <button
                        type="button"
                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none"
                        @click="restore"
                    >
                        Restore
                    </button>
                    <button
                        type="button"
                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-ink transition duration-150 ease-in-out hover:bg-ink/5 focus:bg-ink/5 focus:outline-none"
                        @click="confirmingForceDelete = true"
                    >
                        Delete permanently
                    </button>
                </template>
            </Dropdown>
        </div>

        <ConfirmDialog
            :show="confirmingForceDelete"
            title="Permanently delete this resume?"
            :message="`“${resume.title}” will be permanently deleted. This cannot be undone.`"
            confirm-label="Delete permanently"
            danger
            :processing="deleting"
            @confirm="forceDelete"
            @cancel="confirmingForceDelete = false"
        />

        <p class="text-xs text-ink/40">
            Deleted on {{ new Date(resume.deleted_at).toLocaleDateString() }}
        </p>
    </div>
</template>
