<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class GenerateTaskBundle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $baseTaskId;
    /** @var array<int> */
    public array $taskIds;
    public string $zipFileName;

    public $timeout = 600;
    public $tries = 1;

    public function __construct(int $baseTaskId, array $taskIds, string $zipFileName)
    {
        $this->baseTaskId = $baseTaskId;
        $this->taskIds = array_values(array_unique(array_map('intval', $taskIds)));
        $this->zipFileName = $zipFileName;
    }

    public function handle(): void
    {
        @ini_set('max_execution_time', '600');
        @set_time_limit(600);

        $baseTask = Task::with('group', 'subject')->findOrFail($this->baseTaskId);
        $ids = collect($this->taskIds);
        if ($ids->isEmpty()) {
            $ids = collect([$this->baseTaskId]);
        }
        $tasks = Task::with('group')->whereIn('id', $ids->all())->get()
            ->filter(function ($task) {
                // Фильтруем задачи, у которых есть группа
                return $task->group !== null;
            })
            ->sortBy(function ($task) {
                // Теперь можем безопасно обращаться к formatted_title
                return (int) ($task->group->formatted_title ?? 999999);
            });

        $questionHtmlMap = [];
        foreach ($tasks as $t) {
            $questionHtmlMap[$t->id] = $this->inlineImages($t->question);
        }

        $dir = public_path('exports/tasks/'.$this->baseTaskId);
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Файлы к упаковке
        $ts = time();
        $pdfNoAns = $dir.DIRECTORY_SEPARATOR.'variant-'.$ts.'.pdf';
        $pdfAns   = $dir.DIRECTORY_SEPARATOR.'variant-answers-'.$ts.'.pdf';
        $docNoAns = $dir.DIRECTORY_SEPARATOR.'variant-'.$ts.'.docx';
        $docAns   = $dir.DIRECTORY_SEPARATOR.'variant-answers-'.$ts.'.docx';
        $zipPath  = $dir.DIRECTORY_SEPARATOR.$this->zipFileName;

        // PDF без ответов
        $htmlNo = view('pdf.task', [
            'task' => $baseTask,
            'subject' => $baseTask->subject,
            'tasks' => $tasks,
            'questionHtmlMap' => $questionHtmlMap,
            'withAnswers' => false,
        ])->render();
        $this->savePdf($htmlNo, $pdfNoAns);

        // PDF с ответами
        $htmlAns = view('pdf.task', [
            'task' => $baseTask,
            'tasks' => $tasks,
            'subject' => $baseTask->subject,
            'questionHtmlMap' => $questionHtmlMap,
            'withAnswers' => true,
        ])->render();
        $this->savePdf($htmlAns, $pdfAns);

        // DOCX без ответов: только Pandoc из отдельного Word-шаблона
        // Word-HTML без ответов по отдельному шаблону
        $wordHtmlNo = view('word.task', [
            'task' => $baseTask,
            'tasks' => $tasks,
            'subject' => $baseTask->subject,
            'questionHtmlMap' => $questionHtmlMap,
            'withAnswers' => false,
        ])->render();
        if ($this->saveDocxViaPandoc($wordHtmlNo, $docNoAns)) {
            Log::info('DOCX (no answers) generated via pandoc', ['path' => $docNoAns]);
        } else {
            Log::error('Pandoc failed to generate DOCX (no answers)', ['target' => $docNoAns]);
        }

        // DOCX с ответами: только Pandoc из отдельного Word-шаблона
        $wordHtmlAns = view('word.task', [
            'task' => $baseTask,
            'tasks' => $tasks,
            'subject' => $baseTask->subject,
            'questionHtmlMap' => $questionHtmlMap,
            'withAnswers' => true,
        ])->render();
        if ($this->saveDocxViaPandoc($wordHtmlAns, $docAns)) {
            Log::info('DOCX (with answers) generated via pandoc', ['path' => $docAns]);
        } else {
            Log::error('Pandoc failed to generate DOCX (with answers)', ['target' => $docAns]);
        }

        // ZIP
        $zip = new ZipArchive();
        $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($result === true) {
            try {
                if (is_file($pdfNoAns)) { $zip->addFile($pdfNoAns, basename($pdfNoAns)); }
                if (is_file($pdfAns))   { $zip->addFile($pdfAns, basename($pdfAns)); }
                if (is_file($docNoAns)) { $zip->addFile($docNoAns, basename($docNoAns)); }
                if (is_file($docAns))   { $zip->addFile($docAns, basename($docAns)); }

                // Добавляем MP3 файлы из additional_files
                $this->addAdditionalFilesToZip($zip, $tasks);

                $zip->close();
            } catch (\Throwable $e) {
                Log::error('Error while creating ZIP archive', [
                    'path' => $zipPath,
                    'error' => $e->getMessage()
                ]);
                // Пытаемся закрыть архив, если он был открыт
                if ($zip->status === ZipArchive::ER_OK) {
                    @$zip->close();
                }
            }
        } else {
            Log::error('Failed to open ZIP archive', [
                'path' => $zipPath,
                'error_code' => $result
            ]);
        }
    }

