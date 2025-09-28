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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('article_id');
            $table->longText('question');
            $table->longText('image')->nullable();
            $table->longText('response')->nullable();
            $table->string('subject_id', 64);
            $table->string('blank_id', 64)->nullable();
            $table->string('question_id', 64)->nullable();
            $table->string('mark')->nullable();
            $table->timestamps();

            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
            $table->index(['subject_id', 'article_id']);
            $table->unique(['article_id', 'blank_id'], 'tasks_article_blank_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
