<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE orders MODIFY zip_file_name VARCHAR(512) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE orders ALTER COLUMN zip_file_name TYPE VARCHAR(512)');
        }

        foreach (DB::table('orders')->whereNull('base_task_id')->cursor() as $row) {
            $url = (string) ($row->file_url ?? '');
            $path = parse_url($url, PHP_URL_PATH) ?: $url;
            if ($path === '') {
                continue;
            }
            if (preg_match('~/exports/tasks/(\d+)/([^/]+\.zip)$~i', $path, $m)) {
                $zip = urldecode($m[2]);
                if (strlen($zip) > 500) {
                    continue;
                }
                DB::table('orders')->where('id', $row->id)->update([
                    'base_task_id' => (int) $m[1],
                    'zip_file_name' => $zip,
                ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
