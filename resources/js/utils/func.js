function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
}


function formatTaskText(text) {
    if (!text) return '';


    text = text

        .replace(/\r\n/g, '\n')
        .replace(/\r/g, '\n')


        .replace(/\(\s*\n\s*(\d+)\)/g, '($1)')

        .replace(/\s*\(\s*\n\s*(\d+)\)/g, '\n($1)')

        .replace(/\(предложение\s*(\d+)\s*\n\s*\)/gi, '(предложение $1)')


        .replace(/\(\s*предложение[\s\S]*?\)/gi, (s) => s
            .replace(/[\s\u00A0]+/g, ' ')
            .replace(/\s\)/g, ')')
        )


        .replace(/\(предложение\s*(\d+)\)\s*\n+/gi, '(предложение $1) ')

        .replace(/\(предложение\s*([0-9]+)\s*[\r\n]+\s*\)/gi, '(предложение $1)')

        .replace(/\(\s*предложение[\s\u00A0]*([0-9]+)[\s\u00A0]*\)/gi, '(предложение $1)')

        .replace(/([\p{L}\p{N}\)])\s*\n\s*([\.,;:!?])/gu, '$1$2 ')



        .replace(/(\d+)\)\s*([^(\n]+)\(предложение\s*\n\s*\1\)\s*(\d+)\)/g, '$1) $2(предложение $1)\n$3)')


        .replace(/\(предложение\s*\n\s*(\d+)\)/g, '(предложение $1)')


        .replace(/(\d+)\)\s*([^(\n]+)\(предложение\s*\n\s*(\d+)\)/g, '$1) $2(предложение $3)')


        .replace(/(предложение\s*)(\d+)\)/gi, '$1@@PRED$2@@)')


        .replace(/(^|\s)(\d+)\)\s*/g, '$1|||OPT:$2||| ')


        .replace(/\s*\|\|\|OPT:/g, '\n|||OPT:')

        .replace(/^\n\|\|\|OPT:/, '|||OPT:')


        .replace(/\|\|\|OPT:(\d+)\|\|\|\s*\|\|\|OPT:\1\|\|\|/g, '|||OPT:$1|||')


        .replace(/([^\n])\n\s*([\.,;:!?])(?=\s*(?:\n|$))/g, '$1$2')


        .replace(/\n\s*([\.,;:!\?»”])/g, '$1 ')


        .replace(/([\.!\?])\s*(["«])/g, '$1\n$2')


        .replace(/[ \t]+/g, ' ');

    return text

        .replace(/\n/g, '<br>')


        .replace(/\((\d+)\)/g, '<br><strong>($1)</strong>')


        .replace(/^([А-ЯЁ][А-ЯЁ\s]{2,}[А-ЯЁ])\s*$/gm, '<br><strong>$1</strong><br>')



        .replace(/(\d+)\)\s*([^(\n]+)\(предложение\s*(\n|\r\n?)\s*\1\)\s*(\d+)\)/g, '$1) $2(предложение $1)$3$4)')


        .replace(/\(предложение\s*(\n|\r\n?)\s*(\d+)\)/g, '(предложение $2)')


        .replace(/\|\|\|OPT:(\d+)\|\|\|/g, '<br><strong>$1)</strong> ')


        .replace(/@@PRED(\d+)@@/g, '$1')

        .replace(/\(предложение[^)]*?<br\s*\/?>[^)]*?\)/gi, (m) => m.replace(/<br\s*\/?>/gi, ' '))

        .replace(/\(предложение\s*(\d+)\)\s*<br\s*\/?>/gi, '(предложение $1) ')

        .replace(/<br\s*\/?>\s*\)/gi, ')')


        .replace(/\(([^)]*)\)/g, (match, inner) => `(${inner.replace(/<br\s*\/?>/gi, ' ').replace(/\s+/g, ' ').trim()})`)

        .replace(/(<br><strong>\d+\)<\/strong>[^<]*)(?!\s*<br>)/g, '$1<br>')


        .replace(/(^|\.\s|\s)([А-ЯЁ])\)\s*/gm, '$1<br><strong>$2)</strong> ')


        .replace(/(Выберите один или несколько правильных ответов\.?\s*)/gi, '<br><strong>$1</strong><br>')
        .replace(/(Укажите варианты ответов[^.]*\.?\s*)/gi, '<strong>$1</strong><br>')
        .replace(/(Запишите номера ответов\.?\s*)/gi, '<strong>$1</strong><br>')
        .replace(/(Установите соответствие[^.]*\.?\s*)/gi, '<br><strong>$1</strong><br>')
        .replace(/(К каждой позиции[^.]*\.?\s*)/gi, '<strong>$1</strong><br>')
        .replace(/(Запишите в таблицу[^.]*\.?\s*)/gi, '<strong>$1</strong><br>')


        .replace(/(Прочитайте текст и выполните задания\.?\s*)/gi, '<strong>$1</strong><br>')


        .replace(/(<br\s*\/?>){3,}/g, '<br><br>')


        .replace(/<br>\s*([\.,;:!?])/g, '$1')

        .replace(/([\.!\?])\s*(["«])/g, '$1<br>$2')


        .replace(/\s+/g, ' ')


        .trim()


        .replace(/^<br\s*\/?>/i, '');
}

export { copyToClipboard, formatTaskText };