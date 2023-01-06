<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use Illuminate\Routing\Controllers\Middleware;
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

Route::get('/', [DoctorController::class, 'index'])->middleware(['auth']);

Route::get('/patient/create', [PatientController::class, 'create'])->middleware(['auth']);
Route::get('/doctor/create', [DoctorController::class, 'create'])->name('add');

Route::get('/patient/{id}', [PatientController::class, 'show'])->middleware(['auth']);
Route::get('/doctor/{id}', [DoctorController::class, 'showDoc'])->middleware(['auth']);

Route::post('/patient/store', [PatientController::class, 'store'])->middleware(['auth']);
Route::post('/doctor/store', [DoctorController::class, 'store'])->middleware(['auth']);

Route::post('/exam/store', [ExamController::class, 'store'])->middleware(['auth']);