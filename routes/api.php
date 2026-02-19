<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LastMile\HubController;
use App\Http\Controllers\LastMile\ClientController;
use App\Http\Controllers\LastMile\ReportController;
use App\Http\Controllers\LastMile\DashboardController;

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::middleware('auth:api')->get('/users', [UserController::class, 'index']);
Route::middleware('auth:api')->post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/google-login', [AuthController::class, 'googleLogin']);
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});
Route::middleware('auth:api')->group(function() {
    Route::resource('hubs', HubController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('reports', ReportController::class);
    Route::get('/reports/hub/{hubId}', [ReportController::class, 'getByHub']);
    Route::get('/dashboard/dashboard-stats', [DashboardController::class, 'getDashboardStats']);
    Route::get('/dashboard/daily-trends', [DashboardController::class, 'getDailyTrends']);
    Route::get('/dashboard/hub-performance', [DashboardController::class, 'getHubPerformance']);
    Route::get('/dashboard/key-metrics', [DashboardController::class, 'getKeyMetrics']);
});
