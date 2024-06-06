<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends UserController
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'senha' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['mensagem' => 'E-mail ou senha inválidos'], 422);
        }

        try {
            $credentials = $request->only('email', 'senha');

            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['senha'], $user->senha)) {
                return response()->json(['mensagem' => 'E-mail ou senha incorretos'], 401);
            }

            $token = JWTAuth::fromUser($user);

            $user->token = $token;
            $user->save();


            return response()->json(['token' => $token], 200);

        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
    public function logout(Request $request)
{
    try {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['mensagem' => 'Nenhum token enviado'], 401);
        }

        $user = User::where('token', $token)->first();
        if ($user) {
            $user->token = null;
            $user->save();
            return response()->json(['mensagem' => 'Sucesso'], 200);
        }else{
            return response()->json(['mensagem' => 'Token não encontrado'], 401);
        }

    } catch (\Exception $e) {
        return response()->json(['mensagem' => 'erro interno: ' . $e->getMessage()], 401);
    }
}

}