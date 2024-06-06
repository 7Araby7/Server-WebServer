<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Models\Competencia;


class CompController extends UserController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        //
    }
    public function listarCompetencias(Request $request)
    {
        try {
            $competencias = Competencia::all();

            return response()->json($competencias, 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
}
