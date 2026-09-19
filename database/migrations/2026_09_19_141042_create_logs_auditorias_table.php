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
    Schema::create('logs_auditorias', function (Blueprint $table) {
        $table->id();
        $table->string('evento'); // login_sucesso, login_falha, 2fa_falha, reset_senha, etc.
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->string('email')->nullable();
        $table->string('ip')->nullable();
        $table->json('detalhes')->nullable();
        $table->char('hash_anterior', 64);
        $table->char('hash_atual', 64);
        $table->timestamp('created_at');
    });
}

public function down(): void
{
    Schema::dropIfExists('logs_auditorias');
}
};
