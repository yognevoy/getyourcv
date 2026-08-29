<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <header class="bg-ink">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
            <Link href="/" class="text-lg font-semibold tracking-tight text-paper">
                getyourcv
            </Link>

            <nav class="flex items-center gap-6 text-sm">
                <Link
                    v-if="page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-paper/80 hover:text-paper"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="text-paper/80 hover:text-paper">
                        Sign in
                    </Link>
                    <Link :href="route('register')" class="text-paper/80 hover:text-paper">
                        Sign up
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
