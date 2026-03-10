<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * رمز المصدر يصبح اختياري (nullable) — يمكن إضافة مصدر بالاسم فقط
     */
    public function up(): void
    {
        // تغيير العمود إلى nullable مع الحفاظ على الـ unique constraint الموجود
        DB::statement('ALTER TABLE sources MODIFY code VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE sources MODIFY code VARCHAR(255) NOT NULL');
    }
};
