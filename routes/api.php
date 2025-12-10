<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\GupshupController;
use App\Http\Controllers\DosController;
use App\Http\Controllers\DontsController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\SchedulerLogController;
use App\Http\Middleware\EnsureTaskManagerApiKey;



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

Route::prefix('{module}')->group(function () {

    Route::post('/login',[AuthApiController::class,'login']);
    Route::post('/logout',[AuthApiController::class,'logout'])->middleware('jwt.api.auth');
    Route::post('/refresh',[AuthApiController::class,'refresh'])->middleware('jwt.api.auth');
    Route::post('/edit-profile',[AuthApiController::class,'editProfile'])->middleware('jwt.api.auth');
    Route::post('/change-password',[AuthApiController::class,'changePassword'])->middleware('jwt.api.auth');
    Route::post('/delete-account',[AuthApiController::class,'deleteAccount'])->middleware('jwt.api.auth');
    Route::post('get-workspace-users',[AuthApiController::class,'getWorkspaceUsers'])->middleware('jwt.api.auth');

});

Route::post('/whatsapp/incoming', [GupshupController::class, 'incoming']);

// Task API routes
Route::prefix('tasks')->group(function () {
    Route::post('/create', [TaskApiController::class, 'createTask']);
    Route::get('/list', [TaskApiController::class, 'getTasks']);
});

// DO's and DON'T routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('dos')->group(function () {
        Route::post('/', [DosController::class, 'store']);
        Route::get('/', [DosController::class, 'index']);
        Route::delete('/{id}', [DosController::class, 'destroy']);
    });
    
    Route::prefix('donts')->group(function () {
        Route::post('/', [DontsController::class, 'store']);
        Route::get('/', [DontsController::class, 'index']);
        Route::delete('/{id}', [DontsController::class, 'destroy']);
    });
});

Route::middleware([EnsureTaskManagerApiKey::class])
    ->prefix('v1')
    ->group(function () {
        Route::post('/scheduler-status', [SchedulerLogController::class, 'store']);
        Route::get('/scheduler-status', [SchedulerLogController::class, 'index']);
            Route::post('/task-create', [TaskApiController::class, 'createTask']);
    Route::get('/task-list', [TaskApiController::class, 'getTasks']);
    });
    
Route::post('/save-channel-stats', [TaskApiController::class, 'save_inventory']);




