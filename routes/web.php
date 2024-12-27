<?php

use App\Livewire\HomePage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class);
Route::get('/categories',\App\Livewire\CategoriesPage::class);
Route::get('/products',\App\Livewire\ProductsPage::class);
Route::get('/cart',\App\Livewire\CartPage::class);
Route::get('/products/{product}',\App\Livewire\ProductDetailPage::class);
