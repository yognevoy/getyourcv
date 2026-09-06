import { ref, watch } from 'vue';

export function useCollapsibleRows(rows, { errors, errorPrefix } = {}) {
    const expandedKeys = ref(
        new Set(rows.length <= 1 ? rows.map((row) => row.id) : []),
    );

    function isExpanded(row) {
        return expandedKeys.value.has(row.id);
    }

    function expand(row) {
        expandedKeys.value.add(row.id);
    }

    function toggle(row) {
        if (expandedKeys.value.has(row.id)) {
            expandedKeys.value.delete(row.id);
        } else {
            expandedKeys.value.add(row.id);
        }
    }

    function expandOnly(row) {
        expandedKeys.value = new Set([row.id]);
    }

    if (errors && errorPrefix) {
        const fieldPattern = new RegExp(`^${errorPrefix}\\.(\\d+)\\.`);

        watch(
            errors,
            (currentErrors) => {
                const firstField = Object.keys(currentErrors)[0];
                const match = firstField && firstField.match(fieldPattern);
                const row = match && rows[Number(match[1])];

                if (row) {
                    expand(row);
                }
            },
            { deep: true },
        );
    }

    return { isExpanded, toggle, expandOnly };
}
