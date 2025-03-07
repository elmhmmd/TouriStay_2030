<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AnnonceController::class, 'index'])->name('admin.dashboard');
    Route::get('/tourist/dashboard', [App\Http\Controllers\TouristController::class, 'index'])->name('tourist.dashboard');
    Route::get('/proprietaire/dashboard', [App\Http\Controllers\AnnonceController::class, 'index'])->name('proprietaire.dashboard');
    Route::resource('annonces', App\Http\Controllers\AnnonceController::class)->only(['store', 'edit', 'update', 'destroy']);
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/types', [App\Http\Controllers\TypeDeLogementController::class, 'store'])->name('types.store');
    Route::delete('/types/{id}', [App\Http\Controllers\TypeDeLogementController::class, 'destroy'])->name('types.destroy');

    // Tourist Routes
    Route::get('/tourist/listings', [App\Http\Controllers\TouristController::class, 'listings'])->name('tourist.listings');
    Route::get('/tourist/book/{id}', [App\Http\Controllers\TouristController::class, 'book'])->name('tourist.book.form');
    Route::post('/tourist/book', [App\Http\Controllers\TouristController::class, 'storeBooking'])->name('tourist.book.store');
    Route::post('/tourist/favorites/{id}', [App\Http\Controllers\TouristController::class, 'addFavorite'])->name('tourist.favorites.add');
    Route::get('/tourist/favorites', [App\Http\Controllers\TouristController::class, 'favorites'])->name('tourist.favorites');
    Route::delete('/tourist/favorites/{id}', [App\Http\Controllers\TouristController::class, 'removeFavorite'])->name('tourist.favorites.remove');
    Route::get('/tourist/payment/{booking}', [App\Http\Controllers\TouristController::class, 'payment'])->name('tourist.payment');
    Route::post('/tourist/payment/{booking}/process', [App\Http\Controllers\TouristController::class, 'processPayment'])->name('tourist.payment.process');
    Route::get('/tourist/booking/{booking}/invoice', [App\Http\Controllers\TouristController::class, 'downloadInvoice'])->name('tourist.invoice.download');

    // Proprietaire Routes
    Route::post('/proprietaire/notifications/{id}/read', [App\Http\Controllers\AnnonceController::class, 'markNotificationAsRead'])->name('proprietaire.notifications.read');
});

require __DIR__.'/auth.php';
