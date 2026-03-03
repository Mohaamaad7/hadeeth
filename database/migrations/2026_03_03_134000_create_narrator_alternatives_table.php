<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('narrator_alternatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('narrator_id')->constrained()->onDelete('cascade');
            $table->string('alternative_name')->index();
            $table->string('type')->default('variation'); // misspelling, variation, title, kunya
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narrator_alternatives');
    }
};
