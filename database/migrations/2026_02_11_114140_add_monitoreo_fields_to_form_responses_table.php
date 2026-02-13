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
        Schema::table('form_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('form_responses', 'monitoreo_signature')) {
                $table->longText('monitoreo_signature')->nullable();
            }
            if (!Schema::hasColumn('form_responses', 'monitoreo_signed_at')) {
                $table->timestamp('monitoreo_signed_at')->nullable();
            }
            if (!Schema::hasColumn('form_responses', 'monitoreo_user_id')) {
                $table->foreignId('monitoreo_user_id')->nullable()->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropForeign(['monitoreo_user_id']);
            $table->dropColumn(['monitoreo_signature', 'monitoreo_signed_at', 'monitoreo_user_id']);
        });
    }
};
