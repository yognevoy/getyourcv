<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resume_educations', function (Blueprint $table) {
            $table->boolean('is_current')->default(false)->after('period_to');
        });
    }

    public function down(): void
    {
        Schema::table('resume_educations', function (Blueprint $table) {
            $table->dropColumn('is_current');
        });
    }
};
