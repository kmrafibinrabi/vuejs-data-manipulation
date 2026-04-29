<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FutureIdeaController;
use App\Http\Controllers\ImageResearchController;

// Auth routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Future Ideas API (no authentication required)
Route::post('/future-ideas/generate', [FutureIdeaController::class, 'generate']);

// Public Image Research Module (no authentication required)
Route::post('/image-research', [ImageResearchController::class, 'analyze']);

// Protected routes (require login)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Notes API routes
    Route::apiResource('notes', NoteController::class);
    Route::post('/notes/search', [NoteController::class, 'search']);
});
