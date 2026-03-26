<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class PruneExpiredVariantOrders extends Command
{
    protected $signature = 'variants:prune-orders';

    protected $description = 'Удаляет из БД записи вариантов после ORDER_RETENTION_DAYS (если задан в .env)';

    public function handle(): int
    {
        $days = Order::retentionDays();
        if ($days === null) {
            $this->info('ORDER_RETENTION_DAYS не задан — удаление старых записей отключено.');

            return self::SUCCESS;
        }

        $threshold = now()->subDays($days);

        $count = 0;
        Order::query()
            ->where('created_at', '<', $threshold)
            ->chunkById(50, function ($orders) use (&$count) {
                foreach ($orders as $order) {
                    $order->deleteVariantFilesFromDisk();
                    $order->delete();
                    $count++;
                }
            });

        if ($count > 0) {
            $this->info("Удалено записей вариантов: {$count}.");
        }

        return self::SUCCESS;
    }
}
