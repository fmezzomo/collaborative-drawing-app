<?php

use App\Events\DrawingEvent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/change-tool', function (Illuminate\Http\Request $request) {
    broadcast(new DrawingEvent($request->tool));
});