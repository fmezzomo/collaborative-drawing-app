<?php

use App\Events\DrawingEvent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanvasController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get( '/home', [ App\Http\Controllers\HomeController::class, 'index' ] )->name('home' );

Route::post( '/update-drawing', function ( Illuminate\Http\Request $request ) {
    $validated = $request->validate([
        'tool'       => 'required|string',
        'updateData' => 'nullable|array',
        'origin'     => 'required|string',
    ]);

    broadcast( new DrawingEvent(
        $validated['tool'],
        $validated['updateData'] ?? [],
        $validated['origin']
    ) );

    return response()->json(['status' => 'Drawing updated successfully.']);
});

Route::post('/join-panel', [ CanvasController::class, 'joinCanvas' ] );