<?php

use App\Events\DrawingEvent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get( '/home', [ App\Http\Controllers\HomeController::class, 'index' ] )->name('home' );

Route::post( '/change-tool', function ( Illuminate\Http\Request $request ) {
    $validated = $request->validate([
        'tool' => 'required|string',
    ] );

    broadcast(new DrawingEvent( $validated[ 'tool' ] ) );

    return response()->json( [ 'status' => 'Tool changed successfully.' ] );
});

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