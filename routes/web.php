<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\LgpdController;
use App\Http\Controllers\AuditoriaController;

Route::get('/', function () {
    return view('ecoa');
})->name('landing');

Route::middleware(['auth', 'two_factor_required'])->group(function () {

    // A home abre sempre, como antes. Ela só avisa quando não há consentimento.
    Route::get('/home', function () {
        $usuario = auth()->user();

        return view('home', [
            'usuario' => $usuario,
            'ultimoConsentimento' => $usuario->consentimentos()->latest('aceito_em')->first(),
        ]);
    })->name('home');

    // Áreas que tratam dados exigem consentimento ativo (requisito 4.6).
    // Sem ele, o middleware "consent_active" leva o usuário a /meus-dados.
    Route::middleware('consent_active')->group(function () {
        Route::get('/auditoria', [AuditoriaController::class, 'index'])
            ->name('auditoria.index');

        Route::post('/auditoria/verificar', [AuditoriaController::class, 'verificarIntegridade'])
            ->name('auditoria.verificar');
    });

    // "password.confirm" força reautenticação de senha antes de entrar aqui.
    // As rotas de direitos do titular ficam FORA de "consent_active", para o
    // usuário sem consentimento ainda conseguir consultar, exportar, excluir
    // ou conceder um novo consentimento.
    Route::middleware(['password.confirm'])->group(function () {
        Route::get('/seguranca', [SecurityController::class, 'index'])
            ->name('security.index');
        Route::get('/meus-dados', [LgpdController::class, 'meusDados'])
            ->name('lgpd.meus-dados');
        Route::get('/meus-dados/exportar', [LgpdController::class, 'exportar'])
            ->name('lgpd.exportar');
        Route::post('/meus-dados/consentir', [LgpdController::class, 'consentir'])
            ->name('lgpd.consentir');
        Route::post('/meus-dados/revogar-consentimento', [LgpdController::class, 'revogarConsentimento'])
            ->name('lgpd.revogar');
        Route::delete('/meus-dados', [LgpdController::class, 'excluir'])
            ->name('lgpd.excluir');
    });

});