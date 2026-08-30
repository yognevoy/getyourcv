<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="bg-ink">
        <div
            class="mx-auto flex max-w-7xl flex-col items-center gap-4 px-4 py-6 text-sm text-paper/80 sm:h-16 sm:flex-row sm:justify-between sm:py-0 sm:px-6 lg:px-8"
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
