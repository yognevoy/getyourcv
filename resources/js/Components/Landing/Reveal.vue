<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const el = ref(null);
const visible = ref(false);
let observer = null;

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

onMounted(() => {
    if (prefersReducedMotion || !el.value) {
        visible.value = true;
        return;
    }

    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                visible.value = true;
                observer.disconnect();
            }
        },
        { threshold: 0.1 },
    );
    observer.observe(el.value);
});

onUnmounted(() => observer?.disconnect());
</script>

<template>
    <div ref="el" class="reveal" :class="{ 'reveal--visible': visible }">
        <slot />
    </div>
</template>

<style scoped>
.reveal {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}

.reveal--visible {
    opacity: 1;
    transform: translateY(0);
}
</style>
