<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/apiDocs',[ApiController::class,'index'])->name('api.route');
