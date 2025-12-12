<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // изменить поле task_ids на array
        Schema::table('orders', function (Blueprint $table) {
            $table->longText('task_ids')->change();
            $table->longText('file_url')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // изменить поле task_ids на string
        Schema::table('orders', function (Blueprint $table) {
            $table->longText('task_ids')->change();
            $table->longText('file_url')->change();
        });
    }
};
