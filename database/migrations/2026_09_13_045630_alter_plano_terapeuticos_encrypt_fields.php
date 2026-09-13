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
    Schema::table('plano_terapeuticos', function (Blueprint $table) {
        $table->text('tipo_dificuldade')->change();
        $table->text('fonemas_alvo')->change();
    });
}

    public function down(): void
{
    Schema::table('plano_terapeuticos', function (Blueprint $table) {
        $table->string('tipo_dificuldade')->change();
        $table->string('fonemas_alvo')->change();
    });
}   
};
