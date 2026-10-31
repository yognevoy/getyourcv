<script setup>
import Reveal from '@/Components/Landing/Reveal.vue';
import Hero from '@/Components/Landing/Hero.vue';
import ComparisonTable from '@/Components/Landing/ComparisonTable.vue';
import VersionTimeline from '@/Components/Landing/VersionTimeline.vue';
import ShareLinkPreview from '@/Components/Landing/ShareLinkPreview.vue';
import PdfDownloadPreview from '@/Components/Landing/PdfDownloadPreview.vue';
import AnalyticsPreview from '@/Components/Landing/AnalyticsPreview.vue';
import GlobalSpeed from '@/Components/Landing/GlobalSpeed.vue';
import Faq from '@/Components/Landing/Faq.vue';
import CtaBanner from '@/Components/Landing/CtaBanner.vue';
</script>

<template>
    <main class="relative flex-1">
        <Hero />
        <Reveal><ComparisonTable dark /></Reveal>
        <Reveal><VersionTimeline /></Reveal>
        <Reveal><ShareLinkPreview dark /></Reveal>
        <Reveal><PdfDownloadPreview /></Reveal>
        <Reveal><GlobalSpeed dark /></Reveal>
        <Reveal><AnalyticsPreview /></Reveal>
        <Reveal><Faq dark /></Reveal>
        <Reveal><CtaBanner /></Reveal>
    </main>
</template>
