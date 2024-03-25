<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserAlertController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\BusinessDetailsController;
use App\Http\Controllers\AuthSocialiteController;
use App\Http\Controllers\CustomPasswordResetController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\FeedbackController;


use Laravel\Socialite\Facades\Socialite;

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


//TODO: use route group /v1
Route::post('/v1/register', [UserAuthController::class, 'register']);
Route::post('/v1/login', [UserAuthController::class, 'login']);
Route::get('/v1/user/{userId}', [UserAuthController::class, 'getUserById']);


route::get('/v1/auth/facebook', [FacebookController::class, 'facebookpage']);
route::get('/v1/auth/facebook/callback', [FacebookController::class, 'facebookredirect']);

Route::post('/v1/forgot-password', [CustomPasswordResetController::class, 'forgotPassword']);
Route::post('/v1/reset-password', [CustomPasswordResetController::class, 'resetPassword']);

//Route::post('/v1/payment', 'App\Http\Controllers\StripeController@payment');
Route::post('/v1/payment', [StripeController::class, 'payment']);
//Route::post('/v1/orders', [Controller::class, 'payment']);
// routes/api.php

Route::middleware('auth:api')->group(function () {
	Route::get('/v1/user/orders', [OrderController::class, 'getUserOrders']);
	Route::get('/v1/user/orders/{orderId}', [OrderController::class, 'getOrderDetails']);
});


Route::middleware('auth:api')->group(function () {
    Route::post('/v1/update-profile', 'App\Http\Controllers\UserAuthController@updateProfile');
    Route::post('/v1/update-password', 'App\Http\Controllers\UserAuthController@updatePassword');
    Route::post('/v1/update-profile-picture', 'App\Http\Controllers\UserAuthController@updateProfilePicture');
    Route::get('/v1/cart', [CartController::class, 'index']);
    Route::post('/v1/merge-cart', 'App\Http\Controllers\CartController@mergeCart');
});

//Route::get('/v1/products/filter', [FilterController::class, 'test']);

//Test
Route::get('/v1/products/filter', [ProductController::class, 'filter']);

/**
 * Query Parameters:
 * - per_page: Number of items per page.
 * - e.g: http://localhost:8000/api/v1/products/top-by-sold?per_page=10
 */
Route::get('/v1/products/top-by-quantity', [ProductController::class, 'getTopProductsByQuantity']);
Route::get('/v1/products/top-by-sold', [ProductController::class, 'getTopProductsBySold']);
Route::get('/v1/products/top-by-ratings', [ProductController::class, 'getTopProductsByRatings']);
Route::get('/v1/products/top-by-views', [ProductController::class, 'getTopProductsByViews']);
Route::get('/v1/products/top-by-date', [ProductController::class, 'getTopProductsByDate']);
/** */
Route::get('/v1/products/top-by-type', [ProductController::class, 'getTopProductsByType']);
Route::get('/v1/products/top-by-column', [ProductController::class, 'getTopProductsByColumn']);


Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::get('/products/upload/total', [ProductController::class, 'getTotalUploadedProducts']);
    Route::get('/products/request/total', [ProductController::class, 'getTotalRequests']);
    Route::get('/orders/total', [OrderController::class, 'getTotalOrders']);
    Route::delete('/v1/products/{productId}', [ProductController::class, 'destroy']);
});


Route::get('v1/products', [ProductController::class, 'index']);
Route::get('/v1/products/{id}', [ProductController::class, 'show']);
//Route::put('/v1/products/{id}', [ProductController::class, 'update']);

Route::middleware('auth:api')->group(function () {
    Route::post('/v1/products/{id}', [ProductController::class, 'update']);
    Route::post('/v1/products', [ProductController::class, 'store']);
});

/*
Route::apiResource('v1/products', ProductController::class);
Route::middleware('auth:api')->apiResource('v1/products', ProductController::class);
Route::middleware('auth:api')->group(function () {
    Route::apiResource('v1/products', ProductController::class)->except(['index']);
});
 */


Route::get('/v1/user/{userId}/products', [ProductController::class, 'getUserProducts']);

Route::get('/v1/categories', [ProductController::class, 'getAllCategories']);

//[ Product request
Route::apiResource('/v1/product-requests', ProductRequestController::class);
// ]

Route::post('v1/add-to-cart', [CartController::class, 'addToCart']);
Route::post('v1/remove-from-cart', [CartController::class, 'removeFromCart']);
Route::post('v1/update-cart', [CartController::class, 'updateCart']);
Route::post('v1/clear-cart', [CartController::class, 'clearCart']);

//Route::middleware('auth')->post('/v1/merge-cart', [CartController::class, 'mergeCart']);

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});



Route::middleware('auth:api')->group(function () {
    Route::get('/v1/user-alerts/unread', [UserAlertController::class, 'getUnreadAlerts']);
    Route::put('/v1/user-alerts/mark-as-read', [UserAlertController::class, 'markAlertsAsRead']);
    Route::get('/v1/user-alerts/unread-count', [UserAlertController::class, 'getUnreadAlertsCount']);
});

//Route::group(['prefix' => 'auth'], function () {});
Route::get('/v1/auth/google', [AuthSocialiteController::class, 'redirectToGoogle']);
Route::get('/v1/auth/google/callback', [AuthSocialiteController::class, 'handleGoogleCallback']);
//Route::match(['get', 'post'], '/v1/auth/google/callback', [AuthSocialiteController::class, 'handleGoogleCallback']);


Route::post('/v1/business-details', [BusinessDetailsController::class, 'store']);
Route::get('/v1/business-details/{id}', [BusinessDetailsController::class, 'show']);

Route::middleware(['auth:api'])->group(function () {
	Route::get('/v1/business-details', [BusinessDetailsController::class, 'getBusinessDetails']);
	Route::post('/v1/business-details/update', [BusinessDetailsController::class, 'update']);
});

Route::delete('/v1/business-details/{id}', [BusinessDetailsController::class, 'destroy']);

Route::get('/v1/feedback/{product_id}', [FeedbackController::class, 'index']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('/v1/feedback', [FeedbackController::class, 'store']);
});

