<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ideas', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 100)->unique();
            $table->text('description');
            $table->string('category', 30)->index();
            $table->string('status', 30)->default('Exploring')->index();
            $table->unsignedInteger('votes')->default(0);
            $table->timestamps();
        });
        Schema::create('idea_votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('idea_id')->constrained()->cascadeOnDelete();
            $table->string('voter', 64);
            $table->unique(['idea_id', 'voter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_votes');
        Schema::dropIfExists('ideas');
    }
};
