<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
</script>

<template>
    <Head title="Build your resume" />

    <div class="flex min-h-screen flex-col bg-white text-black">
        <PublicHeader />

        <main class="flex flex-1 flex-col items-center justify-center px-6 text-center">
            <h1 class="max-w-2xl text-4xl font-semibold tracking-tight sm:text-5xl">
                One resume. One link. Ready for any HR.
            </h1>
            <p class="mt-4 max-w-xl text-base text-black/70">
                Fill it in once, publish it at a link, and share it anywhere in the world.
            </p>

            <Link
                :href="route('resumes.create')"
                class="mt-10 border border-black bg-black px-8 py-3 text-sm font-medium text-white transition-colors hover:bg-white hover:text-black"
            >
                Get started
            </Link>
        </main>

        <PublicFooter />
    </div>
</template>
