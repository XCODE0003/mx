function truncate(text, maxLength) {
    if (text.length <= maxLength) {
        return text;
    }
    return text.slice(0, maxLength) + '...';
}

function extractNumbersAndSymbols(text) {
    return text.replace(/[a-zA-Zа-яА-Я]/g, '');
}

export { truncate, extractNumbersAndSymbols };