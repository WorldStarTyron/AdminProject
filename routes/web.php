<?php

use Illuminate\Support\Facades\Route;
use App\Models\Lid;
use App\Http\Controllers\LidController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BetalingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Leden routes
Route::get('/ledenpagina', [LidController::class, 'index'])->name('ledenpagina');

// Leden toevoegen
Route::post('/ledenpagina/addlid', [PostController::class, 'store'])->name('ledenpagina.addlid.store');

// Betalingen routes
Route::get('/betalingPagina', [BetalingController::class, 'index'])->name('betalingPagina');
Route::post('/betalingPagina/addBetaling', [BetalingController::class, 'store'])->name('betalingPagina.addBetaling.store');

// Dashboard routes
Route::get('/dashboard', function () {
    return view('MainDashboardPagina');
})->name('dashboard');

// Login routes
// FIX: GET en POST mogen niet dezelfde naam hebben → POST heet nu 'login.post'
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');   // ✅ FIX: naam was duplicate
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');