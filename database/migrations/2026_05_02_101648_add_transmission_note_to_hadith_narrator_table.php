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
        Schema::table('hadith_narrator', function (Blueprint $table) {
            $table->string('transmission_note')->nullable()->after('narrator_id')->comment('مثل: مرسلا، معضلا، مقطوعا');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hadith_narrator', function (Blueprint $table) {
            $table->dropColumn('transmission_note');
        });
    }
};
