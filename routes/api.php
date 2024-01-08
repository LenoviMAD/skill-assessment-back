<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\QuotesController;
use App\Http\Controllers\API\UtilController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', [AuthController::class, 'login']); //Listo
Route::post('register', [AuthController::class, 'register']); //Listo

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthController::class, 'logout']); //Listo
});
Route::get('getUsers', [AuthController::class, 'getUsers']); //Listo
Route::put('banUser', [AuthController::class, 'banUser']); //Listo
Route::put('unbanUser', [AuthController::class, 'unbanUser']); //Listo



Route::get('principalQuote', [QuotesController::class, 'principalQuote']); //Listo
Route::get('specifiedQuote/{qnt}', [QuotesController::class, 'specifiedQuote']); // Listo
Route::get('getFavorite/{idUser}', [QuotesController::class, 'getFavorite']); // Listo
Route::post('saveFavorite', [QuotesController::class, 'saveFavorite']); //Listo
Route::delete('deleteFavorite/{idQuote}', [QuotesController::class, 'deleteFavorite']);


Route::get('getRoles', [UtilController::class, 'getRoles']); //Listo