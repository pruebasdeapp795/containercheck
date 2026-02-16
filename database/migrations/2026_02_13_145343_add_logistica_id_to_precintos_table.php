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
        Schema::table('precintos', function (Blueprint $table) {
            $table->foreignId('logistica_id')->nullable()->constrained('logisticas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('precintos', function (Blueprint $table) {
            $table->dropForeign(['logistica_id']);
            $table->dropColumn('logistica_id');
        });
    }
};
