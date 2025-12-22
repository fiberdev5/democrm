<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\HipcallWebhookController;
use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\ServiceController;
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

// MOBİL UYGULAMA ENDPOİNTLERİ
// Public routes

Route::post('/login', [AuthController::class, 'login']);


// Protected routes
Route::middleware(['auth:sanctum', 'check.token.expiration'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);    
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    
    // Servis endpoints
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'myAssignedServices']); // Servis listesi
        Route::get('/{id}', [ServiceController::class, 'myAssignedServiceDetail']); // Servis detay
    });
    // Personele atanan stoklar
    Route::get('/my-stocks', [ServiceController::class, 'myStocks']);
});