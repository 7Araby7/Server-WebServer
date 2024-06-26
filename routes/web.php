<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompController;
use App\Http\Controllers\VagaController;
use App\Http\Controllers\RamoController;
use App\Http\Controllers\MensagemController;

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

Route::post('/usuarios/candidatos', [UserController::class, 'cadastrarCand'])->withoutMiddleware(['web']);

Route::post('/usuarios/empresa', [UserController::class, 'cadastrarEmp'])->withoutMiddleware(['web']);

Route::get('/usuario', [UserController::class, 'lerUsuario'])->withoutMiddleware(['web']);

Route::get('/usuario/logado', [UserController::class, 'usuariosLogados'])->withoutMiddleware(['web']);

Route::post('/usuarios/candidatos/buscar', [UserController::class, 'buscarUsuario'])->withoutMiddleware(['web']);

Route::put('/usuario', [UserController::class, 'editarUsuario'])->withoutMiddleware(['web']);

Route::delete('/usuario', [UserController::class, 'apagarUsuario'])->withoutMiddleware(['web']);

Route::get('/competencias', [CompController::class, 'listarCompetencias'])->withoutMiddleware(['web']);

Route::get('/ramos', [RamoController::class, 'listarRamos'])->withoutMiddleware(['web']);

Route::post('/vagas', [VagaController::class, 'cadastrarVaga'])->withoutMiddleware(['web']);

Route::get('/vagas', [VagaController::class, 'listarVagas'])->withoutMiddleware(['web']);

Route::put('/vagas/{id}', [VagaController::class, 'editarVaga'])->withoutMiddleware(['web']);

Route::get('/vagas/{id}', [VagaController::class, 'buscarVaga'])->withoutMiddleware(['web']);

Route::delete('/vagas/{id}', [VagaController::class, 'excluirVaga'])->withoutMiddleware(['web']);

Route::post('/mensagem', [MensagemController::class, 'enviarMensagem'])->withoutMiddleware(['web']);

Route::get('/mensagem', [MensagemController::class, 'lerMensagem'])->withoutMiddleware(['web']);