<?php

use App\Http\Controllers\API\APImainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::post('scanUrl',[App\Http\Controllers\API\APImainController::class,'getUrlData']); //Scan Url
    Route::post('initscan',[App\Http\Controllers\API\APImainController::class,'iniScannerSteps']); //Scan Url
    Route::post('startscan',[App\Http\Controllers\API\APImainController::class,'startScannerSteps']); //Scan Url
    // Route::post('urlmeta',[App\Http\Controllers\API\APImainController::class,'getlinkMetadata']); //Scan Url
    // Route::post('isredirect',[App\Http\Controllers\API\APImainController::class,'isredirect']); //Scan Url


    // Route::post('testres',[App\Http\Controllers\API\APImainController::class,'testappapi']); //Scan Url
    // Route::post('testres2',[App\Http\Controllers\API\APImainController::class,'testappapisteps']); //Scan Url
    // Route::get('testnotif',[App\Http\Controllers\API\APImainController::class,'testnotifapp']); //Scan Url
});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
