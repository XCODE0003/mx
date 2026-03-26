/**
 * Логика отображения и самопроверки заданий на странице «Банк заданий».
 */

export function taskHasTableAnswer(task) {
    if (!task) return false;
    if (Number(task.table_answer) === 1) return true;
    const r = task.response || '';
    return /<table[\s>]/i.test(r);
}

export function taskAudioUrls(task) {
    let raw = task.additional_files;
    if (raw == null) return [];
    if (typeof raw === 'string') {
        try {
            raw = JSON.parse(raw);
        } catch {
            return [];
        }
    }
    if (!Array.isArray(raw)) return [];
    return raw
        .map((u) => String(u).trim())
        .filter((u) => /\.(mp3|ogg|wav|m4a)(\?|$)/i.test(u));
}

/** Развёрнутые ответы, таблицы и длинный HTML — без поля «Проверить». */
export function showSelfCheck(task) {
    if (!task) return false;
    if (task.type_answer === 'hide_line') return false;
    if (taskHasTableAnswer(task)) return false;
    const plain = stripHtml(task.response || '');
    if (plain.length > 240) return false;
    return true;
}

export function stripHtml(html) {
    return String(html || '')
        .replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, ' ')
        .replace(/<style[\s\S]*?>[\s\S]*?<\/style>/gi, ' ')
        .replace(/<[^>]+>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

/**
 * Номер подпункта для связки (например группа «2–3», index 0 → 2).
 */
export function subtaskNumberFromGroupTitle(groupTitle, indexInGroup) {
    const nums = String(groupTitle || '').match(/\d+/g);
    if (!nums || nums.length === 0) return String(indexInGroup + 1);
    const start = parseInt(nums[0], 10);
    if (nums.length === 1) {
        return String(start + indexInGroup);
    }
    return String(start + indexInGroup);
}
