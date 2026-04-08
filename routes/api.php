<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    return response()->json([
        'status' => true,
        'email' => $request->email
    ]);
});