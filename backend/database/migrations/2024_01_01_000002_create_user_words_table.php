<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_words', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('word_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['new', 'learning', 'mastered'])->default('new');
            $table->timestamp('next_review_date')->nullable();
            $table->integer('review_count')->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('incorrect_count')->default(0);
            $table->float('easiness_factor')->default(2.5); // For SM-2 algorithm
            $table->integer('interval')->default(0); // Days until next review
            $table->timestamps();

            $table->unique(['session_id', 'word_id']);
            $table->index('next_review_date');
            $table->index(['session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_words');
    }
};

