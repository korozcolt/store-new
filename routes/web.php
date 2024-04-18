<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class);
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/products/{product}', ProductDetailPage::class);

// ----- Orders -----
Route::get('/cart', CartPage::class);
Route::get('/checkout', CheckoutPage::class);
Route::get('/myorders', MyOrdersPage::class);
Route::get('/myorders/{order}', MyOrderDetailPage::class);
Route::get('/success', SuccessPage::class);
Route::get('/failed', CancelPage::class);

// ----- Auth -----
Route::get('/login', LoginPage::class)->name('login');
Route::get('/register', RegisterPage::class)->name('register');
Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
