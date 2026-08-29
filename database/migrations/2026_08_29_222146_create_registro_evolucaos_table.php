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
    Schema::create('registro_evolucaos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('crianca_id')->constrained('criancas')->cascadeOnDelete();
        $table->foreignId('plano_id')->constrained('plano_terapeuticos')->cascadeOnDelete();
        $table->foreignId('relator_id')->constrained('users')->cascadeOnDelete();
        $table->date('data_registro');
        $table->string('origem');
        $table->text('observacoes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_evolucaos');
    }
};
