<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Models\Ramo;


class RamoController extends UserController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        //
    }
    public function listarRamos(Request $request)
    {
        try {
            $ramos = Ramo::all();

            return response()->json($ramos, 200);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
}
