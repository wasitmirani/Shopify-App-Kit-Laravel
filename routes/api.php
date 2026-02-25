<?php

declare(strict_types=1);

use App\Http\Controllers\API\BlockController;
use App\Http\Controllers\API\CollectionController;
use App\Http\Controllers\API\FAQController;
use App\Http\Controllers\API\IconController;
use App\Http\Controllers\API\IntegrationController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ThemeController;
use App\Http\Controllers\API\TutorialController;
use App\Http\Controllers\API\UserController;
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

Route::middleware('auth:sanctum')->get('/user', static fn (Request $request) => $request->user());

Route::group(['middleware' => ['verify.shopify']], static function () {
    /* Plan Pricing Routes */
    Route::group(['prefix' => 'plans', 'as' => 'plans.', 'controller' => PlanController::class], static function () {
        Route::get('/', 'index')->name('index');
        Route::post('/choose-plan/free', 'chooseFreePlan')->name('freePlan');
        Route::get('/get-shop', 'getShop')->name('shop');
        Route::get('/get-active-plan', 'getActivePlan')->name('active');
    });

    /* User and plan Routes */
    Route::get('/user-plan', [UserController::class, 'getUserPlan'])->name('user.plan');
    Route::get('/user-page-views-count', [UserController::class, 'getUserPageViewsCount']);

    /* Block Routes */
    Route::group(['prefix' => 'blocks', 'controller' => BlockController::class], static function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'getSingleIconBlock');
        Route::delete('/{id}', 'delete');
        Route::post('/', 'store');
        Route::get('/update-status/{id}/{status}', 'updateStatus');
        Route::post('/duplicate/{id}', 'duplicate');
    });

    /* Icon Routes */
    Route::group(['prefix' => 'icons', 'controller' => IconController::class], static function () {
        Route::get('/default', 'getDefaultIcons');
        Route::get('/regular', 'getRegularIconsByCategory');
        Route::get('/custom', 'getCustomIcons');
        Route::get('/search', 'getSearchIcons');
        Route::get('/{id}', 'getSingleIcon');

        /* Upload Custom Icon */
        Route::post('/upload', 'uploadIcon');
    });

    /* Product Routes */
    Route::group(['prefix' => 'products', 'controller' => ProductController::class], static function () {
        Route::get('/', 'index')->name('products.index');
    });

    /* Collection Routes */
    Route::group(['prefix' => 'collections', 'controller' => CollectionController::class], static function () {
        Route::get('/', 'index')->name('collections.index');
    });

    /* Review Routes */
    Route::post('/submit-review', [ReviewController::class, 'store']);

    /* Tutorial Routes */
    Route::group(['prefix' => 'tutorials', 'controller' => TutorialController::class], static function () {
        Route::get('/', 'index');
    });

    /* FAQ Routes */
    Route::group(['prefix' => 'faqs', 'controller' => FAQController::class], static function () {
        Route::get('/', 'index');
    });

    /* FAQ Routes */
    Route::group(['prefix' => 'integrations', 'controller' => IntegrationController::class], static function () {
        Route::get('/', 'index');
    });

    /* Theme Routes */
    Route::post('activate-app-extension', [ThemeController::class, 'activateExtension']);

    Route::post('segment-events', [UserController::class, 'sendSegmentEvent']);
});

/* Storefront Routes */
Route::group(['prefix' => 'storefront/{shop}', 'middleware' => ['shop.auth','page.views']], static function () {
    Route::get('index/blocks', [BlockController::class, 'indexBlocks']);
    Route::get('product/blocks', [BlockController::class, 'productBlocks']);
    Route::get('cart/blocks', [BlockController::class, 'cartBlocks']);
    Route::get('site-common/blocks', [BlockController::class, 'siteCommonBlocks']);
});
