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
        $query = Order::query()
            ->whereNotNull('uuid')
            ->whereNotNull('files_expire_at')
            ->where('files_expire_at', '<=', $now)
            ->whereNull('files_purged_at');

        $count = 0;
        $query->chunkById(50, function ($orders) use (&$count) {
            foreach ($orders as $order) {
                $order->deleteVariantFilesFromDisk();
                $order->forceFill(['files_purged_at' => now()])->saveQuietly();
                $count++;
            }
        });

        if ($count > 0) {
            $this->info("Удалены файлы для {$count} вариант(ов).");
        }

        return self::SUCCESS;
    }
}
