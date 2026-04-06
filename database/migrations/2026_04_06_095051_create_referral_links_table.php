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
        Schema::create('referral_links', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Уникальный код реферальной ссылки');
            $table->string('name')->comment('Название партнера/источника');
            $table->text('description')->nullable()->comment('Описание партнера');
            $table->unsignedBigInteger('clicks')->default(0)->comment('Количество кликов');
            $table->boolean('is_active')->default(true)->comment('Активна ли ссылка');
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_links');
    }
};
