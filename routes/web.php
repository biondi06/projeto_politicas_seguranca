<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecurityController;

Route::get('/', function () {
    return view('ecoa');
})->name('landing');

Route::middleware(['auth', 'two_factor_required'])->group(function () {

    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // "password.confirm" força reautenticação de senha antes de entrar
    // aqui — necessário porque essa página controla o 2FA da conta.
    Route::middleware(['password.confirm'])->group(function () {
        Route::get('/seguranca', [SecurityController::class, 'index'])->name('security.index');
    });

});