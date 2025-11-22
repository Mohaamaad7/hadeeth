<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('narrators', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('bio')->nullable();
            $table->string('grade_status')->nullable();
            $table->string('color_code')->default('#22c55e');
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narrators');
    }
};
