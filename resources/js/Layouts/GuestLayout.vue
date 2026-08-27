<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-paper pt-6 text-ink sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/" class="text-lg font-semibold tracking-tight text-ink">
                getyourcv
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden rounded-md border border-ink bg-paper px-6 py-4 sm:max-w-md"
        >
            <slot />
        </div>
    </div>
</template>
