<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('base_task_id')->nullable()->after('subject_id');
            $table->string('zip_file_name', 255)->nullable()->after('task_ids');
            $table->timestamp('files_expire_at')->nullable()->after('file_url');
            $table->timestamp('files_purged_at')->nullable()->after('files_expire_at');
        });

        foreach (DB::table('orders')->select('id')->cursor() as $row) {
            DB::table('orders')->where('id', $row->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'base_task_id', 'zip_file_name', 'files_expire_at', 'files_purged_at']);
        });
    }
};
