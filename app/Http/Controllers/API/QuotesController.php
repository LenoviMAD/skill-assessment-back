<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Validator;
use App\Models\User;
use App\Models\Quotes;

class QuotesController extends BaseController
{
    // //Quotes
    // public function randomQuote(Request $request)
    // {
    //     $resp = Http::get("https://api.kanye.rest/");

    //     return $this->sendResponse('positive', 'Bienvenido', 200, $resp->json());
    // }

    //Solicitud a las 5 quotes de la pagina
    public function principalQuote(Request $request)
    {
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->get('https://api.kanye.rest/'),
            $pool->get('https://api.kanye.rest/'),
            $pool->get('https://api.kanye.rest/'),
            $pool->get('https://api.kanye.rest/'),
            $pool->get('https://api.kanye.rest/'),
        ]);

        $var = [];
        $flag = 0;

        foreach ($responses as &$key) {
            $var = Arr::add($var, $flag, $key->json());
            $flag++;
        };
        return $this->sendResponse('positive', 'Bienvenido', 200, $var);
    }

    //Quotes especificas
    public function specifiedQuote(int $qnt)
    {

        $var = [];
        for ($i = 0; $i < $qnt; $i++) {
            $responses = Http::pool(fn (Pool $pool) => [
                $pool->get('https://api.kanye.rest/'),
            ]);
            $var = Arr::add($var, $i, $responses[0]->json());
        }

        return $this->sendResponse('positive', 'Bienvenido', 200, $var);
    }

    //Guardar favoritos
    public function saveFavorite(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'idUser' => 'required',
                'quote' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendResponse('warning', 'Error de validación', 200, $validator->errors());
            }

            $input = $request->all();
            $dataQuote['idUser'] = $input['idUser'];
            $dataQuote['description'] = $input['quote'];
            $resQuote = Quotes::create($dataQuote);


            DB::commit();
            return $this->sendResponse('positive', 'Guardado favoritos', 200, $resQuote);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse('negative', 'Error', 401, $th->getMessage());
        }
    }

    //Delete favoritos
    public function deleteFavorite(int $idQuote)
    {
        DB::beginTransaction();
        try {

            $deleted = Quotes::where('id', $idQuote)->delete();
            DB::commit();
            return $this->sendResponse('positive', 'Eliminado correctamente', 200, $deleted);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse('negative', 'Error borrando la cita', 401, $th->getMessage());
        }
    }

    //Delete favoritos
    public function getFavorite(int $idUser)
    {
        try {
            $dataFavorite = Quotes::where('idUser', $idUser)
                ->get();
            return $this->sendResponse('positive', 'Datos de favoritos', 100, $dataFavorite);
        } catch (\Throwable $th) {
            return $this->sendResponse('negative', 'Error', 401, $th->getMessage());
        }
    }
}
