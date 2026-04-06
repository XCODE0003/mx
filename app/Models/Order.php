<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    /** Срок хранения файлов на диске (минуты) */
    public const FILES_TTL_MINUTES = 30;

    /** @deprecated Используйте config('orders.retention_days') */
    public const RETENTION_DAYS = 30;

    protected $hidden = [
        'task_ids',
    ];

    protected $fillable = [
        'uuid',
        'subject_id',
        'base_task_id',
        'task_ids',
        'variant_count',
        'variants_task_ids',
        'zip_file_name',
        'user_id',
        'referral_link_id',
        'file_url',
        'files_expire_at',
        'files_purged_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function referralLink(): BelongsTo
    {
        return $this->belongsTo(ReferralLink::class);
    }

    protected function casts(): array
    {
        return [
            'task_ids' => 'array',
            'variants_task_ids' => 'array',
            'variant_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'files_expire_at' => 'datetime',
            'files_purged_at' => 'datetime',
        ];
    }

    protected $appends = [
        'download_url',
        'files_available',
        'retention_expired',
        'retention_limited',
        'retention_limit_days',
    ];

    /** Запись в БД когда-либо удаляется по расписанию (ORDER_RETENTION_DAYS). */
    public function getRetentionLimitedAttribute(): bool
    {
        return self::retentionDays() !== null;
    }

    /** Число дней хранения записи в БД или null, если без ограничения. */
    public function getRetentionLimitDaysAttribute(): ?int
    {
        return self::retentionDays();
    }

    public function absoluteVariantDirectory(): string
    {
        return public_path('exports/variants/'.$this->uuid);
    }

    public function absoluteZipPath(): string
    {
        $name = $this->zip_file_name ?? '';

        return $this->absoluteVariantDirectory().DIRECTORY_SEPARATOR.$name;
    }

    /**
     * Актуальный путь к zip: сначала каталог по uuid, затем старый путь exports/tasks/{base_task_id}/.
     */
    public function resolveZipAbsolutePath(): ?string
    {
        $primary = $this->absoluteZipPath();
        if (is_file($primary) && filesize($primary) > 0) {
            return $primary;
        }
        if ($this->base_task_id && $this->zip_file_name) {
            $legacy = public_path('exports/tasks/'.$this->base_task_id.DIRECTORY_SEPARATOR.$this->zip_file_name);
            if (is_file($legacy) && filesize($legacy) > 0) {
                return $legacy;
            }
        }

        return null;
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if (! $this->uuid) {
            return null;
        }

        return url('/variants/'.$this->uuid.'/download');
    }

    public function getFilesAvailableAttribute(): bool
    {
        if (! $this->uuid || ! $this->zip_file_name) {
            return false;
        }

        return $this->resolveZipAbsolutePath() !== null;
    }

    /**
     * Срок хранения записи в БД (дней). null — без ограничения.
     */
    public static function retentionDays(): ?int
    {
        return config('orders.retention_days');
    }

    public function getRetentionExpiredAttribute(): bool
    {
        $days = self::retentionDays();
        if ($days === null || ! $this->created_at) {
            return false;
        }

        return $this->created_at->copy()->addDays($days)->isPast();
    }

    public function retentionEndsAt(): ?\Illuminate\Support\Carbon
    {
        $days = self::retentionDays();
        if ($days === null || ! $this->created_at) {
            return null;
        }

        return $this->created_at->copy()->addDays($days);
    }

    /**
     * Список id заданий для пересборки (устойчиво к «двойному» JSON в longtext).
     *
     * @return array<int>
     */
    public function normalizedTaskIds(): array
    {
        $raw = $this->getAttributes()['task_ids'] ?? null;
        if ($raw === null || $raw === '') {
            return [];
        }

        $decoded = $raw;
        if (! is_array($decoded)) {
            for ($i = 0; $i < 4 && is_string($decoded); $i++) {
                $next = json_decode($decoded, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    break;
                }
                $decoded = $next;
            }
        }

        if (! is_array($decoded)) {
            return [];
        }

        $ids = [];
        foreach ($decoded as $v) {
            if (is_numeric($v)) {
                $ids[] = (int) $v;
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    /**
     * Наборы id заданий по вариантам (для мульти-архива). Одна запись = один вариант.
     *
     * @return array<int, array<int>>
     */
    public function variantsTaskIdsList(): array
    {
        $raw = $this->variants_task_ids;
        if (is_array($raw) && $raw !== []) {
            $out = [];
            foreach ($raw as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $ids = [];
                foreach ($row as $v) {
                    if (is_numeric($v)) {
                        $ids[] = (int) $v;
                    }
                }
                if ($ids !== []) {
                    $out[] = array_values(array_unique($ids));
                }
            }

            return $out;
        }

        $single = $this->normalizedTaskIds();

        return $single === [] ? [] : [$single];
    }

    public function deleteVariantFilesFromDisk(): void
    {
        if (! $this->uuid) {
            return;
        }
        $dir = $this->absoluteVariantDirectory();
        if (! is_dir($dir)) {
            return;
        }
        $this->recursiveDeleteDirectory($dir);
    }

    private function recursiveDeleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $items = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($items as $item) {
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                $this->recursiveDeleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
