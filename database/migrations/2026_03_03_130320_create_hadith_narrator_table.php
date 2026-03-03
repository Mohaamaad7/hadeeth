<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hadith_narrator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hadith_id')->constrained()->onDelete('cascade');
            $table->foreignId('narrator_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['hadith_id', 'narrator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hadith_narrator');
    }
};
