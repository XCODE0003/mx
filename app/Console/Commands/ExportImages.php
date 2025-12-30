<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportImages extends Command
{
    protected $signature = 'export:images';
    protected $description = 'Export task images preserving folder structure';

    public function handle()
    {
        $subjectIds = [
            'E040A72A1A3DABA14C90C97E0B6EE7DC',
            'AC437B34557F88EA4115D2F374B0A07B',
            'DE0E276E497AB3784C3FC4CC20248DC0'
        ];

        $this->info('▶ Fetching images from DB...');

        $rows = DB::table('tasks')
            ->whereIn('subject_id', $subjectIds)
            ->whereNotNull('image')
            ->get(['id', 'image']);

        $exportRoot = storage_path('app/exported_images');
        if (!is_dir($exportRoot)) mkdir($exportRoot, 0777, true);

        $missingLog = $exportRoot . '/missing.log';
        file_put_contents($missingLog, "=== Missing files ===\n");

        $count = 0;

        foreach ($rows as $row) {
            $imageValue = $row->image;

            // Извлекаем из JSON-массива ["..."]
            if (str_starts_with(trim($imageValue), '["')) {
                $decoded = json_decode($imageValue, true);
                $imagePath = $decoded[0] ?? null;
            } else {
                $imagePath = $imageValue;
            }

            if (!$imagePath) continue;

            // Убираем домен https://oge.fipi.ru/
            $imagePath = str_replace('https://oge.fipi.ru/', '', $imagePath);

            $source = public_path($imagePath);
            $destination = $exportRoot . '/' . $imagePath;

            // Создаём директорию перед копированием
            $destDir = dirname($destination);
            if (!is_dir($destDir)) mkdir($destDir, 0777, true);

            if (file_exists($source)) {
                copy($source, $destination);
                $this->info("✔ $imagePath");
                $count++;
            } else {
                file_put_contents($missingLog, "$imagePath\n", FILE_APPEND);
                $this->warn("❌ $imagePath");
            }
        }

        $this->info("\n=== DONE ===");
        $this->info("Exported: $count files");
        $this->info("Export dir → $exportRoot");
        $this->info("Missing → $missingLog");

        return Command::SUCCESS;
    }
}
