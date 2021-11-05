<?php

namespace App\Http\Controllers;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forget-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.sent');
    Route::post('reset-password', [AuthController::class, 'sendResetResponse'])->name('password.reset');

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
    });
});

Route::group(['prefix' => 'user'], function () {
    Route::get('roles', [UserController::class, 'roles']);
    Route::get('plans', [UserController::class, 'plans']);
    Route::get('planBenefits', [UserController::class, 'planBenefits']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::post('updateMe', [UserController::class, 'addOrEditMe']);
        Route::put('password', [UserController::class, 'editUserPassword']);
        Route::post('fetchUser', [UserController::class, 'fetchUser']);
        Route::get('users', [UserController::class, 'users']);
        Route::post('activities', [UserController::class, 'activities']);

        Route::group(['prefix' => 'v1'], function () {
            Route::get('setup-intent', [UserController::class, 'getSetupIntent']);
            Route::post('payments', [UserController::class, 'postPaymentMethods']);
            Route::get('payment-methods', [UserController::class, 'getPaymentMethods']);
            Route::post('remove-payment', [UserController::class, 'removePaymentMethod']);
            Route::put('subscription', [UserController::class, 'updateSubscription']);
        });
    });
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => ['auth:sanctum', 'role']], function() {
        Route::get('activities', [AdminController::class, 'getAllActivities']);

        Route::post('fetchUsers', [AdminController::class, 'fetchUsers']);
        Route::post('fetchActivities', [AdminController::class, 'fetchActivities']);
        Route::post('saveUser', [AdminController::class, 'addOrEditUser']);
        Route::post('deleteUser', [AdminController::class, 'deleteUser']);
        Route::post('savePlanBenefit', [AdminController::class, 'addOrEditPlanBenefit']);
        Route::post('deletePlanBenefit', [AdminController::class, 'deletePlanBenefit']);
    });
});
