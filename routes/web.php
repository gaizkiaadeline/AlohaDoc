<?php

use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginRegisterController::class, 'landing'])->name('landing');
    Route::get('/admin', [LoginRegisterController::class, 'loginAdmin'])->name('admin.login');
    Route::post('/login', [LoginRegisterController::class, 'loginProcess'])->name('login');
    Route::post('/register', [LoginRegisterController::class, 'registerProcess'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
    Route::get('/consultation', [ConsultationController::class, 'index'])->name('consultation');

    Route::middleware('checkRole:admin')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user');
    });
});