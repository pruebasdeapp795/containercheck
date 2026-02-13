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
        Schema::create('precintos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('tipo');
            $table->integer('cantidad');
            $table->timestamp('fecha_ingreso')->useCurrent();
            $table->string('estado')->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precintos');
    }
};
