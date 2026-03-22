<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectAdditionalFilesSeeder extends Seeder
{
    /**
     * Маппинг subject_id → папка в public/files/ и список файлов.
     * Критерии оценивания.pdf, Справочные материалы.pdf — по наличию в папках.
     */
    private function getMapping(): array
    {
        return [
            // Математика ОГЭ
            'DE0E276E497AB3784C3FC4CC20248DC0' => [
                'files/MAT_OGE/Критерии оценивания.pdf',
                'files/MAT_OGE/Справочные материалы.pdf',
            ],
            // Математика. Профильный уровень ЕГЭ
            'AC437B34557F88EA4115D2F374B0A07B' => [
                'files/MAT_PROF/Критерии оценивания.pdf',
            ],
            // Математика. Базовый уровень ЕГЭ
            'E040A72A1A3DABA14C90C97E0B6EE7DC' => [
                'files/MAT_BASE/Справочные материалы.pdf',
            ],
            // Русский язык ОГЭ
            '2F5EE3B12FE2A0EA40B06BF61A015416' => [
                'files/OGE_RUS/Критерии оценивания.pdf',
            ],
            // Русский язык ЕГЭ
            'AF0ED3F2557F8FFC4C06F80B6803FD26' => [
                'files/EGE_RUS/Критерии оценивания.pdf',
            ],
        ];
    }

    public function run(): void
    {
        $mapping = $this->getMapping();

        foreach ($mapping as $subjectId => $paths) {
            $existing = array_filter($paths, fn ($path) => is_file(public_path($path)));

            if (empty($existing)) {
                $this->command?->warn("Subject {$subjectId}: no files found, skipping.");
                continue;
            }

            DB::table('subjects')
                ->where('subject_id', $subjectId)
                ->update([
                    'additional_files' => json_encode(array_values($existing)),
                    'updated_at' => now(),
                ]);

            $this->command?->info("Subject {$subjectId}: updated with " . count($existing) . " file(s).");
        }
    }
}
