<?php

use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\LoginRegisterController;
use App\Http\Controllers\ScheduleController;
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

    Route::prefix('consultation')->group(function () {
        Route::get('/', [ConsultationController::class, 'index'])->name('consultation');
        Route::post('/', [ConsultationController::class, 'store'])->name('consultation.store');
        Route::get('/getSession', [ConsultationController::class, 'getSession'])->name('consultation.get-session');
        Route::get('/cancel/{consultation}', [ConsultationController::class, 'cancel'])->name('consultation.cancel');
        Route::get('/do/{consultation}', [ConsultationController::class, 'do'])->name('consultation.do');
        Route::get('/activate/{consultation}/{setActive}', [ConsultationController::class, 'activateRequest'])->name('consultation.activate');
    });

    Route::middleware('checkRole:admin')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::get('/changeStatus/{user}', [UserController::class, 'changeStatus'])->name('user.activate');
    });

    Route::middleware('checkRole:doctor')->group(function () {
        Route::prefix('schedule')->group(function () {
            Route::get('/', [ScheduleController::class, 'index'])->name('schedule');
            Route::get('/activate/{schedule}/{user}/{setActive}', [ScheduleController::class, 'activateSchedule'])->name('schedule.activate');
        });
    });
});