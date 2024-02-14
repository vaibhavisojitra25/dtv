<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('verifyDeviceCode', [ApiController::class, 'verifyDeviceCode']);

Route::post('createCode', [ApiController::class, 'createCode']);
Route::post('renewCode', [ApiController::class, 'renewCode']);
Route::post('playlistByMacKey', [ApiController::class, 'playlistByMacKey']);
Route::post('playlistByDevice', [ApiController::class, 'playlistByDevice']);
Route::post('checkSubscription', [ApiController::class, 'checkSubscription']);
Route::post('checkSubscriptionWithDeviceID', [ApiController::class,'checkSubscriptionWithDeviceID']);
Route::post('getCurrentPlan', [ApiController::class, 'getCurrentPlan']);
Route::post('getCurrentPlanWithDeviceID', [ApiController::class, 'getCurrentPlanWithDeviceID']);
Route::post('activeDeactiveDevice', [ApiController::class, 'activeDeactiveDevice']);

Route::get('scheduleCron', [ApiController::class, 'scheduleCron']);
Route::get('scheduleSubscription', [ApiController::class, 'scheduleSubscription']);

