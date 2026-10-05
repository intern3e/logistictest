<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\api;
use App\Http\Controllers\Api\CallbackController;

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
Route::get('/idbilldelivery', [api::class, 'apibilldeli']);

// ===== bot/OCR ภายนอกยิงผลการอ่านเอกสารบิลเข้ามา (ตรวจกับระบบ) =====
Route::post('/billdoccheck/match', [\App\Http\Controllers\BillDocCheckController::class, 'botMatch']);
Route::post('/callstatus/bulk', [CallbackController::class, 'callstatusBulk']);
Route::post('/callstatussuccess/bulk', [CallbackController::class, 'callstatussuccess']);
