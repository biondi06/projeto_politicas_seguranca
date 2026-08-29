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
    Schema::create('plano_terapeuticos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('crianca_id')->constrained('criancas')->cascadeOnDelete();
        $table->foreignId('fonoaudiologo_responsavel_id')->constrained('fonoaudiologos')->cascadeOnDelete();
        $table->string('tipo_dificuldade');
        $table->string('fonemas_alvo');
        $table->text('metas');
        $table->string('status')->default('ativo');
        $table->date('data_criacao');
        $table->date('data_atualizacao')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_terapeuticos');
    }
};
