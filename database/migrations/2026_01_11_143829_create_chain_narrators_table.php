<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chain_narrators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->constrained('hadith_chains')->onDelete('cascade');
            $table->foreignId('narrator_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('position')->comment('ترتيب الراوي في السلسلة (1=المصنف، آخر=الصحابي)');
            $table->string('role')->nullable()->comment('دور الراوي: المصنف، الصحابي، إلخ');
            $table->timestamps();
            
            $table->index(['chain_id', 'position']);
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chain_narrators');
    }
};
