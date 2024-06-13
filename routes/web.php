<?php

use App\Helpers\SiteSetting;
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
use Resend\Laravel\Facades\Resend;

// ----- Test Page ------
Route::get('/test', function () {
    Resend::emails()->send([
        'from' => 'Acme <onboarding@resend.dev>',
        'to' => 'ing.korozco@gmail.com',
        'subject' => 'Welcome to Acme!',
        'html' => '<h1>Welcome to Acme!</h1>',
    ]);
});
// ----- Pages ------
Route::get('/', HomePage::class);
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/products/{slug}', ProductDetailPage::class);

// ----- Cart -----
Route::get('/cart', CartPage::class);

// ----- Guest -----
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
    Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
});

// ----- Auth -----
Route::middleware('auth')->group(function () {
    Route::get('/checkout', CheckoutPage::class);
    Route::get('/my-orders', MyOrdersPage::class)->name('my-orders');
    Route::get('/my-orders/{order_id}', MyOrderDetailPage::class)->name('my-orders.show');
    Route::get('/order/success', SuccessPage::class)->name('success');
    Route::get('/order/cancel', CancelPage::class)->name('cancel');

    Route::get('/logout', function () {
        auth()->logout();
        return redirect('/');
    });
});
