<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Http\Controllers\EventController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/apiDocs',[ApiController::class,'index'])->name('api.route');

Route::get('/get-data',function(){
    $users = User::all();
    foreach($users as $user){
        echo $user->name;
    }
});

Route::get('/calendar/create',[EventController::class,'create'])->name('frontend.event.create');
Route::post('/calendar/store',[EventController::class,'store'])->name('calendar.store');
