<?php

use App\Http\Controllers\api\v1\CompanyController;
use App\Http\Controllers\api\v1\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Controllers\api\v1\ShopController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Users
Route::prefix('/user')->group(function(){
    Route::post('/login',[UserController::class,'login']);
    Route::post('/register',[UserController::class,'register']);

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/user',[UserController::class,'user']);   
    });
});
 
 Route::middleware(['auth:api'])->group(function () {
     //Shops
    Route::get('/shops',[ShopController::class,'index']);
    Route::post('/shops',[ShopController::class,'store']);   
    Route::get('/shops/{shop}',[ShopController::class,'show']);
    Route::put('/shops/{shop}',[ShopController::class,'update']);
    Route::delete('/shops/{shop}',[ShopController::class,'destroy']);

    //Customers
    Route::get('/customers/{shop?}',[CustomerController::class,'index']);
    Route::post('/customers',[CustomerController::class,'store']);
    Route::get('/customer/{customer}',[CustomerController::class,'show']);
    Route::put('/customers/{customer}',[CustomerController::class,'update']);
    Route::delete('/customers/{customer}',[CustomerController::class,'destroy']);

    //Companies
    Route::get('/companies/{shop?}',[CompanyController::class,'index']);
    Route::post('/companies',[CompanyController::class,'store']);
    Route::get('/company/{company}',[CompanyController::class,'show']);
    Route::put('/companies/{company}',[CompanyController::class,'update']);
    Route::delete('/companies/{company}',[CompanyController::class,'destroy']);
});


