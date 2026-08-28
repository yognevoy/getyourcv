/**
 * Recursively checks whether a value has no real content.
 */
export function isBlank(value) {
    if (value == null) return true;
    if (typeof value === 'string') return value.trim() === '';
    if (Array.isArray(value)) return value.every(isBlank);
    if (typeof value === 'object') return Object.values(value).every(isBlank);

    return false;
}