private function savePdf(string $html, string $fullPath): void
{
    // Инлайним CSS из внешнего файла
    $cssPath = public_path('assets/task.css');
    if (is_file($cssPath)) {
        $cssContent = file_get_contents($cssPath);
        // Заменяем <link rel="stylesheet" href="/assets/task.css?v=..."> на <style>
        $html = preg_replace(
            '/<link\s+rel=["\']stylesheet["\']\s+href=["\'][^"\']*task\.css[^"\']*["\'][^>]*>/i',
            '<style>' . $cssContent . '</style>',
            $html
        );
    }

    $footerHtml = '<div style="width:100%; font-size:10px; text-align:center; color:#000;font-family: "Times New Roman", serif; padding-top:6px; border-top:1px solid #000;">
        © '.date('Y').' год. Вариант сгенерирован на сайте kim365.ru<br>
        Публикация в интернете или печатных изданиях без письменного согласия запрещена
    </div>';

    $headerHtml = '<div style="height:0; overflow:hidden;"></div>';

    $browser = Browsershot::html($html)
        ->format('A4')
        ->margins(15, 15, 35, 15)              // большее нижнее поле под футер
        ->showBrowserHeaderAndFooter()         // включает header/footer
        ->headerHtml($headerHtml)              // HTML заголовка
        ->footerHtml($footerHtml)              // HTML футера
        ->waitUntilNetworkIdle()
        ->setDelay(2000)                       // Увеличиваем задержку для применения стилей
        ->timeout(600)
        ->noSandbox()
        ->emulateMedia('print');               // Эмулируем print media для корректных page-break

    $browser->setOption('args', [
        '--allow-file-access-from-files',
        '--disable-web-security',
    ]);

    $browser->savePdf($fullPath);
}

    /**
     * Попытка сгенерировать DOCX напрямую из HTML через LibreOffice (soffice)
     * Возвращает true при успехе
     */
    private function saveDocxFromHtml(string $html, string $targetDocx): bool
    {
        try {
            $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'docx_'.uniqid();
            @mkdir($tmpDir, 0775, true);
            $htmlFile = $tmpDir.DIRECTORY_SEPARATOR.'index.html';
            // Оборачиваем в минимальный HTML, если пришёл фрагмент
            if (stripos($html, '<html') === false) {
                $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>'.$html.'</body></html>';
            }
            @file_put_contents($htmlFile, $html);

            $outDir = dirname($targetDocx);
            $bin = $this->detectSofficeBinary();
            if ($bin === null) {
                return false;
            }
            $cmd = escapeshellcmd($bin).' --headless --convert-to '.escapeshellarg('docx:MS Word 2007 XML').' --outdir '.escapeshellarg($outDir).' '.escapeshellarg($htmlFile).' 2>&1';
            @exec($cmd, $output, $code);
            $produced = $outDir.DIRECTORY_SEPARATOR.'index.docx';
            if ($code === 0 && is_file($produced)) {
                @rename($produced, $targetDocx);
                return true;
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return false;
    }

    /**
     * Конвертация PDF -> DOCX через LibreOffice (сохраняет макет максимально близко к PDF)
     */
    private function saveDocxFromPdf(string $pdfPath, string $targetDocx): bool
    {
        try {
            if (!is_file($pdfPath)) return false;
            $outDir = dirname($targetDocx);
            $bin = $this->detectSofficeBinary();
            if ($bin === null) return false;
            $cmd = escapeshellcmd($bin).' --headless --convert-to '.escapeshellarg('docx:MS Word 2007 XML').' --outdir '.escapeshellarg($outDir).' '.escapeshellarg($pdfPath).' 2>&1';
            @exec($cmd, $output, $code);
            $base = pathinfo($pdfPath, PATHINFO_FILENAME);
            $produced = $outDir.DIRECTORY_SEPARATOR.$base.'.docx';
            if ($code === 0 && is_file($produced)) {
                @rename($produced, $targetDocx);
                return true;
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return false;
    }

    private function detectSofficeBinary(): ?string
    {
        $candidates = ['soffice', 'libreoffice'];
        foreach ($candidates as $bin) {
            $path = trim(@shell_exec('command -v '.escapeshellarg($bin)) ?: '');
            if ($path !== '' && is_executable($path)) return $path;
        }
        // MacOS typical path
        $mac = '/Applications/LibreOffice.app/Contents/MacOS/soffice';
        if (is_executable($mac)) return $mac;
        return null;
    }

    private function detectNodeBinary(): ?string
    {
        $path = trim(@shell_exec('command -v node') ?: '');
        if ($path !== '' && is_executable($path)) return $path;
        return null;
    }

    private function saveDocxViaNode(string $html, string $targetDocx): bool
    {
        try {
            $node = $this->detectNodeBinary();
            if ($node === null) return false;
            $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'docx_'.uniqid();
            @mkdir($tmpDir, 0775, true);
            $htmlFile = $tmpDir.DIRECTORY_SEPARATOR.'index.html';
            if (stripos($html, '<html') === false) {
                $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>'.$html.'</body></html>';
            }
            @file_put_contents($htmlFile, $html);
            $script = base_path('scripts/html-to-docx.js');
            $cmd = escapeshellarg($node).' '.escapeshellarg($script).' '.escapeshellarg($htmlFile).' '.escapeshellarg($targetDocx).' 2>&1';
            @exec($cmd, $output, $code);
            return $code === 0 && is_file($targetDocx);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function detectPandocBinary(): ?string
    {
        $path = trim(@shell_exec('command -v pandoc') ?: '');
        if ($path !== '' && is_executable($path)) return $path;
        return null;
    }

    /**
     * Конвертация HTML → DOCX через pandoc (современный и устойчивый конвертер)
     */
    private function saveDocxViaPandoc(string $html, string $targetDocx): bool
    {
        try {
            $pandoc = $this->detectPandocBinary();
            if ($pandoc === null) return false;
            $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pandoc_'.uniqid();
            @mkdir($tmpDir, 0775, true);
            $htmlFile = $tmpDir.DIRECTORY_SEPARATOR.'index.html';
            if (stripos($html, '<html') === false) {
                $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>'.$html.'</body></html>';
            }
            @file_put_contents($htmlFile, $html);
            $resourcePath = public_path();
            $cmd = escapeshellcmd($pandoc).' -f html -t docx --embed-resources --standalone '
                .'--resource-path='.escapeshellarg($resourcePath).' '
                .escapeshellarg($htmlFile).' -o '.escapeshellarg($targetDocx).' 2>&1';
            @exec($cmd, $output, $code);
            return $code === 0 && is_file($targetDocx);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Упрощённый fallback-рендеринг DOCX (без точной верстки)
     */
    private function saveDocxFallback(Collection $tasks, array $questionHtmlMap, bool $withAnswers, string $fullPath): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        foreach ($tasks as $t) {
            // Безопасное получение formatted_title
            $taskTitle = $t->group?->formatted_title ?? '?';
            $section->addText('Задание '.$taskTitle, ['bold' => true, 'size' => 12]);
            $html = $questionHtmlMap[$t->id] ?? ($t->question ?? '');
            // Текстовая часть
            $text = $this->htmlToPlainText($html);
            foreach (explode("\n", $text) as $line) {
                if (trim($line) !== '') {
                    $section->addText($line, ['size' => 11]);
                }
            }
            // Картинки
            foreach ($this->extractImages($html) as $imgPath) {
                try { $section->addImage($imgPath, ['width' => 400]); } catch (\Throwable $e) {}
            }
            $answerText = $withAnswers ? (string)($t->response ?? '') : '___________________________';
            $section->addText('Ответ: '.$answerText, ['size' => 11]);
            $section->addTextBreak(1);
        }
        $phpWord->save($fullPath, 'Word2007');
    }

    private function htmlToPlainText(string $html): string
    {
        $normalized = preg_replace(['#<br\s*/?>#i', '#</p>#i', '#</div>#i'], ["\n", "\n", "\n"], $html);
        $text = strip_tags($normalized);
        return html_entity_decode($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Достаём изображения (data: и http) в temp-файлы
     * @return array<string> paths
     */
    private function extractImages(string $html): array
    {
        $paths = [];
        if (!preg_match_all('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', $html, $m)) {
            return $paths;
        }
        foreach ($m[1] as $src) {
            try {
                $data = null;
                $mime = 'image/jpeg';
                if (str_starts_with($src, 'data:')) {
                    if (preg_match('#^data:([^;]+);base64,(.*)$#', $src, $mm)) {
                        $mime = $mm[1] ?: 'image/jpeg';
                        $data = base64_decode($mm[2]);
                    }
                } else if (preg_match('#^https?://#i', $src)) {
                    $data = @file_get_contents($src);
                    $ext = strtolower(pathinfo(parse_url($src, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                    $mime = $this->extToMime($ext);
                } else {
                    $abs = public_path(ltrim($src, '/'));
                    if (is_file($abs)) {
                        $data = @file_get_contents($abs);
                        $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
                        $mime = $this->extToMime($ext);
                    }
                }
                if ($data) {
                    $ext = $this->mimeToExt($mime);
                    $tmp = tempnam(sys_get_temp_dir(), 'img_');
                    $new = $tmp.'.'.$ext;
                    @file_put_contents($new, $data);
                    $paths[] = $new;
                }
            } catch (\Throwable $e) {}
        }
        return $paths;
    }

    private function extToMime(string $ext): string
    {
        $map = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        ];
        return $map[$ext] ?? 'image/jpeg';
    }

    private function mimeToExt(string $mime): string
    {
        $map = [
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', 'image/svg+xml' => 'svg',
        ];
        return $map[$mime] ?? 'jpg';
    }

    private function inlineImages(?string $html): string
    {
        if (!$html) return '';
        return preg_replace_callback('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', function ($m) {
            $src = $m[1] ?? '';
            $resolved = $this->resolveImageSrc($src);
            return str_replace($src, $resolved, $m[0]);
        }, $html);
    }

    private function resolveImageSrc(string $src): string
    {
        if ($src === '' || str_starts_with($src, 'data:')) {
            return $src;
        }
        if (preg_match('#^https?://#i', $src)) {
            return $src;
        }
        $path = ltrim($src, '/');
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }
        $full = public_path($path);
        if (!is_file($full)) {
            $try = public_path('docs/'.ltrim($path, '/'));
            if (is_file($try)) {
                $full = $try;
            }
        }
        if (is_file($full)) {
            $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            $map = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mime = $map[$ext] ?? (@mime_content_type($full) ?: 'image/jpeg');
            $data = @file_get_contents($full);
            if ($data !== false) {
                return 'data:'.$mime.';base64,'.base64_encode($data);
            }
        }
        $abs = url('/'.ltrim($path, '/'));
        $httpData = @file_get_contents($abs);
        if ($httpData !== false) {
            $ext = strtolower(pathinfo(parse_url($abs, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            $map = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mime = $map[$ext] ?? 'image/jpeg';
            return 'data:'.$mime.';base64,'.base64_encode($httpData);
        }
        return $abs;
    }

    /**
     * Добавляет файлы из additional_files задач в ZIP архив
     */
    private function addAdditionalFilesToZip(ZipArchive $zip, Collection $tasks): void
    {
        $addedFiles = []; // Для предотвращения дубликатов

        foreach ($tasks as $task) {
            if (!$task->additional_files) {
                continue;
            }

            try {
                $files = json_decode($task->additional_files, true);
                if (!is_array($files)) {
                    continue;
                }

                foreach ($files as $filePath) {
                    // Пропускаем если уже добавлен
                    if (in_array($filePath, $addedFiles)) {
                        continue;
                    }

                    // Убираем ведущий слэш и проверяем формат пути
                    $path = ltrim($filePath, '/');

                    // Проверяем, что путь начинается с files/
                    if (!str_starts_with($path, 'files/')) {
                        continue;
                    }

                    // Получаем полный путь к файлу
                    $fullPath = public_path($path);

                    // Проверяем существование файла
                    if (is_file($fullPath)) {
                        $fileName = basename($path);
                        $zip->addFile($fullPath, 'files/' . $fileName);
                        $addedFiles[] = $filePath;
                        Log::info('Added additional file to ZIP', [
                            'file' => $filePath,
                            'task_id' => $task->id
                        ]);
                    } else {
                        Log::warning('Additional file not found', [
                            'file' => $filePath,
                            'full_path' => $fullPath,
                            'task_id' => $task->id
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Error processing additional_files', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function getZipErrorMessage(int $code): string
    {
        $messages = [
            ZipArchive::ER_OK => 'No error',
            ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
            ZipArchive::ER_RENAME => 'Renaming temporary file failed',
            ZipArchive::ER_CLOSE => 'Closing zip archive failed',
            ZipArchive::ER_SEEK => 'Seek error',
            ZipArchive::ER_READ => 'Read error',
            ZipArchive::ER_WRITE => 'Write error',
            ZipArchive::ER_CRC => 'CRC error',
            ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
            ZipArchive::ER_NOENT => 'No such file',
            ZipArchive::ER_EXISTS => 'File already exists',
            ZipArchive::ER_OPEN => 'Can\'t open file',
            ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
            ZipArchive::ER_ZLIB => 'Zlib error',
            ZipArchive::ER_MEMORY => 'Memory allocation failure',
            ZipArchive::ER_CHANGED => 'Entry has been changed',
            ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
            ZipArchive::ER_EOF => 'Premature EOF',
            ZipArchive::ER_INVAL => 'Invalid argument',
            ZipArchive::ER_NOZIP => 'Not a zip archive',
            ZipArchive::ER_INTERNAL => 'Internal error',
            ZipArchive::ER_INCONS => 'Zip archive inconsistent',
            ZipArchive::ER_REMOVE => 'Can\'t remove file',
            ZipArchive::ER_DELETED => 'Entry has been deleted',
        ];

        return $messages[$code] ?? "Unknown error (code: {$code})";
    }
}


