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
        Schema::table('inspection_signatures', function (Blueprint $table) {
            $table->string('vest_number')->nullable()->after('role_in_inspection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_signatures', function (Blueprint $table) {
            $table->dropColumn('vest_number');
        });
    }
};
