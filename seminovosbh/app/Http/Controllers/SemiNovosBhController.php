<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ListRepository;
use App\Repositories\MarcaRepository;
use App\Repositories\ModeloRepository;
use Validator;

class SemiNovosBhController extends Controller
{
    public function getCarsList (Request $request) {
        $ListRepo   = new ListRepository;
        $carsList   = null;
        $marca      = $request->get("marca") ? $request->get("marca") : "";
        $modelo     = $request->get("modelo") ? $request->get("modelo") : "";

        $validator = Validator::make($request->all(), [
            'marca' => 'required',
            'modelo' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json('[]');
        }

        $url = $ListRepo->buildUrl([
            'marca'     => $marca,
            'modelo'    => $modelo
        ]);

        if ($url) {
            $carsList = $ListRepo->carsList($url);
        } else {
            return response()->json([
                'msg' => "Url Couldn't be builded"
            ], 500);
        }

        return response()->json($carsList);
    }

    public function getMarcasList () {
        $marcasList = (new MarcaRepository)->marcasList();
        return response()->json($marcasList);
    }

    public function getModelosList ($value) {
        $modelosList = (new ModeloRepository)->modelosList($value);
        return response()->json($modelosList);
    }
}
