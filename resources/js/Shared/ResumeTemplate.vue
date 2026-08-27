<script setup>
import { computed } from 'vue';

const props = defineProps({
    resume: {
        type: Object,
        required: true,
    },
    bordered: {
        type: Boolean,
        default: true,
    },
});

const skillGroups = computed(() =>
    (props.resume.skill_groups || []).filter(
        (group) => group.label || (group.skills || []).some((skill) => skill.value),
    ),
);

const experiences = computed(() => props.resume.experiences || []);
const links = computed(() => (props.resume.links || []).filter((link) => link.url));

function skillsLine(group) {
    return (group.skills || [])
        .map((skill) => skill.value)
        .filter(Boolean)
        .join(', ');
}

function period(item) {
    const from = item.period_from || '';
    const to = item.is_current ? 'Present' : item.period_to || '';

    return [from, to].filter(Boolean).join(' - ');
}
</script>

<template>
    <div class="bg-white p-10 text-black" :class="bordered ? 'border border-black/20' : ''">
        <header>
            <h1 class="text-2xl font-semibold">{{ resume.full_name || 'Your Name' }}</h1>
            <p v-if="resume.position" class="mt-1 text-black/70">{{ resume.position }}</p>
            <p v-if="resume.email" class="mt-1 text-sm">{{ resume.email }}</p>
            <p v-if="links.length" class="mt-1 text-sm">
                <template v-for="(link, i) in links" :key="i">
                    <a :href="link.url" class="underline">{{ link.label || link.url }}</a>
                    <span v-if="i < links.length - 1">, </span>
                </template>
            </p>
        </header>

        <section v-if="resume.about" class="mt-6">
            <h2 class="border-b border-black pb-1 text-xs font-semibold uppercase tracking-wide">
                About
            </h2>
            <p class="mt-2 whitespace-pre-line text-sm">{{ resume.about }}</p>
        </section>

        <section v-if="skillGroups.length" class="mt-6">
            <h2 class="border-b border-black pb-1 text-xs font-semibold uppercase tracking-wide">
                Skills
            </h2>
            <p v-for="(group, i) in skillGroups" :key="i" class="mt-2 text-sm">
                <span v-if="group.label" class="font-medium">{{ group.label }}: </span>
                <span>{{ skillsLine(group) }}</span>
            </p>
        </section>

        <section v-if="experiences.length" class="mt-6">
            <h2 class="border-b border-black pb-1 text-xs font-semibold uppercase tracking-wide">
                Experience
            </h2>
            <div v-for="(experience, i) in experiences" :key="i" class="mt-3 text-sm">
                <div class="flex flex-wrap items-baseline justify-between gap-x-2">
                    <span class="font-medium">
                        {{ experience.title || 'Position' }}<template v-if="experience.company">, {{ experience.company }}</template>
                    </span>
                    <span class="text-black/60">{{ period(experience) }}</span>
                </div>
                <ul v-if="(experience.bullets || []).length" class="mt-1 list-disc space-y-0.5 pl-5">
                    <li v-for="(bullet, j) in experience.bullets" :key="j">{{ bullet.text }}</li>
                </ul>
            </div>
        </section>
    </div>
</template>
