<?php

use App\Http\Controllers\payments\mpesa\MpesaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/daraja/token', [MpesaController::class, 'getAccessToken']);
Route::post('/v1/daraja/stk', [MpesaController::class, 'stkPush']);
Route::post('/v1/daraja/validation', [MpesaController::class, 'mpesaValidation']);
Route::post('/v1/daraja/transaction/confirmation', [MpesaController::class, 'mpesaConfirmation']);