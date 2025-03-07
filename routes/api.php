<?php

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/teams', [TeamController::class, 'index']);
Route::post('/teams/initialize', [TeamController::class, 'initialize']);
Route::put('/teams/{id}', [TeamController::class, 'update']);

Route::get('/fixtures', [FixtureController::class, 'index']);
Route::get('/fixtures/week/{week}', [FixtureController::class, 'getByWeek']);
Route::post('/fixtures/generate', [FixtureController::class, 'generate']);
Route::put('/fixtures/{id}/result', [FixtureController::class, 'updateResult']);

Route::get('/simulation/state', [SimulationController::class, 'getState']);
Route::post('/simulation/match/{id}', [SimulationController::class, 'simulateMatch']);
Route::post('/simulation/next-week', [SimulationController::class, 'simulateNextWeek']);
Route::post('/simulation/all-weeks', [SimulationController::class, 'simulateAllWeeks']);
Route::post('/simulation/reset', [SimulationController::class, 'resetSimulation']);
Route::post('/simulation/predictions', [SimulationController::class, 'updatePredictions']);
