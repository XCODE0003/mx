<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class PurgeExpiredVariantFiles extends Command
{
    protected $signature = 'variants:purge-files';

    protected $description = 'Удаляет с диска файлы вариантов по истечении 30 минут (метаданные в БД остаются для пересоздания)';

    public function handle(): int
    {
        $now = now();
        $count = 0;

        // Новые заказы с истёкшим TTL (files_expire_at установлен)
        Order::query()
            ->whereNotNull('uuid')
            ->whereNotNull('files_expire_at')
            ->where('files_expire_at', '<=', $now)
            ->whereNull('files_purged_at')
            ->chunkById(50, function ($orders) use (&$count) {
                foreach ($orders as $order) {
                    $order->deleteVariantFilesFromDisk();
                    $order->forceFill(['files_purged_at' => now()])->saveQuietly();
                    $count++;
                }
            });

        // Устаревшие заказы без files_expire_at (до внедрения TTL) — удаляем файлы немедленно
        Order::query()
            ->whereNotNull('uuid')
            ->whereNull('files_expire_at')
            ->whereNull('files_purged_at')
            ->chunkById(50, function ($orders) use (&$count) {
                foreach ($orders as $order) {
                    $order->deleteVariantFilesFromDisk();
                    $this->purgeLegacyTaskFiles($order);
                    $order->forceFill([
                        'files_expire_at' => now(),
                        'files_purged_at' => now(),
                    ])->saveQuietly();
                    $count++;
                }
            });

        if ($count > 0) {
            $this->info("Удалены файлы для {$count} вариант(ов).");
        }

        return self::SUCCESS;
    }

    /**
     * Удаляет файлы из legacy-пути exports/tasks/{base_task_id}/.
     */
    private function purgeLegacyTaskFiles(Order $order): void
    {
        if (! $order->base_task_id || ! $order->zip_file_name) {
            return;
        }
        $legacyDir = public_path('exports/tasks/' . $order->base_task_id);
        if (! is_dir($legacyDir)) {
            return;
        }
        $zipPath = $legacyDir . DIRECTORY_SEPARATOR . $order->zip_file_name;
        if (is_file($zipPath)) {
            @unlink($zipPath);
        }
    }
}
