<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RanqueamentoController;
use App\Http\Controllers\EscolhaController;

Route::get('/',[IndexController::class, 'index']);
Route::resource('/ranqueamentos',RanqueamentoController::class);
Route::get('/admin/ciclo_basico',[AdminController::class, 'ciclo_basico']);
Route::post('/declinar',[EscolhaController::class, 'declinar'])->name('declinar');
Route::get('/escolhas',[EscolhaController::class, 'form'])->name('escolhas_form');
Route::post('/escolhas',[EscolhaController::class, 'store'])->name('escolhas_store');
