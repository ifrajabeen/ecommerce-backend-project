<?php

use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/products', [ApiController::class,'index']);
Route::get('/products/{id}', [ApiController::class, 'show']);
Route::post('/store_products', [ApiController::class, 'store']);
Route::get('/destroy_products/{id}', [ApiController::class, 'destroy']);
Route::post('/update_products/{id}', [ApiController::class, 'update']);