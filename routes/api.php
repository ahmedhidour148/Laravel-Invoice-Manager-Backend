<?php
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ClientController;
use App\Http\Controllers\api\InvoiceController;
use App\Http\Controllers\TargetmanagerController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Type\Integer;


// authentication routes
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:limit5');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:limit5');
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// clientes route 
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('clients')->group(function () {
        Route::get('', [ClientController::class, 'index']);
        Route::post('', [ClientController::class, 'store']);
        Route::get('/{ClientId}', [ClientController::class, 'show']);
        Route::put('/{ClientId}', [ClientController::class, 'update']);
        Route::delete("/{ClientId}", [ClientController::class, 'destroy']);
    });
});

//invoices route
Route::middleware('auth:sanctum')->prefix('invoices')->group(function () {
    Route::get('', [InvoiceController::class, 'index']);
    Route::post('', [InvoiceController::class, 'store']);
    Route::get("/{InvoiceId}", [InvoiceController::class, 'show']);
    Route::put("/{InvoiceId}", [InvoiceController::class, 'update']);
    Route::delete("/{InvoiceId}", [InvoiceController::class, 'destroy']);
    Route::delete("/{InvoiceId}/{ItemId}", [InvoiceController::class, 'deleteitem']); //delete item for invoice from InvoiceItems
});

//sending otp code 
Route::post('verify-email', [AuthController::class, 'verfiyemail']);