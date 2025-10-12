<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->integer('total_words')->default(0);
            $table->integer('mastered_words')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->json('level_progress')->nullable(); // Progress per level
            $table->float('accuracy_percentage')->default(0);
            $table->integer('total_quizzes')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();

            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};

