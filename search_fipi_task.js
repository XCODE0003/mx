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
        https.get(url, (res) => {
            let data = '';

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
 * Функция для поиска задания по ID
 */
function findTaskById(html, taskId) {
    // Ищем блок с нужным ID
    const idPattern = new RegExp(`<div class="id-text">Номер: <span class="canselect">${taskId}</span></div>`, 'i');
    
    if (!idPattern.test(html)) {
        return null;
    }

    // Находим начало блока задания
    const blockPattern = new RegExp(`<div class="qblock" id="q${taskId}">([\\s\\S]*?)<div class="id-text">Номер: <span class="canselect">${taskId}</span></div>([\\s\\S]*?)(?=<div class="qblock"|<div id="bottom_marker">)`, 'i');
    const match = html.match(blockPattern);

    if (!match) {
        return null;
    }

    const fullBlock = match[0];

    // Извлекаем GUID
    const guidMatch = fullBlock.match(/<input type="Hidden" name="guid" value="([A-F0-9]+)">/i);
    const guid = guidMatch ? guidMatch[1] : 'Не найден';

    // Извлекаем содержимое задания (текст вопроса)
    const contentMatch = fullBlock.match(/<td valign="top"[^>]*class="cell_0"[^>]*>([\s\S]*?)<\/td>/i);
    let content = contentMatch ? contentMatch[1] : 'Содержимое не найдено';

    // Очищаем HTML теги для более читаемого вывода
    content = content
        .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
        .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
        .replace(/<span class="MathJax[^"]*"[^>]*>[\s\S]*?<\/span>/gi, '')
        .replace(/<[^>]+>/g, '')
        .replace(/\s+/g, ' ')
        .trim();

    // Извлекаем КЭС (Кодификатор элементов содержания)
    const kesMatch = fullBlock.match(/<td class="param-name">КЭС:<\/td><td class="param-row"><div>(.*?)<\/div>/i);
    const kes = kesMatch ? kesMatch[1] : 'Не указан';

    // Извлекаем тип ответа
    const answerTypeMatch = fullBlock.match(/<td class="param-name">Тип ответа:<\/td><td>(.*?)<\/td>/i);
    const answerType = answerTypeMatch ? answerTypeMatch[1] : 'Не указан';

    return {
        id: taskId,
        guid: guid,
        content: content,
        kes: kes,
        answerType: answerType,
        fullHtml: fullBlock
    };
}

/**
 * Главная функция
 */
async function main() {
    console.log('=== Поиск заданий ФИПИ по ID ===\n');

    rl.question('Введите ID задания (например, C118BD): ', async (taskId) => {
        taskId = taskId.trim().toUpperCase();

        if (!taskId) {
            console.log('Ошибка: ID не может быть пустым');
            rl.close();
            return;
        }

        console.log(`\nИщу задание с ID: ${taskId}...`);

        try {
            // URL для поиска (можно изменить параметры)
            const url = 'https://ege.fipi.ru/bank/questions.php?proj=E040A72A1A3DABA14C90C97E0B6EE7DC&page=8&pagesize=500';
            
            console.log('Загружаю страницу...');
            const html = await fetchPage(url);

            console.log('Поиск задания...');
            const task = findTaskById(html, taskId);

            if (task) {
                console.log('\n' + '='.repeat(80));
                console.log('ЗАДАНИЕ НАЙДЕНО!');
                console.log('='.repeat(80));
                console.log(`ID:           ${task.id}`);
                console.log(`GUID:         ${task.guid}`);
                console.log(`КЭС:          ${task.kes}`);
                console.log(`Тип ответа:   ${task.answerType}`);
                console.log('='.repeat(80));
                console.log('СОДЕРЖАНИЕ:');
                console.log(task.content);
                console.log('='.repeat(80));

                // Опционально: сохранить полный HTML блок в файл
                rl.question('\nСохранить полный HTML в файл? (y/n): ', (answer) => {
                    if (answer.toLowerCase() === 'y') {
                        const fs = require('fs');
                        const filename = `task_${taskId}_${Date.now()}.html`;
                        fs.writeFileSync(filename, task.fullHtml);
                        console.log(`\nHTML сохранен в файл: ${filename}`);
                    }
                    rl.close();
                });
            } else {
                console.log(`\nЗадание с ID "${taskId}" не найдено на текущей странице.`);
                console.log('Попробуйте изменить параметры URL (page, pagesize) или проверьте правильность ID.');
                rl.close();
            }

        } catch (error) {
            console.error('Ошибка при выполнении запроса:', error.message);
            rl.close();
        }
    });
}

// Запуск программы
main();

