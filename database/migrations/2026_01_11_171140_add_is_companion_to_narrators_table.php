<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('narrators', function (Blueprint $table) {
            $table->boolean('is_companion')->default(false)->after('name')->comment('هل هو صحابي؟');
            $table->index('is_companion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('narrators', function (Blueprint $table) {
            $table->dropIndex(['is_companion']);
            $table->dropColumn('is_companion');
        });
    }
};
