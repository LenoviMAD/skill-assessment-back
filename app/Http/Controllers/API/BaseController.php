<?php


namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($type, $message, $code, $result)
    {
        $response = [
            'type' => $type,
            'message' => $message,
            'code' => $code,
            'data'    => $result,

        ];

        return response()->json($response);
    }
}
