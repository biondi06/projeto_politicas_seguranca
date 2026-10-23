<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Convites para criar contas profissionais.
 *
 * O link do convite contém um token aleatório. Aqui guardamos apenas o hash
 * dele (SHA-256), do mesmo jeito que o Laravel faz com tokens de recuperação
 * de senha: quem tiver acesso de leitura ao banco não consegue montar um link
 * válido a partir desta tabela.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convites_acesso', function (Blueprint $table) {
            $table->id();
            $table->char('token_hash', 64)->unique();

            // O convite é só para este e-mail: o link não serve para outra pessoa.
            $table->string('email')->index();
            $table->string('perfil');

            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('expira_em');
            $table->timestamp('usado_em')->nullable();
            $table->timestamp('revogado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convites_acesso');
    }
};
