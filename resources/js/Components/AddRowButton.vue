<template>
    <button
        type="button"
        class="flex w-full items-center justify-center gap-2 rounded-md border border-dashed border-ink/30 py-2.5 text-sm text-ink/60 transition-colors hover:border-ink hover:text-ink"
    >
        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M8 2v12M2 8h12" stroke-linecap="round" />
        </svg>
        <slot />
    </button>
</template>
