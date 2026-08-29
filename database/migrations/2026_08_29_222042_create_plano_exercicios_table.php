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
    Schema::create('plano_exercicios', function (Blueprint $table) {
        $table->id();
        $table->foreignId('plano_id')->constrained('plano_terapeuticos')->cascadeOnDelete();
        $table->foreignId('exercicio_id')->constrained('exercicios')->cascadeOnDelete();
        $table->date('data_atribuicao');
        $table->text('observacao_profissional')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_exercicios');
    }
};
