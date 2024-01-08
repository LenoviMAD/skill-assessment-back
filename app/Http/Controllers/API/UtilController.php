<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Roles;

class UtilController extends BaseController
{
    //Devuelve los roles
    public function getRoles()
    {
        try {

            $roles = Roles::all();

            return $this->sendResponse('positive', 'Datos conseguidos', 100, $roles);
        } catch (\Throwable $th) {
            return $this->sendResponse('negative', 'Error', 401, $th->getMessage());
        }
    }
}
