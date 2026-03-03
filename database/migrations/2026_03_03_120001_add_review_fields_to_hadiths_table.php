<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->index()
                ->after('grade');
            $table->foreignId('entered_by')
                ->nullable()
                ->after('narrator_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('entered_by')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_notes')->nullable()->after('reviewed_at');
        });

        // الأحاديث الموجودة حالياً: اعتبارها معتمدة
        \App\Models\Hadith::query()->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropForeign(['entered_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['status', 'entered_by', 'reviewed_by', 'reviewed_at', 'review_notes']);
        });
    }
};
