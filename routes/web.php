<?php

use Illuminate\Support\Facades\Route;

Route::get('/',\App\Livewire\HomePage::class);
Route::get('/categories',\App\Livewire\CategoriesPage::class);
Route::get('/products',\App\Livewire\ProductsPage::class);
Route::get('/cart',\App\Livewire\CartPage::class);
Route::get('/products/{slug}',\App\Livewire\ProductDetailPage::class);
Route::get('/checkout',\App\Livewire\CheckoutPage::class);
Route::get('/my-orders',\App\Livewire\MyOrdersPage::class);
Route::get('/my-orders/{order}',\App\Livewire\MyOrderDetailPage::class);

Route::get('/login',\App\Livewire\Auth\LoginPage::class);
Route::get('/register',\App\Livewire\Auth\RegisterPage::class);
Route::get('/forgot',\App\Livewire\Auth\ForgotPasswordPage::class);
Route::get('/reset',\App\Livewire\Auth\ResetPasswordPage::class);

Route::get('/success',\App\Livewire\SuccessPage::class);
Route::get('/cancel',\App\Livewire\CancelPage::class);
