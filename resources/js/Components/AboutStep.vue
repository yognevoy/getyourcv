<script setup>
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import IconButton from '@/Components/IconButton.vue';
import AiSparkleIcon from '@/Components/AiSparkleIcon.vue';
import AiRewriteDialog from '@/Components/AiRewriteDialog.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const showAiDialog = ref(false);

function applyRewrite(text) {
    props.form.about = text;
}
</script>

<template>
    <div>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-semibold text-ink">About</h2>
            <IconButton
                label="AI enhance"
                :disabled="!form.about || form.about.trim().length < 3"
                @click="showAiDialog = true"
            >
                <AiSparkleIcon />
            </IconButton>
        </div>

        <div class="rounded-md border border-ink/15 bg-white p-4">
            <InputLabel for="about" value="Summary" />
            <textarea
                id="about"
                v-model="form.about"
                rows="10"
                placeholder="A couple of sentences about you as a professional."
                class="mt-1 w-full rounded-md border border-ink/20 px-3 py-2 text-ink transition-colors focus:border-ink focus:outline-none"
            />
            <InputError class="mt-1" :message="form.errors.about" />
        </div>

        <AiRewriteDialog
            :show="showAiDialog"
            :text="form.about || ''"
            target="about"
            @close="showAiDialog = false"
            @apply="applyRewrite"
        />
    </div>
</template>
