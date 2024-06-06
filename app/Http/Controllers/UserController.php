<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Competencia;
use App\Models\Experiencia;
use App\Models\CompetenciaUser;

class UserController extends BaseController
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

    public function lerUsuario(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['mensagem' => 'Nenhum token enviado'], 401);
            }

            $user = User::where('token', $token)->first();

            
            if ($user) {
                if ($user->tipo === 'empresa') {
                    
                    return response()->json([
                        'nome' => $user->nome,
                        'email' => $user->email,
                        'ramo' => $user->ramo,
                        'descricao' => $user->descricao,
                        'tipo' => $user->tipo,
                    ], 200);
                } else {
                    $competenciaIds = CompetenciaUser::where('user_id', $user->id)->pluck('competencia_id');
        
                    $competencias = Competencia::whereIn('id', $competenciaIds)->get();
        
                    $experiencias = Experiencia::where('user_id', $user->id)->get();
                    return response()->json([
                        'nome' => $user->nome,
                        'email' => $user->email,
                        'tipo' => $user->tipo,
                        'competencias' => $competencias,
                        'experiencia' => $experiencias,
                    ], 200);
                }
            } else {
                return response()->json(['mensagem' => 'Token não encontrado'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function editarUsuario(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['mensagem' => 'Nenhum token enviado'], 401);
            }

            $user = User::where('token', $token)->first();

            if (!$user) {
                return response()->json(['mensagem' => 'Usuário não encontrado'], 401);
            }

            $validator = Validator::make($request->all(), [
                'nome' => 'required|string|max:255',
                'senha' => 'nullable|string|min:8',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            if ($validator->fails()) {
                return response()->json(['mensagem' => 'Erro de validação', 'errors' => $validator->errors()], 401);
            }

            if ($user->tipo === 'empresa') {
                $user->ramo = $request->input('ramo');
                $user->descricao = $request->input('descricao');
            }
            
            if ($user->tipo === 'candidato') {

                $competenciaIds = CompetenciaUser::where('user_id', $user->id)->pluck('competencia_id')->toArray();
                $experienciaIds = Experiencia::where('user_id', $user->id)->pluck('id')->toArray();
                
                if ($request->has('competencias')) {
                    $competencias = $request->input('competencias');
                    
                    $competenciasRemover = array_diff($competenciaIds, array_column($competencias, 'id'));
                    CompetenciaUser::where('user_id', $user->id)->whereIn('competencia_id', $competenciasRemover)->delete();
                    
                    foreach ($competencias as $competenciaData) {

                        if (!in_array($competenciaData['id'], $competenciaIds)) {

                            $competenciaUser = new CompetenciaUser();
                            $competenciaUser->user_id = $user->id;
                            $competenciaUser->competencia_id = $competenciaData['id'];
                            $competenciaUser->save();
                        }
                    }
                } else {
                    CompetenciaUser::where('user_id', $user->id)->delete();
                }

                if ($request->has('experiencia')) {
                    $experiencias = $request->input('experiencia');

                    $experienciasRemover = array_diff($experienciaIds, array_column($experiencias, 'id'));
                    Experiencia::where('user_id', $user->id)->whereIn('id', $experienciasRemover)->delete();

                    foreach ($experiencias as $experienciaData) {
                        $experiencia = Experiencia::find($experienciaData['id']);
                        if ($experiencia) {
                            $experiencia->nome_empresa = $experienciaData['nome_empresa'];
                            $experiencia->inicio = $experienciaData['inicio'];
                            $experiencia->fim = $experienciaData['fim'];
                            $experiencia->cargo = $experienciaData['cargo'];
                        } else {
                            $experiencia = new Experiencia();
                            $experiencia->nome_empresa = $experienciaData['nome_empresa'];
                            $experiencia->inicio = $experienciaData['inicio'];
                            $experiencia->fim = $experienciaData['fim'];
                            $experiencia->cargo = $experienciaData['cargo'];
                            $experiencia->user_id = $user->id;
                        }
                        $experiencia->save();
                    }
                } else {
                    Experiencia::where('user_id', $user->id)->delete();
                }
            }
            
            $user->nome = $request->input('nome');
            $user->email = $request->input('email');
            if(!($request->input('senha') === null)){
                $user->senha = bcrypt($request->input('senha'));
            }
            $user->save();
            
            return response()->json(['mensagem' => 'Usuário atualizado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function apagarUsuario(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['mensagem' => 'Nenhum token enviado'], 401);
            }

            $user = User::where('token', $token)->first();

            if (!$user) {
                return response()->json(['mensagem' => 'Usuário não encontrado'], 404);
            }

            $user->delete();

            return response()->json(['mensagem' => 'Usuário apagado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

}
