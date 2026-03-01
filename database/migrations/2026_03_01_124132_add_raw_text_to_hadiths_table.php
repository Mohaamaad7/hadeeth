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
        Schema::table('hadiths', function (Blueprint $table) {
            $table->text('raw_text')->nullable()->after('content')->comment('النص الأصلي كما ورد في المصدر المطبوع');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropColumn('raw_text');
        });
    }
};
