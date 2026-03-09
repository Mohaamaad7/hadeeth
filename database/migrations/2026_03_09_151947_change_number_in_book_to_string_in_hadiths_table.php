<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * تحويل number_in_book من integer إلى string لدعم أرقام مركبة مثل 3222-1 أو 3222/1
     */
    public function up(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->string('number_in_book', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->integer('number_in_book')->nullable()->change();
        });
    }
};
