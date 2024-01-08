<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Models\User;

class AuthController extends BaseController
{
    //Logear
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = User::where('email', $request->email)->first();
            $success['token'] =  $authUser->createToken('API TOKEN')->plainTextToken;
            $success['idRole'] =  $authUser->idRole;
            $success['idUser'] =  $authUser->id;
            $success['name'] =  $authUser->name;
            $success['ban'] =  $authUser->ban;

            return $this->sendResponse('positive', 'Bienvenido', 200, $success);
        } else {
            return $this->sendResponse('negative', 'El email o contraseña es incorrecto', 401, []);
        }
    }

    //Registrar
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'idRole' => 'required',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string',
                'cpassword' => 'required|same:password'
            ]);

            if ($validator->fails()) {
                return $this->sendResponse('warning', 'Error de validación', 500, $validator->errors());
            }

            //Asignamos a una variable el valor del json
            $input = $request->all();

            $user = User::create([
                'name' => $input['name'],
                'idRole' => $input['idRole'],
                'email' => $input['email'],
                'password' => bcrypt($input['password']),
                'ban' => false
            ]);

            $token = $user->createToken('apiToken')->plainTextToken;

            $success = [
                'user' => $user,
                'token' => $token
            ];

            DB::commit();
            return $this->sendResponse('positive', 'Usuario creado satisfactoriamente.', 200, $success);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse('negative', 'Error', 401, $th->getMessage());
        }
    }

    //Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse('positive', 'Deslogeo', 200, ['']);
    }

    //Obtener usuarios
    public function getUsers()
    {
        try {
            $dataUser = User::where('idRole', 2)->get();

            return $this->sendResponse('positive', 'Usuarios Clientes', 100, $dataUser);
        } catch (\Throwable $th) {
            return $this->sendResponse('negative', 'Error', 401, $th->getMessage());
        }
    }

    //Ban User
    public function banUser(Request $request)
    {
        try {
            //Validamos los campos requeridos
            $validator = Validator::make($request->all(), [
                'idUser' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendResponse('warning', 'Error de validación', 200, $validator->errors());
            }
            $inputs = $request->all();
            $dataUser = User::where('id', $inputs['idUser'])
                ->update(['ban' => true]);

            return $this->sendResponse('positive', 'Usuario baneado correctamente', 100, $dataUser);
        } catch (\Throwable $th) {
            return $this->sendResponse('negative', 'Error', 401, $th->getMessage());
        }
    }
    public function unbanUser(Request $request)
    {
        try {
            //Validamos los campos requeridos
            $validator = Validator::make($request->all(), [
                'idUser' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendResponse('warning', 'Error de validación', 200, $validator->errors());
            }
            $inputs = $request->all();
            $dataUser = User::where('id', $inputs['idUser'])
                ->update(['ban' => false]);

            return $this->sendResponse('positive', 'Usuario desbaneado correctamente', 100, $dataUser);
        } catch (\Throwable $th) {
            return $this->sendResponse('negative', 'Error', 401, $th->getMessage());
        }
    }
}
