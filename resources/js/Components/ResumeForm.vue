<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SectionHeader from '@/Components/SectionHeader.vue';
import IconButton from '@/Components/IconButton.vue';
import RemoveIcon from '@/Components/RemoveIcon.vue';
import AddRowButton from '@/Components/AddRowButton.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

function addLink() {
    props.form.links.push({ label: '', url: '' });
}

function removeLink(index) {
    props.form.links.splice(index, 1);
}

function addSkillGroup() {
    props.form.skill_groups.push({ label: '', skills: [] });
}

function removeSkillGroup(index) {
    props.form.skill_groups.splice(index, 1);
}

function addSkill(groupIndex) {
    props.form.skill_groups[groupIndex].skills.push({ value: '' });
}

function removeSkill(groupIndex, skillIndex) {
    props.form.skill_groups[groupIndex].skills.splice(skillIndex, 1);
}

function addExperience() {
    props.form.experiences.push({
        company: '',
        title: '',
        period_from: '',
        period_to: '',
        is_current: false,
        bullets: [],
    });
}

function removeExperience(index) {
    props.form.experiences.splice(index, 1);
}

function addBullet(experienceIndex, type) {
    props.form.experiences[experienceIndex].bullets.push({ type, text: '' });
}

function removeBullet(experienceIndex, bulletIndex) {
    props.form.experiences[experienceIndex].bullets.splice(bulletIndex, 1);
}
</script>

<template>
    <section class="space-y-4">
        <SectionHeader index="01" title="Contacts" />

        <div>
            <InputLabel for="full_name" value="Full name" />
            <TextInput id="full_name" v-model="form.full_name" class="mt-1 block w-full" required />
            <InputError class="mt-1" :message="form.errors.full_name" />
        </div>

        <div>
            <InputLabel for="position" value="Position" />
            <TextInput id="position" v-model="form.position" class="mt-1 block w-full" />
            <InputError class="mt-1" :message="form.errors.position" />
        </div>

        <div>
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" v-model="form.email" class="mt-1 block w-full" />
            <InputError class="mt-1" :message="form.errors.email" />
        </div>

        <div class="space-y-2 pt-2">
            <InputLabel value="Links" />

            <TransitionGroup name="row" tag="div" class="space-y-2">
                <div v-for="(link, i) in form.links" :key="i" class="flex items-start gap-2">
                    <TextInput v-model="link.label" placeholder="GitHub" class="w-1/3" />
                    <TextInput v-model="link.url" placeholder="https://..." class="flex-1" />
                    <IconButton label="Remove link" @click="removeLink(i)">
                        <RemoveIcon />
                    </IconButton>
                </div>
            </TransitionGroup>

            <AddRowButton @click="addLink">Add link</AddRowButton>
        </div>
    </section>

    <section class="space-y-4">
        <SectionHeader index="02" title="About" />
        <textarea
            v-model="form.about"
            rows="5"
            placeholder="A couple of sentences about you as a professional."
            class="w-full border border-black/20 px-3 py-2 text-black transition-colors focus:border-black focus:outline-none"
        />
        <InputError :message="form.errors.about" />
    </section>

    <section class="space-y-4">
        <SectionHeader index="03" title="Skills" />

        <TransitionGroup name="row" tag="div" class="space-y-4">
            <div
                v-for="(group, gi) in form.skill_groups"
                :key="gi"
                class="space-y-3 border border-black/15 p-4"
            >
                <div class="flex items-start gap-2">
                    <TextInput
                        v-model="group.label"
                        placeholder="Languages, Frameworks..."
                        class="flex-1"
                    />
                    <IconButton label="Remove skill group" @click="removeSkillGroup(gi)">
                        <RemoveIcon />
                    </IconButton>
                </div>

                <TransitionGroup name="row" tag="div" class="space-y-2">
                    <div
                        v-for="(skill, si) in group.skills"
                        :key="si"
                        class="flex items-center gap-2"
                    >
                        <TextInput v-model="skill.value" placeholder="PHP" class="flex-1" />
                        <IconButton label="Remove skill" @click="removeSkill(gi, si)">
                            <RemoveIcon />
                        </IconButton>
                    </div>
                </TransitionGroup>

                <AddRowButton @click="addSkill(gi)">Add skill</AddRowButton>
            </div>
        </TransitionGroup>

        <AddRowButton @click="addSkillGroup">Add skill group</AddRowButton>
    </section>

    <section class="space-y-4">
        <SectionHeader index="04" title="Experience" />

        <TransitionGroup name="row" tag="div" class="space-y-4">
            <div
                v-for="(experience, ei) in form.experiences"
                :key="ei"
                class="space-y-4 border border-black/15 p-4"
            >
                <div class="flex items-start gap-2">
                    <div class="grid flex-1 gap-2 sm:grid-cols-2">
                        <TextInput v-model="experience.title" placeholder="Title" />
                        <TextInput v-model="experience.company" placeholder="Company" />
                    </div>
                    <IconButton label="Remove experience" @click="removeExperience(ei)">
                        <RemoveIcon />
                    </IconButton>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <TextInput
                        v-model="experience.period_from"
                        type="date"
                        class="min-w-[9rem] flex-1"
                    />
                    <TextInput
                        v-model="experience.period_to"
                        type="date"
                        class="min-w-[9rem] flex-1"
                        :disabled="experience.is_current"
                    />
                    <label class="flex items-center gap-1.5 text-sm text-black/70">
                        <Checkbox v-model:checked="experience.is_current" />
                        Current
                    </label>
                </div>

                <TransitionGroup name="row" tag="div" class="space-y-2">
                    <div
                        v-for="(bullet, bi) in experience.bullets"
                        :key="bi"
                        class="flex items-start gap-2"
                    >
                        <select
                            v-model="bullet.type"
                            class="border border-black/20 px-2 py-2 text-sm transition-colors focus:border-black focus:outline-none"
                        >
                            <option value="responsibility">Responsibility</option>
                            <option value="achievement">Achievement</option>
                        </select>
                        <TextInput v-model="bullet.text" class="flex-1" />
                        <IconButton label="Remove bullet" @click="removeBullet(ei, bi)">
                            <RemoveIcon />
                        </IconButton>
                    </div>
                </TransitionGroup>

                <div class="flex flex-wrap gap-2">
                    <AddRowButton class="flex-1" @click="addBullet(ei, 'responsibility')">
                        Add responsibility
                    </AddRowButton>
                    <AddRowButton class="flex-1" @click="addBullet(ei, 'achievement')">
                        Add achievement
                    </AddRowButton>
                </div>
            </div>
        </TransitionGroup>

        <AddRowButton @click="addExperience">Add experience</AddRowButton>
    </section>
</template>

<style scoped>
.row-enter-active,
.row-leave-active {
    transition: all 0.18s ease;
}

.row-enter-from,
.row-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
