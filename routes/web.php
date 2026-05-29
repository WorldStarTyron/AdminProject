<?php

use Illuminate\Support\Facades\Route;
use App\Models\Lid;
use App\Http\Controllers\LidController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BetalingController;
use App\Http\Controllers\RolBeheerController;
use App\Http\Controllers\GebruikerController;
use App\Http\Controllers\PasswordResetController;

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



Route::middleware(['auth'])->group(function(){
    // Leden routes
    Route::get('/ledenpagina', [LidController::class, 'index'])->name('ledenpagina');
    // Leden toevoegen
    Route::post('/ledenpagina/addlid', [PostController::class, 'store'])->name('ledenpagina.addlid.store');
    // Leden verwijderen
    Route::delete('/ledenpagina/delete/{lidId}', [PostController::class, 'destroy'])->name('ledenpagina.delete');
    // Lid bekijken
    Route::get('/ledenpagina/{lidId}', [PostController::class, 'show'])->name('ledenpagina.show');
    // Leden Bewerken
    Route::get('/ledenpagina/{lidId}/edit', [PostController::class, 'edit'])->name('ledenpagina.edit');
    Route::put('/ledenpagina/{lidId}', [PostController::class, 'update'])->name('ledenpagina.update');
    // Ledenpagina routes
    Route::get('/Lidpagina', [LidController::class, 'show'])->name('GegevensPagina'); 
}); 


Route::middleware(['auth'])->group(function(){
    // Betalingen routes
    Route::get('/betalingPagina', [BetalingController::class, 'index'])->name('betalingPagina');
    Route::post('/betalingPagina/addBetaling', [BetalingController::class, 'store'])->name('betalingPagina.addBetaling.store');
    //Betalingen Chart Data
    Route::get('/betalingen/chart-data', [ChartController::class, 'chartData'])->name('betalingen.chartData');
    Route::delete('/betalingPagina/delete/{betaling_id}', [BetalingController::class, 'destroy'])->name('betalingPagina.delete');
    // Dashboard routes
    Route::get('/dashboard', function () {return view('MainDashboardPagina');})->name('dashboard');
});
  

// Rol routes
Route::middleware(['auth'])->group(function () { // Add your own auth middleware if needed
    Route::get('/rollen-beheer', [RolBeheerController::class, 'index'])->name('rollen-beheer');
    Route::post('/rollen-beheer/assign', [RolBeheerController::class, 'assignRole'])->name('rollen-beheer.assign');
    Route::get('/rollen-beheer/gebruiker/{userId}/roles', [RolBeheerController::class, 'getUserRoles'])->name('rollen-beheer.user-roles');
    Route::put('/rollen-beheer/gebruiker/{userId}/roles', [RolBeheerController::class, 'updateUserRoles'])->name('rollen-beheer.update');
    Route::get('/rollen-beheer/search-users', [RolBeheerController::class, 'searchUsers'])->name('rollen-beheer.search');
});

// GebruikerBeheer Route
Route::middleware(['auth'])->group(function () {
    Route::get('/GebruikersBeheerPagina', [GebruikerController::class, 'index'])->name('GebruikersBeheer');
    Route::get('/gebruikers', [GebruikerController::class, 'index'])->name('gebruikers.index');
});

//Rapportage Route
Route::middleware(['auth'])->group(function(){
    Route::get('/RapportagePagina', [RapportageController::class, 'index'])->name('RapportagePagina');
});

// Login routes
Route::get('/login', function () {return view('login');})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');  
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/Recover-password',[PasswordResetController::class, 'show'])->name('recover-password');
//Route::post('/Recover-password',[PasswordResetController::class, 'sendResetCode'])->name('recover-password.post');

Route::get('/verify-code',[PasswordResetController::class, 'verifyCode'])->name('verify-code');
//Route::post('/verify-code',[PasswordResetController::class, 'postVerifyCode'])->name('verify-code.post');

Route::get('/Recover-password/new-password',[PasswordResetController::class, 'newPassword'])->name('new-password');
//Route::post('/Recover-password/new-password',[PasswordResetController::class, 'postNewPassword'])->name('new-password.post');

    Route::middleware(['auth'])->group(function (){
    Route::get('/RollenBeheerPagina', function(){ return view('RollenBeheerPagina'); })->name('RollenBeheerPagina'); 
});