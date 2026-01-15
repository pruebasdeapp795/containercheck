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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained('phases')->onDelete('cascade');
            $table->string('label');
            $table->string('column_name');
            $table->string('type'); // text, number, select, date, signature', 'file']);
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // For select inputs
            $table->json('conditionals')->nullable(); // For logic rules
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
