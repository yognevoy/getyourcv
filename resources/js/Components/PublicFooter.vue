<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="border-t border-black">
        <div
            class="mx-auto flex max-w-5xl flex-col items-center gap-4 px-6 py-8 text-sm text-black sm:flex-row sm:justify-between"
        >
            <span>&copy; {{ new Date().getFullYear() }} getyourcv</span>

            <nav class="flex items-center gap-6">
                <Link :href="route('about')" class="hover:opacity-60">About</Link>
                <Link :href="route('policy')" class="hover:opacity-60">Policy</Link>
                <Link :href="route('contacts')" class="hover:opacity-60">Contacts</Link>
            </nav>
        </div>
    </footer>
</template>
