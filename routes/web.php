<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInvitationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Accepte une invitation avec un token
Route::get('/invitations/accept/{token}', [GroupInvitationController::class, 'accept'])
    ->name('invitations.accept');

// (Optionnel) Formulaire de création de compte après invitation
Route::get('/register-from-invitation', [GroupInvitationController::class, 'showRegistrationForm'])
    ->name('invitations.register.form');

Route::post('/register-from-invitation', [GroupInvitationController::class, 'registerFromInvitation'])
    ->name('invitations.register.submit');




// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('dashboard', [GroupController::class, 'index'])->name('dashboard'); 
    // Envoie une invitation à un email donné
    Route::post('/groups/{group}/invite', [GroupInvitationController::class, 'invite'])
        ->name('groups.invite');;
    // Ajouter une cotisation pour un groupe
    Route::post('/groups/{group}/cotisations', [GroupController::class, 'storeCotisation'])
        ->name('groups.cotisations.store');
    
    Route::resource('groups', GroupController::class);
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
