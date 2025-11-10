#!/usr/bin/env node
const https = require('https');

/**
 * Быстрый HTTP запрос
 */
function fetchPage(url) {
    return new Promise((resolve, reject) => {
        const options = {
            rejectUnauthorized: false,
            headers: {
                'User-Agent': 'Mozilla/5.0'
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
 * Проверка наличия задания на странице
 */
function hasTask(html, taskId) {
    // Быстрая проверка по ID блока и номеру в id-text
    const idPattern = new RegExp(`id=['"]q${taskId}['"]`, 'i');
    const idTextPattern = new RegExp(`<span class=['"]canselect['"]>${taskId}</span>`, 'i');

    return idPattern.test(html) && idTextPattern.test(html);
}

/**
 * Асинхронный поиск задания на одной странице
 */
async function searchPage(taskId, page) {
    const url = `https://ege.fipi.ru/bank/questions.php?proj=E040A72A1A3DABA14C90C97E0B6EE7DC&pagesize=500&page=${page}`;

    try {
        const html = await fetchPage(url);

        if (hasTask(html, taskId)) {
            return { found: true, page, url };
        }

        return { found: false, page };
    } catch (error) {
        return { found: false, page, error: error.message };
    }
}

/**
 * Параллельный поиск по всем страницам (оптимизированный)
 */
async function findTaskParallel(taskId, maxPages = 50, concurrency = 10) {
    return new Promise((resolve) => {
        let found = null;
        let completedPages = 0;
        let activeRequests = 0;
        let pageIndex = 0;

        const pages = Array.from({ length: maxPages }, (_, i) => i + 1);

        const startNextRequest = () => {
            if (found || pageIndex >= pages.length) {
                if (activeRequests === 0) {
                    console.log(''); // Новая строка после прогресса
                    resolve(found);
                }
                return;
            }

            const page = pages[pageIndex++];
            activeRequests++;

            searchPage(taskId, page).then(result => {
                completedPages++;
                activeRequests--;

                process.stdout.write(`\rПроверено страниц: ${completedPages}/${maxPages}`);

                if (result.found && !found) {
                    found = result;
                    // Найдено! Останавливаем дальнейшие запросы
                    pageIndex = pages.length;

                    if (activeRequests === 0) {
                        console.log(''); // Новая строка после прогресса
                        resolve(found);
                    }
                } else {
                    // Запускаем следующий запрос
                    startNextRequest();
                }
            }).catch(() => {
                completedPages++;
                activeRequests--;
                process.stdout.write(`\rПроверено страниц: ${completedPages}/${maxPages}`);
                startNextRequest();
            });
        };

        // Запускаем начальную партию запросов
        for (let i = 0; i < Math.min(concurrency, pages.length); i++) {
            startNextRequest();
        }
    });
}

/**
 * Главная функция
 */
async function main() {
    const args = process.argv.slice(2);

    if (args.length === 0 || args[0] === '--help' || args[0] === '-h') {
        console.log(`
╔═══════════════════════════════════════════════════════════════════════════════╗
║           БЫСТРЫЙ ПОИСК ЗАДАНИЙ ФИПИ (ЕГЭ) - ВОЗВРАТ ССЫЛКИ                  ║
╚═══════════════════════════════════════════════════════════════════════════════╝

Использование:
  node find_task.cjs <НОМЕР_ЗАДАНИЯ> [опции]

Опции:
  --pages <N>        Максимум страниц для поиска (по умолчанию: 50)
  --concurrent <N>   Параллельных запросов (по умолчанию: 10)
  --help, -h         Показать эту справку

Примеры:
  node find_task.cjs B35CF7
  node find_task.cjs C118BD --pages 20
  node find_task.cjs A12345 --concurrent 20

Скрипт ищет задание параллельно на всех страницах и возвращает ссылку.
`);
        process.exit(0);
    }

    const taskId = args[0].trim().toUpperCase();

    const pagesIndex = args.indexOf('--pages');
    const maxPages = pagesIndex !== -1 && args[pagesIndex + 1] ? parseInt(args[pagesIndex + 1]) : 50;

    const concurrentIndex = args.indexOf('--concurrent');
    const concurrency = concurrentIndex !== -1 && args[concurrentIndex + 1] ? parseInt(args[concurrentIndex + 1]) : 10;

    console.log(`\n🔍 Поиск задания: ${taskId}`);
    console.log(`📊 Параметры: ${maxPages} страниц, ${concurrency} параллельных запросов\n`);

    const startTime = Date.now();

    try {
        const result = await findTaskParallel(taskId, maxPages, concurrency);

        const elapsed = ((Date.now() - startTime) / 1000).toFixed(2);

        if (result) {
            console.log(`\n✓ НАЙДЕНО на странице ${result.page}! (за ${elapsed} сек)\n`);
            console.log('═'.repeat(80));
            console.log(`Ссылка: ${result.url}`);
            console.log('═'.repeat(80));
            console.log(`\nПрямая ссылка на страницу ${result.page} с заданием ${taskId}`);
        } else {
            console.log(`\n❌ Задание "${taskId}" не найдено на ${maxPages} страницах (${elapsed} сек)`);
            process.exit(1);
        }

    } catch (error) {
        console.error(`\n❌ Ошибка: ${error.message}`);
        process.exit(1);
    }
}

main();

