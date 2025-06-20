<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/subscriptions', [SubscriptionController::class, 'adminIndex']);
    Route::get('/admin/subscriptions/create', [SubscriptionController::class, 'create']);
    Route::get('/admin/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit']);
});