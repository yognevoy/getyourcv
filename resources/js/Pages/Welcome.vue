<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import Landing from '@/Components/Landing/Landing.vue';
</script>

<template>
    <Head title="Build your resume">
        <meta
            name="description"
            content="A resume builder to create and share your CV without wasting time."
        />
        <meta
            name="keywords"
            content="resume builder, CV builder, online resume, resume link, AI resume, create resume, professional resume"
        />
    </Head>

    <div class="flex min-h-screen flex-col bg-paper text-ink">
        <PublicHeader />

        <Landing />

        <PublicFooter />
    </div>
</template>
