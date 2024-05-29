<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware(['web']);

Route::post('/logout', [AuthController::class, 'logout'])->WithoutMiddleware(['web']);

Route::post('/usuarios/candidatos', [Controller::class, 'cadastrarCand'])->withoutMiddleware(['web']);

Route::post('/usuarios/empresa', [Controller::class, 'cadastrarEmp'])->withoutMiddleware(['web']);

Route::get('/usuario', [Controller::class, 'lerUsuario'])->withoutMiddleware(['web']);

Route::put('/usuario', [Controller::class, 'editarUsuario'])->withoutMiddleware(['web']);

Route::delete('/usuario', [Controller::class, 'apagarUsuario'])->withoutMiddleware(['web']);

Route::get('/competencias', [Controller::class, 'listarCompetencias'])->withoutMiddleware(['web']);

