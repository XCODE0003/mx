function truncate(text, maxLength) {
    if (text.length <= maxLength) {
        return text;
    }
    return text.slice(0, maxLength) + '...';
}

function extractNumbersAndSymbols(text) {
    let textReplace = text.replace(/[a-zA-Zа-яА-Я]/g, '');
    textReplace = textReplace.replace('№', '');
    return textReplace;
}

export { truncate, extractNumbersAndSymbols };