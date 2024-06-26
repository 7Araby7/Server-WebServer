<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Models\Mensagem;
use App\Models\User;


class MensagemController extends UserController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        //
    }
    public function enviarMensagem(Request $request)
{
    try {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['mensagem' => 'Nenhum token enviado'], 401);
        }

        $empresa = User::where('token', $token)->first();

        if (!$empresa) {
            return response()->json(['mensagem' => 'Usuário não encontrado'], 401);
        }

        foreach ($request->input('candidatos') as $email) {
            $candidato1 = User::where('email', $email)->first();

            if (!$candidato1) {
                return response()->json(['mensagem' => 'Candidato não encontrado com o email: ' . $email], 404);
            }

            $mensagem = new Mensagem();
            $mensagem->empresa_id = $empresa->id;
            $mensagem->candidato_id = $candidato1->id;
            $mensagem->mensagem = "A empresa " . $empresa->nome . " está interessada no seu currículo. Entre em contato através do e-mail: " . $empresa->email;
            $mensagem->nova = false;
            $mensagem->save();
        }

        return response()->json(['mensagem' => 'Mensagens enviadas com sucesso'], 200);
    } catch (\Exception $e) {
        return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
    }
}


    public function lerMensagem(Request $request)
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

            $mensagens = Mensagem::where('candidato_id', $user->id)->get();

            $mensagensArray = array();
            foreach ($mensagens as $mensagem) {

                $empresa = User::where('id', $mensagem['empresa_id'])->first();
                
                $mensagemArray = array(
                    'empresa' => $empresa->nome,
                    'mensagem' => $mensagem->mensagem,
                    'lida' => $mensagem->nova
                );
                array_push($mensagensArray, $mensagemArray);
                
                $mensagem->nova = true;
                $mensagem->save();
            }

            return response()->json($mensagensArray, 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
}
