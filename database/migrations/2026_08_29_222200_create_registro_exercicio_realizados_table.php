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
    Schema::create('registro_exercicio_realizados', function (Blueprint $table) {
        $table->id();
        $table->foreignId('registro_evolucao_id')->constrained('registro_evolucaos')->cascadeOnDelete();
        $table->foreignId('exercicio_id')->constrained('exercicios')->cascadeOnDelete();
        $table->boolean('realizado_corretamente')->default(false);
        $table->text('observacao')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_exercicio_realizados');
    }
};
