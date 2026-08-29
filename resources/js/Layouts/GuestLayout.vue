<script setup>
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-paper text-ink">
        <PublicHeader />

        <main class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center px-6 py-16">
            <div class="rounded-md border border-ink/15 bg-white px-8 py-8">
                <slot />
            </div>
        </main>

        <PublicFooter />
    </div>
</template>
