<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const page = usePage();
</script>

<template>
    <header class="bg-ink">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center">
                <Link href="/" class="text-lg font-semibold tracking-tight text-paper">
                    getyourcv
                </Link>

                <div v-if="page.props.auth.user" class="ms-10 flex items-center space-x-8">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </NavLink>
                    <NavLink :href="route('resumes.trash')" :active="route().current('resumes.trash')">
                        Trash
                    </NavLink>
                </div>
            </div>

            <nav v-if="!page.props.auth.user" class="flex items-center gap-6 text-sm">
                <Link :href="route('login')" class="text-paper/80 hover:text-paper">
                    Sign in
                </Link>
                <Link :href="route('register')" class="text-paper/80 hover:text-paper">
                    Sign up
                </Link>
            </nav>
        </div>
    </header>
</template>
