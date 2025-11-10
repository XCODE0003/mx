#!/usr/bin/env node
const https = require('https');
const fs = require('fs');

/**
 * Функция для выполнения HTTP запроса
 */
function fetchPage(url) {
    return new Promise((resolve, reject) => {
        const options = {
            rejectUnauthorized: false,
            headers: {
                'Accept-Charset': 'utf-8',
                'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
            }
        };

        https.get(url, options, (res) => {
            res.setEncoding('utf8');
            let data = '';

            if (res.statusCode === 301 || res.statusCode === 302) {
                return fetchPage(res.headers.location).then(resolve).catch(reject);
            }

            res.on('data', (chunk) => {
                data += chunk;
            });

            res.on('end', () => {
                resolve(data);
            });
        }).on('error', (err) => {
            reject(err);
        });
    });
}

/**
 * Очистка текста от HTML тегов
 */
function cleanText(text) {
    return text
        .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
        .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
        .replace(/<span class="MathJax[^"]*"[^>]*>[\s\S]*?<\/span>/gi, '')
        .replace(/<nobr[^>]*>[\s\S]*?<\/nobr>/gi, '')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/g, ' ')
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&')
        .replace(/&#x(\w+);/g, (match, hex) => String.fromCharCode(parseInt(hex, 16)))
        .replace(/\s+/g, ' ')
        .trim();
}

/**
 * Функция для поиска задания по ID
 */
function findTaskById(html, taskId) {
    const idPattern = new RegExp(`<div class=['"]id-text['"]>[^<]*<span class=['"]canselect['"]>${taskId}</span></div>`, 'i');

    if (!idPattern.test(html)) {
        return null;
    }

    const allBlocks = html.split(/<div class=['"]qblock['"]/i);

    let targetBlock = null;
    for (let block of allBlocks) {
        const hasId = new RegExp(`<span class=['"]canselect['"]>${taskId}</span>`, 'i').test(block);
        if (hasId) {
            targetBlock = '<div class="qblock' + block.split(/<div class=['"]separator['"]/i)[0];
            break;
        }
    }

    if (!targetBlock) {
        return null;
    }

    const guidMatch = targetBlock.match(/<input type=['"][Hh]idden['"] name=['"]guid['"] value=['"]([A-F0-9]+)['"]/i);
    const guid = guidMatch ? guidMatch[1] : 'Не найден';

    const blockIdMatch = targetBlock.match(/id=['"]q([A-F0-9]+)['"]/i);
    const blockId = blockIdMatch ? blockIdMatch[1] : taskId;

    const contentMatch = targetBlock.match(/<td[^>]*class=['"]cell_0['"][^>]*>([\s\S]*?)<\/td>/i);
    let content = contentMatch ? contentMatch[1] : 'Содержимое не найдено';
    const cleanContent = cleanText(content);

    const hintMatch = targetBlock.match(/<div[^>]*class=['"]hint['"][^>]*>(.*?)<\/div>/i);
    const hint = hintMatch ? cleanText(hintMatch[1]) : '';

    const kesMatch = targetBlock.match(/<td class=['"]param-name['"]>[^<]*КЭС[^<]*<\/td><td class=['"]param-row['"]><div>(.*?)<\/div>/i);
    const kes = kesMatch ? cleanText(kesMatch[1]) : 'Не указан';

    const answerTypeMatch = targetBlock.match(/<td class=['"]param-name['"]>[^<]*Тип ответа[^<]*<\/td><td>(.*?)<\/td>/i);
    const answerType = answerTypeMatch ? cleanText(answerTypeMatch[1]) : 'Не указан';

    return {
        id: taskId,
        blockId: blockId,
        guid: guid,
        hint: hint,
        content: cleanContent,
        kes: kes,
        answerType: answerType,
        fullHtml: targetBlock
    };
}

/**
 * Поиск по нескольким страницам
 */
async function searchMultiplePages(taskId, startPage = 1, endPage = 50) {
    const baseUrl = 'https://ege.fipi.ru/bank/questions.php?proj=E040A72A1A3DABA14C90C97E0B6EE7DC&pagesize=500&page=';

    for (let page = startPage; page <= endPage; page++) {
        process.stdout.write(`\rПроверяю страницу ${page}/${endPage}...`);

        try {
            const html = await fetchPage(baseUrl + page);
            const task = findTaskById(html, taskId);

            if (task) {
                console.log(`\n✓ Найдено на странице ${page}!\n`);
                return task;
            }
        } catch (error) {
            // Продолжаем поиск даже при ошибке
        }
    }

    console.log('\n');
    return null;
}

/**
 * Главная функция
 */
async function main() {
    const args = process.argv.slice(2);

    if (args.length === 0 || args[0] === '--help' || args[0] === '-h') {
        console.log(`
╔═══════════════════════════════════════════════════════════════════════════════╗
║              ПОИСК ЗАДАНИЙ НА САЙТЕ ФИПИ (ЕГЭ) ПО НОМЕРУ                     ║
╚═══════════════════════════════════════════════════════════════════════════════╝

Использование:
  node search_fipi.cjs <НОМЕР_ЗАДАНИЯ> [опции]

Опции:
  --save, -s         Сохранить HTML в файл
  --page <N>         Искать только на странице N (по умолчанию: 1)
  --all              Искать на всех страницах (1-50)
  --help, -h         Показать эту справку

Примеры:
  node search_fipi.cjs B35CF7
  node search_fipi.cjs C118BD --save
  node search_fipi.cjs A12345 --all
  node search_fipi.cjs B35CF7 --page 5

`);
        process.exit(0);
    }

    const taskId = args[0].trim().toUpperCase();
    const saveHtml = args.includes('--save') || args.includes('-s');
    const searchAll = args.includes('--all');
    const pageIndex = args.indexOf('--page');
    const specificPage = pageIndex !== -1 && args[pageIndex + 1] ? parseInt(args[pageIndex + 1]) : 1;

    console.log(`\n🔍 Поиск задания: ${taskId}...\n`);

    try {
        let task = null;

        if (searchAll) {
            console.log('🌐 Поиск на всех страницах...');
            task = await searchMultiplePages(taskId, 1, 50);
        } else {
            console.log(`📥 Загружаю страницу ${specificPage}...`);
            const url = `https://ege.fipi.ru/bank/questions.php?proj=E040A72A1A3DABA14C90C97E0B6EE7DC&page=${specificPage}&pagesize=500`;
            const html = await fetchPage(url);
            task = findTaskById(html, taskId);
        }

        if (task) {
            console.log('═'.repeat(80));
            console.log(`ID:           ${task.id}`);
            console.log(`GUID:         ${task.guid}`);
            console.log(`КЭС:          ${task.kes}`);
            console.log(`Тип ответа:   ${task.answerType}`);
            console.log('═'.repeat(80));

            if (task.hint) {
                console.log(`\n📌 ${task.hint}`);
            }

            console.log(`\n📝 ${task.content}\n`);
            console.log('═'.repeat(80));

            if (saveHtml) {
                const filename = `task_${taskId}_${Date.now()}.html`;
                fs.writeFileSync(filename, task.fullHtml);
                console.log(`\n✓ HTML сохранен: ${filename}`);
            }
        } else {
            console.log(`❌ Задание "${taskId}" не найдено.`);
            console.log(`💡 Попробуйте: node search_fipi.cjs ${taskId} --all`);
            process.exit(1);
        }

    } catch (error) {
        console.error(`\n❌ Ошибка: ${error.message}`);
        process.exit(1);
    }
}

main();

