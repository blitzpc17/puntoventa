<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// routes/api.php
Route::get('/products', [ProductoController::class, 'searching']);
Route::get('/clients', [ClienteController::class, 'searching']);
Route::get('/provs', [ProveedorController::class, 'searching']);
Route::get('/cats', [CategoriaController::class, 'searching']);
