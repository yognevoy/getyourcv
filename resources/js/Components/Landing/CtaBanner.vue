<script setup>
import { Link } from '@inertiajs/vue3';
import DotPatchOverlay from '@/Components/Landing/DotPatchOverlay.vue';
</script>

<template>
    <section class="relative isolate overflow-hidden px-6 py-14 sm:py-20">
        <DotPatchOverlay side="left" />
        <div class="relative mx-auto max-w-5xl rounded-lg border border-ink bg-ink px-8 py-14 text-center text-paper sm:px-16">
            <div class="relative">
                <h2 class="text-[clamp(1.75rem,5vw,3rem)] font-semibold tracking-tight">
                    Your next job starts with a link
                </h2>
                <Link
                    :href="route('resumes.create')"
                    class="mt-8 inline-block rounded-md border border-paper bg-paper px-9 py-4 text-base font-medium text-ink transition-colors hover:bg-ink hover:text-paper"
                >
                    Get your CV
                </Link>
            </div>
        </div>
    </section>
</template>
