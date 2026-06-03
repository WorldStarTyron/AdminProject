<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use App\Models\Lid;
use App\Http\Controllers\LidController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BetalingController;
use App\Http\Controllers\RolBeheerController;
use App\Http\Controllers\GebruikerController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ActiviteitController;

Route::get('/', function () {
    return view('welcome');
});
// Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/MainDashboardPagina', function () {
        return view('MainDashboardPagina');
    })->name('dashboard')->middleware('can:dashboard');
});

// Leden, Betalingen, Rapport, Log, Rollen & Gebruikers routes
Route::middleware(['auth'])->group(function () {
    
    // Lid — eigen profiel
    Route::get('/Lidpagina', [LidController::class, 'show'])->name('GegevensPagina')->middleware('can:eigen-profiel');

    // Leden — lezen
    Route::get('/ledenpagina', [LidController::class, 'index'])->name('ledenpagina')->middleware('can:leden-bekijken');
    Route::get('/ledenpagina/{lidId}', [PostController::class, 'show'])->name('ledenpagina.show')->middleware('can:leden-bekijken');

    // Leden — schrijven
    Route::post('/ledenpagina/addlid', [PostController::class, 'store'])->name('ledenpagina.addlid.store')->middleware('can:leden-beheren');
    Route::get('/ledenpagina/{lidId}/edit', [PostController::class, 'edit'])->name('ledenpagina.edit')->middleware('can:leden-beheren');
    Route::put('/ledenpagina/{lidId}', [PostController::class, 'update'])->name('ledenpagina.update')->middleware('can:leden-beheren');

    // Leden — verwijderen (alleen beheerder)
    Route::delete('/ledenpagina/delete/{lidId}', [PostController::class, 'destroy'])->name('ledenpagina.delete')->middleware('can:leden-verwijderen');

    // Betalingen
    Route::get('/betalingPagina', [BetalingController::class, 'index'])->name('betalingPagina')->middleware('can:betalingen-bekijken');
    Route::post('/betalingPagina/addBetaling', [BetalingController::class, 'store'])->name('betalingPagina.addBetaling.store')->middleware('can:betalingen-beheren');
    Route::patch('/betalingen/{betaling_id}', [BetalingController::class, 'update'])->name('betalingen.update')->middleware('can:betalingen-beheren');
    Route::delete('/betalingPagina/delete/{betaling_id}', [BetalingController::class, 'destroy'])->name('betalingPagina.delete')->middleware('can:betalingen-beheren');
    Route::post('/betalingen/check-subscriptie', [BetalingController::class, 'checkSubscriptie'])->name('betalingen.checkSubscriptie')->middleware('can:betalingen-beheren');
    Route::get('/betalingen/chart-data', [ChartController::class, 'chartData'])->name('betalingen.chartData')->middleware('can:betalingen-bekijken');

    // Rapport & log
    Route::get('/RapportPagina', [BetalingController::class, 'rapportageData'])->name('Rapport')->middleware('can:rapport-bekijken');
    Route::get('/ActiviteitLogPagina', [ActiviteitController::class, 'activiteitLogData'])->name('ActiviteitLog')->middleware('can:activiteitlog-bekijken');

    // Rollen & gebruikers
    Route::prefix('/rollen-beheer')->middleware('can:rollenbeheer')->group(function () {
        Route::get('/', [RolBeheerController::class, 'index'])->name('rollen-beheer');
        Route::post('/assign', [RolBeheerController::class, 'assignRole'])->name('rollen-beheer.assign');
        Route::get('/gebruiker/{userId}/roles', [RolBeheerController::class, 'getUserRoles'])->name('rollen-beheer.user-roles');
        Route::put('/gebruiker/{userId}/roles', [RolBeheerController::class, 'updateUserRoles'])->name('rollen-beheer.update');
        Route::get('/search-users', [RolBeheerController::class, 'searchUsers'])->name('rollen-beheer.search');
    });
    
    Route::get('/GebruikersBeheerPagina', [GebruikerController::class, 'index'])->name('GebruikersBeheer')->middleware('can:gebruikersbeheer');
});  

// Login routes
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Wachtwoord herstel routes (geen auth vereist)
Route::get('/Recover-password', [PasswordResetController::class, 'show'])->name('recover-password');
Route::post('/Recover-password', [PasswordResetController::class, 'sendResetCode'])->name('recover-password.post');
Route::get('/verify-code', [PasswordResetController::class, 'verifyCode'])->name('verify-code');
Route::post('/verify-code', [PasswordResetController::class, 'postVerifyCode'])->name('verify-code.post');
Route::get('/Recover-password/new-password', [PasswordResetController::class, 'newPassword'])->name('new-password');
Route::post('/Recover-password/new-password', [PasswordResetController::class, 'postNewPassword'])->name('new-password.post');
