<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-white pt-6 text-black sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/" class="text-lg font-semibold tracking-tight text-black">
                getyourcv
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden border border-black bg-white px-6 py-4 sm:max-w-md"
        >
            <slot />
        </div>
    </div>
</template>
