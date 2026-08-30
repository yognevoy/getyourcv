export function useRowIds() {
    let counter = 0;

    function nextRowId() {
        return counter++;
    }

    function withIds(rows) {
        rows.forEach((row) => {
            row.id ??= nextRowId();
        });

        return rows;
    }

    return { nextRowId, withIds };
}
