<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const POOL = [
    'Vue',
    'React',
    'TypeScript',
    'Laravel',
    'Docker',
    'AWS',
    'Leadership',
    'Remote-friendly',
    '5+ years',
    'REST API',
    'GraphQL',
    'Agile',
    'Mentoring',
    'Ownership',
    'Testing',
    'Figma',
    'PostgreSQL',
    'CI/CD',
    'Next.js',
    'Python',
    'Cross-functional',
];

const VISIBLE_ROWS = 8;
const HISTORY_LIMIT = 60;
const INTERVAL_MS = 800;
const RESUME_AFTER_MS = 2500;
const WHEEL_COOLDOWN_MS = 90;

const history = ref([]);
const scrollOffset = ref(0);
const isLive = computed(() => scrollOffset.value === 0);
const visible = computed(() => history.value.slice(scrollOffset.value, scrollOffset.value + VISIBLE_ROWS));

let counter = 0;
let pushTimer = null;
let resumeTimer = null;
let lastWheelAt = 0;

function randomWord(excluding) {
    let word = POOL[Math.floor(Math.random() * POOL.length)];
    let guard = 0;
    while (word === excluding && guard < 10) {
        word = POOL[Math.floor(Math.random() * POOL.length)];
        guard++;
    }
    return word;
}

function pushEntry() {
    const excluding = history.value[0]?.word;
    history.value.unshift({ id: ++counter, word: randomWord(excluding) });
    if (history.value.length > HISTORY_LIMIT) history.value.length = HISTORY_LIMIT;
    if (!isLive.value) scrollOffset.value += 1;
}

function handleWheel(event) {
    event.preventDefault();
    const now = performance.now();
    if (now - lastWheelAt < WHEEL_COOLDOWN_MS) return;
    lastWheelAt = now;

    const direction = event.deltaY > 0 ? 1 : -1;
    const max = Math.max(0, history.value.length - VISIBLE_ROWS);
    scrollOffset.value = Math.min(max, Math.max(0, scrollOffset.value + direction));

    clearTimeout(resumeTimer);
    resumeTimer = setTimeout(() => {
        scrollOffset.value = 0;
    }, RESUME_AFTER_MS);
}

onMounted(() => {
    for (let i = 0; i < VISIBLE_ROWS; i++) {
        history.value.push({ id: ++counter, word: randomWord() });
    }
    pushTimer = setInterval(pushEntry, INTERVAL_MS);
});

onUnmounted(() => {
    clearInterval(pushTimer);
    clearTimeout(resumeTimer);
});
</script>

<template>
    <div class="flex h-96 w-full items-center justify-center sm:h-[28rem] lg:h-[34rem]">
        <div class="relative w-full max-w-md overflow-hidden bg-paper/50">
            <div
                class="relative overflow-hidden"
                style="
                    mask-image: linear-gradient(to bottom, black 0%, black 65%, transparent 100%);
                    -webkit-mask-image: linear-gradient(to bottom, black 0%, black 65%, transparent 100%);
                "
                @wheel="handleWheel"
            >
                <TransitionGroup tag="div" name="feed1" class="relative">
                    <div
                        v-for="row in visible"
                        :key="row.id"
                        class="flex h-14 items-center border-b border-ink/10 px-5 text-base text-ink last:border-b-0"
                    >
                        {{ row.word }}
                    </div>
                </TransitionGroup>
            </div>
        </div>
    </div>
</template>

<style scoped>
.feed1-move,
.feed1-enter-active,
.feed1-leave-active {
    transition:
        transform 0.5s cubic-bezier(0.22, 1, 0.36, 1),
        opacity 0.35s ease;
}

.feed1-enter-from {
    transform: translateY(-100%);
    opacity: 0;
}

.feed1-leave-to {
    transform: translateY(100%);
    opacity: 0;
}

.feed1-leave-active {
    position: absolute;
    width: 100%;
}
</style>
