<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RanqueamentoController;

Route::get('/',[IndexController::class, 'index']);
Route::resource('/ranqueamentos',RanqueamentoController::class);
Route::get('/admin/ingressantes',[AdminController::class, 'ingressantes']);
