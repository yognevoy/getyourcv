import axios from 'axios';

export const DRAFT_KEY = 'getyourcv_draft_resume';
export const PENDING_SAVE_KEY = 'getyourcv_pending_save';

export function usePendingDraft(onSaved) {
    const raw = sessionStorage.getItem(DRAFT_KEY);
    const status = sessionStorage.getItem(PENDING_SAVE_KEY);

    if (!raw || !status) {
        return;
    }

    let data;
    try {
        data = JSON.parse(raw);
    } catch {
        sessionStorage.removeItem(PENDING_SAVE_KEY);
        return;
    }

    sessionStorage.removeItem(PENDING_SAVE_KEY);

    axios
        .post(route('resumes.store'), {
            ...data,
            title: data.full_name || 'Untitled resume',
            status,
        })
        .then(() => {
            sessionStorage.removeItem(DRAFT_KEY);
            onSaved?.();
        });
}
