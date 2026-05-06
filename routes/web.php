<?php

use Illuminate\Support\Facades\Route;
use App\Models\Lid; // 1. Import the model
use App\Http\Controllers\LidController;  // 2. Import the controller
use App\Http\Controllers\PostController; // Import the Controller from PostController
use App\Http\Controllers\ChartController;

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

//leden routes
Route::get('/ledenpagina', [LidController::class, 'index'])->name('ledenpagina');

//Leden Toevoegen
Route::resource('ledenpagina/addlid', PostController::class);


//betalingen routes
Route::get('/betalingPagina', function() {
    return view('BetalingPagina');
})->name('betalingPagina');


// dashboard routes
Route::get('/dashboard',function(){
    return view('MainDashboardPagina');
})->name('dashboard');
