<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\LgpdController;
use App\Http\Controllers\AuditoriaController;

Route::get('/', function () {
    return view('ecoa');
})->name('landing');

Route::middleware(['auth', 'two_factor_required'])->group(function () {

    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/auditoria', [AuditoriaController::class, 'index'])
        ->name('auditoria.index');

    Route::post('/auditoria/verificar', [AuditoriaController::class, 'verificarIntegridade'])
        ->name('auditoria.verificar');

    // "password.confirm" força reautenticação de senha antes de entrar
    // aqui — necessário porque essa página controla o 2FA da conta.
    Route::middleware(['password.confirm'])->group(function () {
        Route::get('/seguranca', [SecurityController::class, 'index'])
            ->name('security.index');
        Route::get('/meus-dados', [LgpdController::class, 'meusDados'])
            ->name('lgpd.meus-dados');
        Route::get('/meus-dados/exportar', [LgpdController::class, 'exportar'])
            ->name('lgpd.exportar');
        Route::post('/meus-dados/revogar-consentimento', [LgpdController::class, 'revogarConsentimento'])
            ->name('lgpd.revogar');
        Route::delete('/meus-dados', [LgpdController::class, 'excluir'])
            ->name('lgpd.excluir');
    });

});

