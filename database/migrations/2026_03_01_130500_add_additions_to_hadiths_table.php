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
            $table->json('additions')->nullable()->after('raw_text')->comment('زيادات المصادر - مثال: زاد (طب) في آخره: وهو أهونه علي');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropColumn('additions');
        });
    }
};
