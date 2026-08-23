<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
</script>

<template>
    <Head title="Policy" />

    <div class="flex min-h-screen flex-col bg-white text-black">
        <PublicHeader />

        <main class="mx-auto w-full max-w-2xl flex-1 px-6 py-16">
            <h1 class="text-2xl font-semibold tracking-tight">Policy</h1>
            <p class="mt-4 text-black/70">
                We store the resume data you enter to render your public resume page
                and let you edit it later. We do not sell your data to third parties.
                Public resume links are visible to anyone who has the link.
            </p>
        </main>

        <PublicFooter />
    </div>
</template>
