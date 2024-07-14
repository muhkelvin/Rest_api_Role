<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


Route::get('users', [\App\Http\Controllers\AuthController::class, 'index'])->name('users.index');
Route::post('register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('login',[\App\Http\Controllers\AuthController::class,'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum', 'role:user|admin']], function () {
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify']);

});


Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/{book}', [\App\Http\Controllers\BookController::class, 'show'])->name('users.show');
// Route untuk BookController dengan middleware admin
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('books/create', [BookController::class, 'create'])->name('books.create');
    Route::put('books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Route untuk mengubah status payment
    Route::patch('payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
});

