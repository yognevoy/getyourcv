<template>
    <div class="dot-grid pointer-events-none fixed inset-0 -z-10" aria-hidden="true"></div>
</template>

<style scoped>
/* Viewport-fixed so the texture doesn't depend on the (possibly very long) page height -
   it's meant to fill whatever margin space isn't covered by opaque content, not to "fit" a section. */
.dot-grid {
    background-image: radial-gradient(circle, rgba(10, 10, 10, 0.35) 1.5px, transparent 1.5px);
    background-size: 32px 32px;
}
</style>
