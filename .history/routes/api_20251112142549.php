<?php

use App\Http\Controllers\Api\HipcallWebhookController;
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

Route::post('/hipcall/webhook', [HipcallWebhookController::class, 'handle'])
    ->name('hipcall.webhook.handle');