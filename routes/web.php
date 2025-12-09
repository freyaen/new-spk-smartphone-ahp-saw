<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AHPController;
use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\SAWController;
use App\Http\Controllers\LoginController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('index');

Route::get('units', [UnitController::class, 'index'])->name('units.index');
Route::post('units', [UnitController::class, 'store'])->name('units.store');
Route::put('units/{uuid}', [UnitController::class, 'update'])->name('units.update');
Route::delete('units/{uuid}', [UnitController::class, 'destroy'])->name('units.destroy');

Route::get('alternatives', [AlternativeController::class, 'index'])->name('alternatives.index');
Route::post('alternatives', [AlternativeController::class, 'store'])->name('alternatives.store');
Route::put('alternatives/{uuid}', [AlternativeController::class, 'update'])->name('alternatives.update');
Route::delete('alternatives/{uuid}', [AlternativeController::class, 'destroy'])->name('alternatives.destroy');

Route::get('criterias', [CriteriaController::class, 'index'])->name('criterias.index');
Route::post('criterias', [CriteriaController::class, 'store'])->name('criterias.store');
Route::put('criterias/{uuid}', [CriteriaController::class, 'update'])->name('criterias.update');
Route::delete('criterias/{uuid}', [CriteriaController::class, 'destroy'])->name('criterias.destroy');

Route::get('ahp', [AHPController::class, 'index'])->name('ahp.index');
Route::post('ahp', [AHPController::class, 'store'])->name('ahp.store');

Route::get('saw', [SAWController::class, 'index'])->name('saw.index');

