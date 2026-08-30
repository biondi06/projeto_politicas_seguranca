<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page Pública
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('ecoa');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Área Autenticada
|--------------------------------------------------------------------------
|
| Apenas usuários autenticados podem acessar o painel.
|
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/home', function () {
        return view('home');
    })->name('home');

});