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
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <InputLabel for="about" value="About" />
            <IconButton
                label="AI enhance"
                :disabled="!form.about || form.about.trim().length < 3"
                @click="showAiDialog = true"
            >
                <AiSparkleIcon />
            </IconButton>
        </div>
        <textarea
            id="about"
            v-model="form.about"
            rows="10"
            placeholder="A couple of sentences about you as a professional."
            class="w-full rounded-md border border-ink/20 px-3 py-2 text-ink transition-colors focus:border-ink focus:outline-none"
        />
        <InputError :message="form.errors.about" />

        <AiRewriteDialog
            :show="showAiDialog"
            :text="form.about || ''"
            target="about"
            @close="showAiDialog = false"
            @apply="applyRewrite"
        />
    </div>
</template>
