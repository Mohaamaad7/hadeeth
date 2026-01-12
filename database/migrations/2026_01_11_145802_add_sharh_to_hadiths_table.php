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
        Schema::table('hadiths', function (Blueprint $table) {
            $table->text('sharh_context')->nullable()->after('content'); // سياق الحديث
            $table->json('sharh_obstacles')->nullable()->after('sharh_context'); // الموانع/المشاكل المذكورة
            $table->json('sharh_commands')->nullable()->after('sharh_obstacles'); // الأوامر/الحلول
            $table->text('sharh_conclusion')->nullable()->after('sharh_commands'); // الخلاصة والأحكام
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropColumn(['sharh_context', 'sharh_obstacles', 'sharh_commands', 'sharh_conclusion']);
        });
    }
};
