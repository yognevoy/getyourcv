<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import CloseButton from '@/Components/CloseButton.vue';
import Modal from '@/Components/Modal.vue';
import { showToast } from '@/composables/useToast';
import { formatRelativeDate } from '@/utils/formatRelativeDate';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    resumeId: {
        type: [Number, String],
        required: true,
    },
});

const emit = defineEmits(['close']);

const versions = ref([]);
const loading = ref(false);

const confirming = ref(null);
const processingId = ref(null);

const actions = {
    restore: {
        confirmMessage: 'Restore this version?',
        successMessage: 'Resume restored to this version',
        submit: (id, options) => router.post(route('resumes.versions.restore', [props.resumeId, id]), {}, options),
    },
    delete: {
        confirmMessage: 'Delete this version?',
        successMessage: 'Version deleted',
        submit: (id, options) => router.delete(route('resumes.versions.destroy', [props.resumeId, id]), options),
    },
};

watch(
    () => props.show,
    (show) => {
        if (show) {
            confirming.value = null;
            loadVersions();
        }
    },
);

async function loadVersions() {
    loading.value = true;

    try {
        const response = await axios.get(route('resumes.versions.index', props.resumeId));
        versions.value = response.data.versions;
    } finally {
        loading.value = false;
    }
}

function requestConfirm(id, action) {
    confirming.value = { id, action };
}

function confirmAction() {
    if (!confirming.value) {
        return;
    }

    const { id, action } = confirming.value;
    processingId.value = id;

    actions[action].submit(id, {
        preserveScroll: true,
        onSuccess: () => {
            showToast(actions[action].successMessage);
            loadVersions();
        },
        onFinish: () => {
            processingId.value = null;
            confirming.value = null;
        },
    });
}
</script>

<template>
    <Modal :show="show" max-width="md" :bordered="false" @close="emit('close')">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-ink">Versions</h2>
                <CloseButton @click="emit('close')" />
            </div>

            <p v-if="loading && !versions.length" class="mt-6 text-sm text-ink/50">
                Loading…
            </p>

            <p v-else-if="!loading && !versions.length" class="mt-6 text-sm text-ink/50">
                No versions yet.
            </p>

            <ul v-else class="mt-6 divide-y divide-ink/10">
                <li
                    v-for="version in versions"
                    :key="version.id"
                    class="flex items-center justify-between gap-3 py-3"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-ink" :title="new Date(version.created_at).toLocaleString()">
                            {{ formatRelativeDate(version.created_at) }}
                        </span>
                        <span
                            v-if="version.is_current"
                            class="rounded-full border border-ink px-2 py-0.5 text-xs text-ink"
                        >
                            Current
                        </span>
                    </div>

                    <div v-if="confirming?.id === version.id" class="flex items-center gap-3 text-sm">
                        <span class="text-ink/60">
                            {{ actions[confirming.action].confirmMessage }}
                        </span>
                        <button
                            type="button"
                            :disabled="processingId === version.id"
                            class="text-ink underline-offset-2 hover:underline disabled:cursor-not-allowed disabled:text-ink/30 disabled:no-underline"
                            @click="confirmAction"
                        >
                            Confirm
                        </button>
                        <button
                            type="button"
                            class="text-ink/50 underline-offset-2 hover:underline"
                            @click="confirming = null"
                        >
                            Cancel
                        </button>
                    </div>

                    <div v-else class="flex items-center gap-4 text-sm">
                        <a
                            :href="route('resumes.versions.pdf', [resumeId, version.id])"
                            target="_blank"
                            class="text-ink underline-offset-2 hover:underline"
                        >
                            View
                        </a>
                        <button
                            v-if="!version.is_current"
                            type="button"
                            class="text-ink underline-offset-2 hover:underline"
                            @click="requestConfirm(version.id, 'restore')"
                        >
                            Restore
                        </button>
                        <button
                            v-if="!version.is_current"
                            type="button"
                            class="text-ink underline-offset-2 hover:underline"
                            @click="requestConfirm(version.id, 'delete')"
                        >
                            Delete
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </Modal>
</template>
