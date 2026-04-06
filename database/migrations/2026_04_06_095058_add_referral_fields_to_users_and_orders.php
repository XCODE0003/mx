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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('referral_link_id')->nullable()->after('id')->constrained('referral_links')->onDelete('set null')->comment('Реферальная ссылка, по которой зарегистрировался');
            $table->index('referral_link_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('referral_link_id')->nullable()->after('user_id')->constrained('referral_links')->onDelete('set null')->comment('Реферальная ссылка, по которой совершена покупка');
            $table->index('referral_link_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referral_link_id']);
            $table->dropColumn('referral_link_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['referral_link_id']);
            $table->dropColumn('referral_link_id');
        });
    }
};
