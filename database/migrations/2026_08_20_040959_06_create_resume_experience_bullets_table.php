<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_experience_bullets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_experience_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('text');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_experience_bullets');
    }
};
