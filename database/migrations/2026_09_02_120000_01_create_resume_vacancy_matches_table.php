<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_vacancy_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resume_version_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vacancy_title')->nullable();
            $table->text('vacancy_text');
            $table->string('vacancy_text_hash', 64);
            $table->unsignedTinyInteger('score');
            $table->jsonb('matched_skills');
            $table->jsonb('missing_skills');
            $table->text('summary');
            $table->timestamps();

            $table->index(['resume_id', 'resume_version_id', 'vacancy_text_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_vacancy_matches');
    }
};
