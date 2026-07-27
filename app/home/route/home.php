<?php
use think\facade\Route;
Route::miss('IndexController/NotFound');
Route::get('/','IndexController/Index');
Route::group('v1', function () {
    // 认证
    Route::post('auth/login', 'AuthController/login');
    Route::post('auth/register', 'AuthController/register');
    // 产品浏览
    Route::get('product', 'ProductController/GetProductList');
    Route::get('product/:id', 'ProductController/GetProductInfo')->pattern(['id' => '\d+']);
    // 公共接口
    Route::get('common/country', 'CommonController/country');

})->middleware([
    \app\http\middleware\Cors::class,
    \app\http\middleware\OptionalAuth::class,
]);
Route::group('v1', function () {
    Route::post('auth/logout', 'AuthController/logout');
    Route::put('auth/password', 'AuthController/changePassword');
    Route::get('account/profile', 'AccountController/profile');
    Route::put('account/profile', 'AccountController/UpdateProfile');
    Route::get('order', 'OrderController/GetOrderList');
    Route::get('order/:id', 'OrderController/GetOrderInfo')->pattern(['id' => '\d+']);
    Route::put('order/:id/cancel', 'OrderController/CancelOrder')->pattern(['id' => '\d+']);
    Route::get('host', 'HostController/GetHostList');
    Route::get('host/:id', 'HostController/GetHostInfo')->pattern(['id' => '\d+']);
    Route::put('host/:id/notes', 'HostController/UpdateHostNotes')->pattern(['id' => '\d+']);
    Route::get('cart', 'CartController/GetCartList');
    Route::post('cart', 'CartController/AddToCart');
    Route::put('cart/:id', 'CartController/UpdateCart')->pattern(['id' => '\d+']);
    Route::delete('cart/:id', 'CartController/DeleteCart')->pattern(['id' => '\d+']);
    Route::delete('cart/batch', 'CartController/batchDelete');
    Route::delete('cart/clear', 'CartController/clear');
    Route::post('cart/settle', 'CartController/settle');
    Route::get('pay/gateway', 'PayController/gatewayList');
    Route::post('pay', 'PayController/pay');
    Route::post('pay/credit', 'PayController/creditPay');
    Route::get('transaction', 'TransactionController/GetTransactionList');
    Route::get('dashboard', 'IndexController/dashboard');
})->middleware([
    \app\http\middleware\ClientAuth::class,
    \app\http\middleware\Cors::class,
    \app\http\middleware\Pagination::class,
    \app\http\middleware\ThrottleRepeat::class,
]);