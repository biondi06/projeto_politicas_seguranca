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
    Schema::table('users', function (Blueprint $table) {
        $table->string('perfil')->nullable()->after('email');
        $table->boolean('two_factor_ativo')->default(false)->after('perfil');
    });
}

    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['perfil', 'two_factor_ativo']);
    });
}
};
