<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function (){
    Route::post('register','register');
    Route::post('login','login')->middleware('throttle:5,2');
    Route::post('logout','logout')->middleware('auth:sanctum');

});

Route::middleware('auth:sanctum')->prefix('teams')->group(function () {

Route::apiResource('/', TeamController::class);
Route::post('{id}/add-member', [TeamController::class, 'addMember']);
Route::put('{id}/members/{memberId}', [TeamController::class, 'updateMemberRole']);
Route::delete('{id}/members/{memberId}', [TeamController::class, 'removeMember']);

});