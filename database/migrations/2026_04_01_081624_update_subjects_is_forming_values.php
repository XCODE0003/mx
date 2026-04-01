<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Сначала отключаем формирование для ВСЕХ предметов
        DB::table('subjects')->update(['is_forming' => false]);
        
        // Затем включаем ТОЛЬКО для нужных предметов
        DB::table('subjects')->whereIn('class_name', [
            'RUSS_OGE',        // ОГЭ Русский язык
            'MAT_OGE',         // ОГЭ Математика
            'RUSS_EGE',        // ЕГЭ Русский язык
            'MAT_BAZA_EGE',    // ЕГЭ Математика базовая
            'MAT_PROF_EGE',    // ЕГЭ Математика профильная
        ])->update(['is_forming' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем всем предметам is_forming = true
        DB::table('subjects')->update(['is_forming' => true]);
    }
};
