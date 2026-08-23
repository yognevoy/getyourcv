<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
</script>

<template>
    <Head title="Contacts" />

    <div class="flex min-h-screen flex-col bg-white text-black">
        <PublicHeader />

        <main class="mx-auto w-full max-w-2xl flex-1 px-6 py-16">
            <h1 class="text-2xl font-semibold tracking-tight">Contacts</h1>
            <p class="mt-4 text-black/70">
                Questions or feedback? Reach out at
                <a href="mailto:hello@getyourcv.app" class="underline hover:opacity-60">
                    hello@getyourcv.app
                </a>.
            </p>
        </main>

        <PublicFooter />
    </div>
</template>
