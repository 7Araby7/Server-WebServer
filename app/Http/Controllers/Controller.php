<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        //
    }

    public function cadastrarCand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'senha' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['mensagem' => 'Erro ao cadastrar usuário'], 422);
        }

        try {
            $user = new User();
            $user->nome = $request->input('nome');
            $user->email = $request->input('email');
            $user->senha = bcrypt($request->input('senha'));
            $user->tipo = 'candidato';
            $user->ramo = null;
            $user->descricao = null;
            $user->save();

            return response()->json(['mensagem' => 'Usuário cadastrado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function cadastrarEmp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'senha' => 'required|string|min:8',
            'ramo' => 'required|string|min:3',
            'descricao' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['mensagem' => 'Erro ao cadastrar usuário'], 422);
        }

        try {
            $user = new User();
            $user->nome = $request->input('nome');
            $user->email = $request->input('email');
            $user->senha = bcrypt($request->input('senha'));
            $user->tipo = 'empresa';
            $user->ramo = $request->input('ramo');
            $user->descricao = $request->input('descricao');
            $user->save();

            return response()->json(['mensagem' => 'Usuário cadastrado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function lerUsuario(Request $request){
        try {
            $token = $request->bearerToken();

            $competencias = [
                [
                    'id' => 1,
                    'nome' => 'Competência um'
                ]
            ];

            $experiencia = [
                [
                    'id' => 1,
                    'nome_empresa' => 'Empresa um',
                    'inicio' => '2001',
                    'fim' => '2011',
                    'cargo' => 'cargo um'
                ],
                [
                    'id' => 2,
                    'nome_empresa' => 'Empresa dois',
                    'inicio' => '2012',
                    'fim' => '2022',
                    'cargo' => 'cargo dois'
                ]
            ];
            
            if (!$token) {
                return response()->json(['mensagem' => 'Nenhum token enviado'], 401);
            }
    
            $user = User::where('token', $token)->first();
            if ($user) {
                if($user->tipo === 'empresa'){
                    
                    return response()->json([
                        'nome' => $user->nome,
                        'email' => $user->email,
                        'ramo' => $user->ramo,
                        'descricao' => $user->descricao,
                        'tipo' => $user->tipo,
                    ], 200);
                }else{
                    return response()->json([
                        'nome' => $user->nome,
                        'email' => $user->email,
                        'tipo' => $user->tipo,
                        'competencias' => $competencias,
                        'experiencia' => $experiencia,
                    ], 200);
                }
            }else{
                return response()->json(['mensagem' => 'Token não encontrado'], 401);
            }
    
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'erro interno: ' . $e->getMessage()], 500);
        }
    }

}
