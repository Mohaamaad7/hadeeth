<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('narrators', function (Blueprint $table) {
            $table->string('rank')->nullable()->after('grade_status')->comment('رتبة الراوي: sahabi, sahabiyyah, tabii, rawi');
            $table->string('judgment')->nullable()->after('rank')->comment('حكم العلماء: thiqah, saduq, saduq_yukhti, daif, matruk, kadhdhab');

            $table->index('rank');
            $table->index('judgment');
        });

        // ترحيل البيانات القديمة من grade_status إلى rank + judgment
        DB::table('narrators')->where('grade_status', 'صحابي')->update(['rank' => 'sahabi', 'judgment' => null]);
        DB::table('narrators')->where('grade_status', 'صحابية')->update(['rank' => 'sahabiyyah', 'judgment' => null]);
        DB::table('narrators')->where('grade_status', 'ثقة')->update(['rank' => 'rawi', 'judgment' => 'thiqah']);
        DB::table('narrators')->where('grade_status', 'صدوق')->update(['rank' => 'rawi', 'judgment' => 'saduq']);
        DB::table('narrators')->where('grade_status', 'ضعيف')->update(['rank' => 'rawi', 'judgment' => 'daif']);
        DB::table('narrators')->where('grade_status', 'متروك')->update(['rank' => 'rawi', 'judgment' => 'matruk']);

        // الرواة الذين عندهم is_companion=true و لا يوجد لهم rank بعد
        DB::table('narrators')
            ->where('is_companion', true)
            ->whereNull('rank')
            ->update(['rank' => 'sahabi']);
    }

    public function down(): void
    {
        Schema::table('narrators', function (Blueprint $table) {
            $table->dropIndex(['rank']);
            $table->dropIndex(['judgment']);
            $table->dropColumn(['rank', 'judgment']);
        });
    }
};
