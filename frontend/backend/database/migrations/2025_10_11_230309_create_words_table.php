<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->enum('level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->index();
            $table->integer('difficulty')->default(1);
            $table->text('definition');
            $table->string('phonetic')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('image_url')->nullable();
            $table->text('example_sentence')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('words');
    }
};
