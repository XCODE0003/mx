<?php

/**
 * ORDER_RETENTION_DAYS — через сколько дней удалять запись варианта из БД (команда variants:prune-orders).
 * Не задано или 0 — записи не удаляются (можно скачивать тот же набор заданий сколько угодно долго).
 */
$env = env('ORDER_RETENTION_DAYS');

return [
    'retention_days' => ($env !== null && $env !== '' && (int) $env > 0)
        ? (int) $env
        : null,
];
