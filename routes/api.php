<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProjectsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function (){
    Route::post('register','register');
    Route::post('login','login')->middleware('throttle:5,2');
    Route::post('logout','logout')->middleware('auth:sanctum');

});

Route::middleware('auth:sanctum')->prefix('teams')->group(function () {

    // CRUD teams
    Route::apiResource('', TeamController::class);

    // team members
    Route::post('{team}/add-member', [TeamController::class, 'addMember']);
    Route::put('{team}/members/{member}', [TeamController::class, 'updateMemberRole']);
    Route::delete('{team}/members/{member}', [TeamController::class, 'removeMember']);
});


Route::middleware('auth:sanctum')->prefix('teams/{team}')->group(function () {

    Route::apiResource('projects', ProjectsController::class);
});
