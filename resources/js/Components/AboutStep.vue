<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    form: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="space-y-4">
        <InputLabel for="about" value="About" />
        <textarea
            id="about"
            v-model="form.about"
            rows="10"
            placeholder="A couple of sentences about you as a professional."
            class="w-full rounded-md border border-ink/20 px-3 py-2 text-ink transition-colors focus:border-ink focus:outline-none"
        />
        <InputError :message="form.errors.about" />
    </div>
</template>
