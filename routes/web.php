<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\RanqueamentoController;
use App\Http\Controllers\EscolhaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\ScoreController;

Route::get('/',[IndexController::class, 'index']);

Route::get('/ranqueamentos/ciclo_basico/{ranqueamento}',[RanqueamentoController::class, 'ciclo_basico'])->name('ciclo_basico');
Route::resource('/ranqueamentos',RanqueamentoController::class);

Route::post('/declinar',[EscolhaController::class, 'declinar'])->name('declinar');
Route::get('/escolhas',[EscolhaController::class, 'form'])->name('escolhas_form');
Route::post('/escolhas',[EscolhaController::class, 'store'])->name('escolhas_store');
Route::get('/escolhas/{ranqueamento}',[EscolhaController::class, 'index'])->name('escolhas_index');

Route::get('/notas/{codpes}', [NotaController::class, 'show']);
Route::get('/hist/{codpes}', [NotaController::class, 'hist']);
Route::get('/excel/{ranqueamento}', [EscolhaController::class, 'excel']);

Route::get('/scores/csv/{ranqueamento}', [ScoreController::class, 'csv']);
Route::get('/scores/{ranqueamento}', [ScoreController::class, 'show']);
Route::post('/scores/{ranqueamento}', [ScoreController::class, 'update']);



