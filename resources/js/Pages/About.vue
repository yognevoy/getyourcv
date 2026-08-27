<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
</script>

<template>
    <Head title="About" />

    <div class="flex min-h-screen flex-col bg-paper text-ink">
        <PublicHeader />

        <main class="mx-auto w-full max-w-2xl flex-1 px-6 py-16">
            <h1 class="text-2xl font-semibold tracking-tight">About</h1>
            <p class="mt-4 text-ink/70">
                getyourcv is a simple way to build a resume, publish it at a link, and
                tailor it to a specific job without juggling files or templates. Fill
                the form once, share the link with any recruiter, anywhere in the world.
            </p>
        </main>

        <PublicFooter />
    </div>
</template>
