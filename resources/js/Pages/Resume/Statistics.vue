<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EyeIcon from '@/Components/EyeIcon.vue';
import ViewsSparkline from '@/Components/ViewsSparkline.vue';
import ViewsChart from '@/Components/ViewsChart.vue';
import { formatRelativeDate } from '@/utils/formatRelativeDate';
import { formatDate } from '@/utils/formatDate';

defineProps({
    resume: {
        type: Object,
        required: true,
    },
    totalViews: {
        type: Number,
        required: true,
    },
    uniqueViews: {
        type: Number,
        required: true,
    },
    viewsLast7Days: {
        type: Number,
        required: true,
    },
    lastViewedAt: {
        type: String,
        default: null,
    },
    recentViews: {
        type: Array,
        required: true,
    },
    dailyViews: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Statistics" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-screen-lg px-4 py-10 sm:px-6 lg:px-10">
            <div class="max-w-2xl">
                <Link :href="route('dashboard')" class="text-sm text-ink/50 hover:text-ink">
                    &larr; Dashboard
                </Link>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">Statistics</h1>
                <p class="mt-1 text-sm text-ink/60">
                    How often {{ resume.full_name || 'this resume' }} has been viewed at its public link.
                </p>
            </div>

            <div v-if="totalViews === 0" class="mt-10 rounded-md border border-ink/15 bg-white px-8 py-16 text-center">
                <p class="text-sm text-ink/50">No views yet. Share your link to start tracking.</p>
            </div>

            <div v-else class="mt-8 rounded-md border border-ink/15 bg-white">
                <div class="flex flex-wrap items-center gap-5 p-4">
                    <div>
                        <p class="text-xs text-ink/50">Total views</p>
                        <p class="mt-1 text-5xl font-bold tracking-tight tabular-nums">{{ totalViews.toLocaleString() }}</p>
                    </div>
                    <ViewsSparkline :daily-views="dailyViews" />
                </div>

                <div class="flex flex-wrap gap-x-2 gap-y-1 px-4 pb-4 text-sm text-ink/60">
                    <span><strong class="font-semibold text-ink">{{ uniqueViews.toLocaleString() }}</strong> unique</span>
                    <span class="text-ink/25">&middot;</span>
                    <span><strong class="font-semibold text-ink">{{ viewsLast7Days.toLocaleString() }}</strong> last 7 days</span>
                    <span class="text-ink/25">&middot;</span>
                    <span>last viewed <strong class="font-semibold text-ink">{{ lastViewedAt ? formatRelativeDate(lastViewedAt) : '—' }}</strong></span>
                </div>

                <ViewsChart :daily-views="dailyViews" />

                <div v-if="recentViews.length" class="border-t border-ink/10 p-4">
                    <h2 class="text-sm font-semibold text-ink">Recent activity</h2>

                    <ul class="mt-3 divide-y divide-ink/10">
                        <li
                            v-for="(viewedAt, index) in recentViews"
                            :key="`${viewedAt}-${index}`"
                            class="flex items-center justify-between gap-3 py-2 text-sm"
                        >
                            <span class="flex items-center gap-2 text-ink/70">
                                <EyeIcon class="h-3.5 w-3.5 shrink-0 text-ink/40" />
                                Resume viewed {{ formatRelativeDate(viewedAt) }}
                            </span>
                            <span class="shrink-0 tabular-nums text-xs text-ink/40">{{ formatDate(viewedAt, { withYear: true, withTime: true }) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
