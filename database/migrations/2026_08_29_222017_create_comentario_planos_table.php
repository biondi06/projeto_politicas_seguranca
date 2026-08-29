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
    Schema::create('comentario_planos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('plano_id')->constrained('plano_terapeuticos')->cascadeOnDelete();
        $table->foreignId('fonoaudiologo_id')->constrained('fonoaudiologos')->cascadeOnDelete();
        $table->text('comentario');
        $table->dateTime('data_hora');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentario_planos');
    }
};
