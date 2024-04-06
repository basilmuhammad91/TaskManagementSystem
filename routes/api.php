<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\GeneralController;

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

Route::post('/login', [UserAuthController::class, 'login']);

Route::group(['middleware'=>['auth:sanctum']], function () {
    Route::apiResources(['users' => UserController::class]);
    Route::apiResources(['roles' => RoleController::class]);
    Route::apiResources(['permissions' => PermissionController::class]);
    Route::apiResources(['tasks' => TaskController::class]);
    Route::apiResources(['settings'=> SettingController::class]);

    Route::get('/all-users', [GeneralController::class, 'getAllUsers']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});