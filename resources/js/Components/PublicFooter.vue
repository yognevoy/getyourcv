<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="bg-ink">
        <div
            class="mx-auto flex max-w-5xl flex-col items-center gap-4 px-6 py-8 text-sm text-paper/80 sm:flex-row sm:justify-between"
        >
            <span>&copy; {{ new Date().getFullYear() }} getyourcv</span>

            <nav class="flex items-center gap-6">
                <Link :href="route('about')" class="hover:text-paper">About</Link>
                <Link :href="route('policy')" class="hover:text-paper">Policy</Link>
                <Link :href="route('contacts')" class="hover:text-paper">Contacts</Link>
            </nav>
        </div>
    </footer>
</template>
