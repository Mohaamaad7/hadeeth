<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('narrators', function (Blueprint $table) {
            $table->string('fame_name')->nullable()->after('name')->index();
        });

        // نقل الاختصارات المعروفة من الكود إلى قاعدة البيانات
        $aliases = [
            'عائشة' => 'عائشة بنت أبي بكر',
            'جابر' => 'جابر بن عبد الله',
            'أنس' => 'أنس بن مالك',
            'ثوبان' => 'ثوبان مولى رسول الله',
            'بلال' => 'بلال بن رباح',
            'معاذ' => 'معاذ بن جبل',
            'صهيب' => 'صهيب الرومي',
            'سلمان' => 'سلمان الفارسي',
            'حذيفة' => 'حذيفة بن اليمان',
            'معاوية' => 'معاوية بن أبي سفيان',
            'عمار' => 'عمار بن ياسر',
            'عمر' => 'عمر بن الخطاب',
            'جرير' => 'جرير بن عبد الله',
            'سمرة' => 'سمرة بن جندب',
            'خالد' => 'خالد بن الوليد',
            'فاطمة' => 'فاطمة الزهراء',
            'عتبان' => 'عتبان بن مالك',
            'العرباض' => 'العرباض بن سارية',
            'عبادة' => 'عبادة بن الصامت',
        ];

        foreach ($aliases as $fameName => $fullName) {
            \App\Models\Narrator::where('name', $fullName)
                ->update(['fame_name' => $fameName]);
        }
    }

    public function down(): void
    {
        Schema::table('narrators', function (Blueprint $table) {
            $table->dropColumn('fame_name');
        });
    }
};
