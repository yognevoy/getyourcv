<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <header class="border-b border-black">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
            <Link href="/" class="text-lg font-semibold tracking-tight text-black">
                getyourcv
            </Link>

            <nav class="flex items-center gap-6 text-sm">
                <Link
                    v-if="page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-black hover:opacity-60"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="text-black hover:opacity-60">
                        Sign in
                    </Link>
                    <Link :href="route('register')" class="text-black hover:opacity-60">
                        Sign up
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
