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
use App\Http\Controllers\MainDashboardController;
use App\Http\Controllers\NotificatieController;

Route::get('/', function () {
    return view('welcome');
});

// Login routes (geen auth vereist)
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

// Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/MainDashboardPagina', [MainDashboardController::class, 'Maindashboard'])->name('MainDashboardPagina')->middleware('can:dashboard');
    Route::get('/dashboard/chart-data', [MainDashboardController::class, 'ChartData'])->name('dashboard.chartdata');
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
    Route::post('/bewijs/upload', [LidController::class, 'UploadBewijs'])->name('UploadBewijs');

    // Leden — verwijderen (alleen beheerder)
    Route::delete('/ledenpagina/delete/{lidId}', [PostController::class, 'destroy'])->name('ledenpagina.delete')->middleware('can:leden-verwijderen');

// Leden - Heractiveer
    Route::post('/leden/{lid_id}/heractiveer', [LidController::class, 'heractiveer'])->name('ledenpagina.heractiveer')->middleware('can:leden-heractiveren');
    // Leden - Deactiveer
    Route::post('/leden/{lid_id}/deactiveer', [LidController::class, 'deactiveer'])->name('ledenpagina.deactiveer')->middleware('can:leden-Deactiveren');

    // Verwijder dubbele betalingen (voor ledenpagina)
    Route::post('/lidpagina/removeduplicateBetalingen', [LidController::class, 'removeduplicateBetalingen'])->name('lidpagina.removeduplicateBetalingen');

// Betalingen
    Route::get('/betalingPagina', [BetalingController::class, 'index'])->name('betalingPagina')->middleware('can:betalingen-bekijken');
    Route::post('/betalingPagina/addBetaling', [BetalingController::class, 'store'])->name('betalingPagina.addBetaling.store')->middleware('can:betalingen-beheren');
    Route::put('/betalingen/{betaling}', [BetalingController::class, 'update'])->name('betalingen.update')->middleware('can:betalingen-beheren');
    Route::get('/betalingen/chart-data', [BetalingController::class, 'chartData'])->name('betalingen.chartData')->middleware('can:betalingen-bekijken');

// Verwijder dubbele betalingen (voor betalingenpagina)
    Route::post('/betalingen/remove-duplicates', [BetalingController::class, 'removeduplicateBetalingen'])->name('betalingen.removeDuplicates');
    Route::delete('/betalingen/{betaling}', [BetalingController::class, 'destroy'])->name('betalingen.destroy')->middleware('can:betalingen-beheren');

    Route::get('/betalingen/trashed', [BetalingController::class, 'trashed'])->name('betalingen.trashed')->middleware('can:betalingen-verwijderen');
    Route::patch('/betalingen/{betaling_id}/restore', [BetalingController::class, 'restore'])->name('betalingen.restore')->middleware('can:betalingen-verwijderen');

    // Notificaties
    Route::get('/notificaties', [NotificatieController::class, 'index'])->name('notificaties.index');
    Route::post('/notificaties/lezen', [NotificatieController::class, 'markeerGelezen'])->name('notificaties.lezen');

    // Verwijderde Betaling Records
    Route::get('/DeletedRecords', [BetalingController::class, 'trashed'])->name('DeletedRecords')->middleware('can:betalingen-verwijderen');

    // Bewijs
    Route::get('/BewijsReceived',                [BetalingController::class, 'showBewijsReceived'])->name('BewijsReceived')->middleware('can:betalingen-beheren');
    Route::get('/bewijs/{betaling_id}',          [BetalingController::class, 'ViewBewijsFile'])->name('ViewBewijsFile')->middleware('can:betalingen-beheren');
    Route::patch('/bewijs/{betaling_id}/approve',[BetalingController::class, 'ApproveBewijs'])->name('BewijsReceived.Approve')->middleware('can:betalingen-beheren');
    Route::patch('/bewijs/{betaling_id}/reject', [BetalingController::class, 'RejectBewijs'])->name('BewijsReceived.Reject')->middleware('can:betalingen-beheren');

    // Rapport & log
    Route::get('/RapportPagina', [RapportController::class, 'RapportageData'])->name('Rapport')->middleware('can:rapport-bekijken');
    Route::get('/ActiviteitLogPagina', [ActiviteitController::class, 'activiteitLogData'])->name('ActiviteitLog')->middleware('can:activiteitlog-bekijken');

    // Rollen & gebruikers
    Route::prefix('/rollen-beheer')->middleware('can:rollenbeheer')->group(function () {
        Route::get('/', [RolBeheerController::class, 'index'])->name('rollen-beheer');
        Route::post('/assign', [RolBeheerController::class, 'assignRole'])->name('rollen-beheer.assign');
        Route::get('/gebruiker/{userId}/roles', [RolBeheerController::class, 'getUserRoles'])->name('rollen-beheer.user-roles');
        Route::put('/gebruiker/{userId}/roles', [RolBeheerController::class, 'updateUserRoles'])->name('rollen-beheer.update');
        Route::get('/search-users', [RolBeheerController::class, 'searchUsers'])->name('rollen-beheer.search');
    });

    // Gebruikersbeheer
    Route::get('/GebruikersBeheerPagina', [GebruikerController::class, 'index'])->name('GebruikersBeheer')->middleware('can:gebruikersbeheer');
    Route::put('/GebruikersBeheerPagina/{userId}', [GebruikerController::class, 'update'])->name('GebruikersBeheer.update')->middleware('can:gebruikersbeheer');
    Route::post('GebruikersBeheerPagina/create', [GebruikerController::class, 'create'])->name('GebruikersBeheer.create')->middleware('can:gebruikersbeheer');
    Route::delete('/GebruikersBeheerPagina/{userId}', [GebruikerController::class, 'destroy'])->name('GebruikersBeheer.destroy')->middleware('can:gebruikersbeheer');
    Route::put('/gebruikers/{id}/deactiveer', [GebruikerController::class, 'deactiveer'])->name('GebruikersBeheer.deactiveer')->middleware('can:gebruikersbeheer');
    Route::put('/gebruikers/{id}/heractiveer', [GebruikerController::class, 'heractiveer'])->name('GebruikersBeheer.heractiveer')->middleware('can:gebruikersbeheer');
    Route::post('/gebruikers-beheer', [GebruikerController::class, 'store'])->name('GebruikersBeheer.store')->middleware('can:gebruikersbeheer');
    Route::get('/leden/{gebruiker_id}/koppel', [LidController::class, 'KoppelOfEdit'])->name('ledenpagina.koppel')->middleware('can:leden-beheren');
    Route::post('/leden/store', [LidController::class, 'store'])->name('ledenpagina.store')->middleware('can:leden-beheren');
});
