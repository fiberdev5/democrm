<?php

use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\HipcallWebhookController;
use App\Http\Controllers\Api\ServiceApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ImpersonationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

    

    
    Route::post('/webhook/hipcall/{token}', [HipcallWebhookController::class, 'handle'])
        ->name('hipcall.webhook.handle');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::prefix('impersonation')->group(function () {
        Route::post('/start/{user_id}', [ImpersonationController::class, 'start']);
        Route::post('/stop', [ImpersonationController::class, 'stop']);
        Route::get('/users/{tenant_id}', [ImpersonationController::class, 'getUsersForImpersonation']);
        Route::get('/history', [ImpersonationController::class, 'getImpersonationHistory']);
        Route::get('/status', [ImpersonationController::class, 'checkStatus']);
    });
});

Route::middleware(['tenant.api'])->group(function () {
    Route::post('/customers', [CustomerApiController::class, 'store']);
    Route::get('/customers', [CustomerApiController::class, 'index']);
    Route::get('/customers/{id}', [CustomerApiController::class, 'show']);

    // Servis Endpoints
    Route::get('/services', [ServiceApiController::class, 'index']);
    Route::get('/services/{id}', [ServiceApiController::class, 'show']);
    Route::get('/services/{id}/status', [ServiceApiController::class, 'checkStatus']);
    Route::get('/services/{id}/is-completed', [ServiceApiController::class, 'isCompleted']);
    Route::put('/services/{id}/status', [ServiceApiController::class, 'updateStatus']);
});