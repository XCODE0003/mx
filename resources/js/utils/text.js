function truncate(text, maxLength) {
    if (text.length <= maxLength) {
        return text;
    }
    return text.slice(0, maxLength) + '...';
}

/**
 * Подписи номеров заданий (кнопки групп и т.п.): только цифры и дефис в диапазонах (4-5), без «Задание», двоеточий и пробелов.
 */
function extractNumbersAndSymbols(text) {
    let t = String(text ?? '')
        .replace(/[a-zA-Zа-яА-ЯёЁ]/g, '')
        .replace(/№/g, '')
        .replace(/[:;,]/g, '')
        .replace(/[–—−]/g, '-')
        .replace(/\s+/g, '')
        .trim();
    return t;
}

export { truncate, extractNumbersAndSymbols };