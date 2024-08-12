<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/post',[ApiController::class,'store']);
Route::get('/post',[ApiController::class,'allPost']);
Route::get('/post/{id}',[ApiController::class,'show']);
Route::put("/post/{id}",[ApiController::class,"update"]);
