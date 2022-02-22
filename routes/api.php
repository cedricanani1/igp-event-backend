<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('categories', CategorieController::class);
// Route::get('categories/{id}', [CategorieController::class,'show']);
Route::resource('products', ProductController::class);
Route::resource('orders', OrderController::class);
Route::get('/orders-client', [OrderController::class,'ordersclient']);
Route::post('/deleteFile', [ProductController::class,'deleteFile']);
Route::post('/addFile', [ProductController::class,'addFile']);
Route::resource('orders-cart', CartController::class);

Route::post('/rating', [RatingController::class,'store']);

Route::get('/bestrate', [ProductController::class,'bestRate']);
Route::post('/bestproduct', [ProductController::class,'best']);
Route::get('/getsellAllTime', [ProductController::class,'sellerAlltime']);
Route::get('/bestviews', [ProductController::class,'bestView']);
Route::post('/getsellByPeriode', [OrderController::class,'sellerPeriode']);
Route::get('/getuserSeller', [OrderController::class,'UserBestSeller']);
