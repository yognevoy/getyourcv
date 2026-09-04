<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('viewer_hash', 64);
            $table->timestamps();

            $table->index(['resume_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_views');
    }
};
