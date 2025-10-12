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
            $table->enum('status', ['learning', 'mastered'])->default('learning');
            $table->timestamp('next_review_date')->nullable();
            $table->integer('review_count')->default(0);
            $table->integer('repetitions')->default(0);
            $table->integer('interval')->default(1);
            $table->float('ease_factor')->default(2.5);
            $table->timestamps();
            
            $table->unique(['session_id', 'word_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_words');
    }
};
