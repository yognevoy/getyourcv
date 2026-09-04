export function formatDate(dateString, { withYear = false, withTime = false } = {}) {
    const date = new Date(dateString);

    const datePart = date.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        ...(withYear ? { year: 'numeric' } : {}),
    });

    if (!withTime) {
        return datePart;
    }

    const timePart = date.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });

    return `${datePart}, ${timePart}`;
}
