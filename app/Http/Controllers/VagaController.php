<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Vaga;
use App\Models\CompetenciaVaga;
use App\Models\Competencia;
use App\Models\Ramo;
use App\Models\User;

class VagaController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function cadastrarVaga(Request $request)
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
                'ramo_id' => 'required|exists:ramos,id',
                'titulo' => 'required|string|max:255',
                'descricao' => 'required|string',
                'experiencia' => 'required|integer|min:0',
                'salario_min' => 'required|numeric|min:0',
                'salario_max' => 'nullable|numeric',
                'ativo' => 'required|boolean',
                'competencias' => 'required|array',
                'competencias.*.id' => 'required|exists:competencias,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['mensagem' => 'Erro de validação', 'errors' => $validator->errors()], 401);
            }

            $vaga = new Vaga();
            $vaga->ramo_id = $request->input('ramo_id');
            $vaga->empresa_id = $user->id;
            $vaga->titulo = $request->input('titulo');
            $vaga->descricao = $request->input('descricao');
            $vaga->experiencia = $request->input('experiencia');
            $vaga->salario_min = $request->input('salario_min');
            $vaga->salario_max = $request->input('salario_max');
            $vaga->ativo = $request->input('ativo');
            $vaga->save();

            foreach ($request->input('competencias') as $competencia) {
                $competenciaVaga = new CompetenciaVaga();
                $competenciaVaga->vaga_id = $vaga->id;
                $competenciaVaga->competencia_id = $competencia['id'];
                $competenciaVaga->save();
            }

            return response()->json(['mensagem' => 'Vaga cadastrada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function editarVaga(Request $request, $id)
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
                'ramo_id' => 'required|exists:ramos,id',
                'titulo' => 'required|string|max:255',
                'descricao' => 'required|string',
                'experiencia' => 'required|integer|min:0',
                'salario_min' => 'required|numeric|min:0',
                'salario_max' => 'nullable|numeric',
                'ativo' => 'required|boolean',
                'competencias' => 'required|array',
                'competencias.*.id' => 'required|exists:competencias,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['mensagem' => 'Erro de validação', 'errors' => $validator->errors()], 422);
            }

            $vaga = Vaga::find($id);

            if (!$vaga) {
                return response()->json(['mensagem' => 'Vaga não encontrada'], 401);
            }

            if ($vaga->empresa_id != $user->id) {
                return response()->json(['mensagem' => 'Vaga de outra empresa'], 401);
            }

            $vaga->ramo_id = $request->input('ramo_id');
            $vaga->titulo = $request->input('titulo');
            $vaga->descricao = $request->input('descricao');
            $vaga->experiencia = $request->input('experiencia');
            $vaga->salario_min = $request->input('salario_min');
            $vaga->salario_max = $request->input('salario_max');
            $vaga->ativo = $request->input('ativo');
            $vaga->save();

            CompetenciaVaga::where('vaga_id', $vaga->id)->delete();

            foreach ($request->input('competencias') as $competencia) {
                $competenciaVaga = new CompetenciaVaga();
                $competenciaVaga->vaga_id = $vaga->id;
                $competenciaVaga->competencia_id = $competencia['id'];
                $competenciaVaga->save();
            }

            return response()->json(['mensagem' => 'Vaga atualizada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function excluirVaga(Request $request, $id)
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

            $vaga = Vaga::find($id);

            if (!$vaga) {
                return response()->json(['mensagem' => 'Vaga não encontrada'], 401);
            }

            if ($vaga->empresa_id != $user->id) {
                return response()->json(['mensagem' => 'Vaga de outra empresa'], 401);
            }

            $vaga->delete();

            return response()->json(['mensagem' => 'Vaga excluída com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function listarVagas(Request $request)
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

            $empresa_id = $user->id;
            $vagas = Vaga::where('empresa_id', $empresa_id)->get();

            if ($vagas == '[]') {
                return response()->json(['mensagem' => 'Nenhuma vaga cadastrada'], 401);
            }

            $vagasArray = array();
            foreach ($vagas as $vaga) {

                $competenciaIds = CompetenciaVaga::where('vaga_id', $vaga->id)->pluck('competencia_id');
                $competencias = Competencia::whereIn('id', $competenciaIds)->get();

                $ramo = Ramo::where('id', $vaga->ramo_id)->get();

                if ($vaga->ativo == 1) {
                    $ativo = true;
                } else {
                    $ativo = false;
                }

                $vagaArray = array(
                    'id' => $vaga->id,
                    'titulo' => $vaga->titulo,
                    'descricao' => $vaga->descricao,
                    'competencias' => $competencias,
                    'experiencia' => $vaga->experiencia,
                    'salario_min' => $vaga->salario_min,
                    'salario_max' => $vaga->salario_max,
                    'empresa_id' => $vaga->empresa_id,
                    'ativo' => $ativo,
                    'ramo' => [
                        'id' => $ramo[0]->id,
                        'nome' => $ramo[0]->nome,
                        'descricao' => $ramo[0]->descricao
                    ]
                );
                array_push($vagasArray, $vagaArray);
            }

            return response()->json($vagasArray, 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    public function buscarVaga(Request $request, $id)
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

            $vaga = Vaga::find($id);

            if (!$vaga) {
                return response()->json(['mensagem' => 'Vaga não encontrada'], 401);
            }

            if ($vaga->empresa_id != $user->id) {
                return response()->json(['mensagem' => 'Vaga de outra empresa'], 401);
            }


            $competenciaIds = CompetenciaVaga::where('vaga_id', $vaga->id)->pluck('competencia_id');
            $competencias = Competencia::whereIn('id', $competenciaIds)->get();

            $ramo = Ramo::where('id', $vaga->ramo_id)->get();

            if ($vaga->ativo == 1) {
                $ativo = true;
            } else {
                $ativo = false;
            }

             $vagaArray = array(
                'id' => $vaga->id,
                'ramo_id' => $vaga->ramo_id,
                'titulo' => $vaga->titulo,
                'descricao' => $vaga->descricao,
                'competencias' => $competencias,
                'experiencia' => $vaga->experiencia,
                'salario_min' => $vaga->salario_min,
                'salario_max' => $vaga->salario_max,
                'empresa_id' => $vaga->empresa_id,
                'ativo' => $ativo,
                'ramo' => [
                    'id' => $ramo[0]->id,
                    'nome' => $ramo[0]->nome,
                    'descricao' => $ramo[0]->descricao
                ]
            );

            return response()->json($vagaArray, 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
}
