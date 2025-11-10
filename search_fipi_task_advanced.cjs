const https = require('https');
const readline = require('readline');

// Создаем интерфейс для ввода
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

/**
 * Функция для выполнения HTTP запроса
 */
function fetchPage(url) {
    return new Promise((resolve, reject) => {
        const options = {
            rejectUnauthorized: false, // Отключаем проверку SSL сертификата
            headers: {
                'Accept-Charset': 'utf-8',
                'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
            }
        };

        https.get(url, options, (res) => {
            // Устанавливаем кодировку
            res.setEncoding('utf8');
            let data = '';

            // Обработка редиректов
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
 * Очистка текста от HTML тегов и лишних пробелов
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
        .replace(/\s+/g, ' ')
        .trim();
}

/**
 * Извлечение формул MathML
 */
function extractMathFormulas(html) {
    const formulas = [];
    const mathmlPattern = /<math[^>]*>([\s\S]*?)<\/math>/gi;
    let match;

    while ((match = mathmlPattern.exec(html)) !== null) {
        const semanticsMatch = match[1].match(/<mrow[^>]*>([\s\S]*?)<\/mrow>/i);
        if (semanticsMatch) {
            // Извлекаем текстовое представление формулы
            const formulaText = semanticsMatch[1]
                .replace(/<mn>([^<]+)<\/mn>/g, '$1')
                .replace(/<mo>([^<]+)<\/mo>/g, ' $1 ')
                .replace(/<mi>([^<]+)<\/mi>/g, '$1')
                .replace(/\s+/g, ' ')
                .trim();
            formulas.push(formulaText);
        }
    }

    return formulas;
}

/**
 * Функция для поиска задания по ID
 */
function findTaskById(html, taskId) {
    // Ищем блок с нужным ID (не зависим от текста "Номер:", только структура)
    // Поддерживаем оба варианта кавычек
    const idPattern = new RegExp(`<div class=['"]id-text['"]>[^<]*<span class=['"]canselect['"]>${taskId}</span></div>`, 'i');

    if (!idPattern.test(html)) {
        return null;
    }

    // Находим начало блока задания - ищем от начала qblock до следующего qblock или маркера
    const allBlocks = html.split(/<div class=['"]qblock['"]/i);

    let targetBlock = null;
    for (let block of allBlocks) {
        // Ищем ID в структуре id-text
        const hasId = new RegExp(`<span class=['"]canselect['"]>${taskId}</span>`, 'i').test(block);
        if (hasId) {
            targetBlock = '<div class="qblock' + block.split(/<div class=['"]separator['"]/i)[0];
            break;
        }
    }

    if (!targetBlock) {
        return null;
    }

    // Извлекаем GUID (поддержка одинарных и двойных кавычек)
    const guidMatch = targetBlock.match(/<input type=['"][Hh]idden['"] name=['"]guid['"] value=['"]([A-F0-9]+)['"]/i);
    const guid = guidMatch ? guidMatch[1] : 'Не найден';

    // Извлекаем ID блока (поддержка одинарных и двойных кавычек)
    const blockIdMatch = targetBlock.match(/id=['"]q([A-F0-9]+)['"]/i);
    const blockId = blockIdMatch ? blockIdMatch[1] : taskId;

    // Извлекаем содержимое задания (поддержка одинарных и двойных кавычек)
    const contentMatch = targetBlock.match(/<td[^>]*class=['"]cell_0['"][^>]*>([\s\S]*?)<\/td>/i);
    let content = contentMatch ? contentMatch[1] : 'Содержимое не найдено';

    // Извлекаем формулы
    const formulas = extractMathFormulas(content);

    // Очищаем текст
    const cleanContent = cleanText(content);

    // Извлекаем подсказку/инструкцию (поддержка одинарных и двойных кавычек)
    const hintMatch = targetBlock.match(/<div[^>]*class=['"]hint['"][^>]*>(.*?)<\/div>/i);
    const hint = hintMatch ? cleanText(hintMatch[1]) : '';

    // Извлекаем КЭС (поддержка одинарных и двойных кавычек)
    const kesMatch = targetBlock.match(/<td class=['"]param-name['"]>КЭС:<\/td><td class=['"]param-row['"]><div>(.*?)<\/div>/i);
    const kes = kesMatch ? cleanText(kesMatch[1]) : 'Не указан';

    // Извлекаем тип ответа (поддержка одинарных и двойных кавычек)
    const answerTypeMatch = targetBlock.match(/<td class=['"]param-name['"]>Тип ответа:<\/td><td>(.*?)<\/td>/i);
    const answerType = answerTypeMatch ? cleanText(answerTypeMatch[1]) : 'Не указан';

    // Извлекаем поле для ответа (поддержка одинарных и двойных кавычек)
    const answerInputMatch = targetBlock.match(/<input type=['"]text['"] name=['"]answer['"][^>]*>/i);
    const hasTextInput = !!answerInputMatch;

    return {
        id: taskId,
        blockId: blockId,
        guid: guid,
        hint: hint,
        content: cleanContent,
        formulas: formulas,
        kes: kes,
        answerType: answerType,
        hasTextInput: hasTextInput,
        fullHtml: targetBlock
    };
}

/**
 * Поиск по нескольким страницам
 */
async function searchMultiplePages(taskId, startPage = 1, endPage = 20, debug = false) {
    const baseUrl = 'https://ege.fipi.ru/bank/questions.php?proj=E040A72A1A3DABA14C90C97E0B6EE7DC&pagesize=500&page=';

    for (let page = startPage; page <= endPage; page++) {
        process.stdout.write(`\rПроверяю страницу ${page}/${endPage}...`);

        try {
            const html = await fetchPage(baseUrl + page);

            if (debug) {
                // Проверяем, есть ли вообще ID в HTML (поддержка одинарных и двойных кавычек)
                const qblockPattern = new RegExp(`id=['"]q${taskId}['"]`, 'i');
                const hasQblock = qblockPattern.test(html);
                console.log(`\nСтраница ${page}: qblock с id="q${taskId}" ${hasQblock ? 'НАЙДЕН' : 'НЕ НАЙДЕН'}`);

                if (hasQblock) {
                    // Проверяем наличие div с номером (не зависим от текста "Номер:")
                    const idTextPattern = new RegExp(`<div class=['"]id-text['"]>[^<]*<span class=['"]canselect['"]>${taskId}</span></div>`, 'i');
                    const hasIdText = idTextPattern.test(html);
                    console.log(`Страница ${page}: id-text ${hasIdText ? 'НАЙДЕН' : 'НЕ НАЙДЕН'}`);
                }
            }

            const task = findTaskById(html, taskId);

            if (task) {
                console.log(`\n✓ Найдено на странице ${page}!`);
                return task;
            }
        } catch (error) {
            console.error(`\nОшибка при загрузке страницы ${page}: ${error.message}`);
        }
    }

    console.log('\n');
    return null;
}

/**
 * Вывод информации о задании
 */
function displayTask(task) {
    console.log('\n' + '═'.repeat(80));
    console.log('║' + ' '.repeat(28) + 'ЗАДАНИЕ НАЙДЕНО!' + ' '.repeat(28) + '║');
    console.log('═'.repeat(80));
    console.log(`║ ID:           ${task.id.padEnd(63)}║`);
    console.log(`║ GUID:         ${task.guid.padEnd(63)}║`);
    console.log(`║ Block ID:     ${task.blockId.padEnd(63)}║`);
    console.log(`║ КЭС:          ${task.kes.substring(0, 63).padEnd(63)}║`);
    console.log(`║ Тип ответа:   ${task.answerType.padEnd(63)}║`);
    console.log('═'.repeat(80));

    if (task.hint) {
        console.log('\n📌 ИНСТРУКЦИЯ:');
        console.log('   ' + task.hint);
    }

    console.log('\n📝 СОДЕРЖАНИЕ ЗАДАНИЯ:');
    console.log('   ' + task.content);

    if (task.formulas.length > 0) {
        console.log('\n🔢 ФОРМУЛЫ:');
        task.formulas.forEach((formula, index) => {
            console.log(`   ${index + 1}. ${formula}`);
        });
    }

    console.log('\n' + '═'.repeat(80));
}

/**
 * Главная функция
 */
async function main() {
    console.log('╔═══════════════════════════════════════════════════════════════════════════════╗');
    console.log('║              ПОИСК ЗАДАНИЙ НА САЙТЕ ФИПИ (ЕГЭ) ПО НОМЕРУ                     ║');
    console.log('╚═══════════════════════════════════════════════════════════════════════════════╝');
    console.log('');

    rl.question('Введите номер задания (например, C118BD): ', async (taskId) => {
        taskId = taskId.trim().toUpperCase();

        if (!taskId) {
            console.log('❌ Ошибка: Номер не может быть пустым');
            rl.close();
            return;
        }

        console.log(`\n🔍 Поиск задания с номером: ${taskId}...`);

        rl.question('Искать на всех страницах? (y/n/debug, по умолчанию: n): ', async (searchAll) => {
            try {
                let task = null;
                const isDebug = searchAll.toLowerCase() === 'debug';

                if (searchAll.toLowerCase() === 'y' || isDebug) {
                    if (isDebug) {
                        console.log('\n🐛 РЕЖИМ ОТЛАДКИ: Начинаю поиск с подробным выводом...');
                    } else {
                        console.log('\n🌐 Начинаю поиск по всем страницам (может занять время)...');
                    }
                    task = await searchMultiplePages(taskId, 1, 50, isDebug);
                } else {
                    const url = 'https://ege.fipi.ru/bank/questions.php?proj=E040A72A1A3DABA14C90C97E0B6EE7DC&page=1&pagesize=500';
                    console.log('\n📥 Загружаю страницу 1...');
                    const html = await fetchPage(url);
                    console.log('🔎 Поиск задания...');
                    task = findTaskById(html, taskId);
                }

                if (task) {
                    displayTask(task);

                    rl.question('\n💾 Сохранить полный HTML в файл? (y/n): ', (answer) => {
                        if (answer.toLowerCase() === 'y') {
                            const fs = require('fs');
                            const filename = `task_${taskId}_${Date.now()}.html`;
                            fs.writeFileSync(filename, task.fullHtml);
                            console.log(`\n✓ HTML сохранен в файл: ${filename}`);
                        }
                        console.log('\n✨ Работа завершена!');
                        rl.close();
                    });
                } else {
                    console.log(`\n❌ Задание с номером "${taskId}" не найдено.`);
                    console.log('💡 Советы:');
                    console.log('   - Проверьте правильность номера');
                    console.log('   - Попробуйте поиск по всем страницам');
                    console.log('   - Убедитесь, что задание существует в банке ФИПИ');
                    rl.close();
                }

            } catch (error) {
                console.error('\n❌ Ошибка при выполнении запроса:', error.message);
                rl.close();
            }
        });
    });
}

// Обработка прерывания программы
process.on('SIGINT', () => {
    console.log('\n\n👋 Программа прервана пользователем');
    rl.close();
    process.exit(0);
});

// Запуск программы
main();

