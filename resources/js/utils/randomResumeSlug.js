const EXAMPLE_TITLES = [
    'john-doe-resume',
    'jane-smith-resume',
    'alex-kim-resume',
    'maria-garcia-resume',
    'sam-patel-resume',
];

function randomSuffix() {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 6; i++) {
        result += chars[Math.floor(Math.random() * chars.length)];
    }
    return result;
}

export function randomResumeSlug() {
    const title = EXAMPLE_TITLES[Math.floor(Math.random() * EXAMPLE_TITLES.length)];
    return `${title}-${randomSuffix()}`;
}
