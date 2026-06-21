<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Livewire\Catalog;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', Catalog::class)->name('catalog');
Route::get('/products/{product:slug}', \App\Livewire\ProductDetail::class)->name('products.show');

Route::get('/cart', \App\Livewire\CartPage::class)->name('cart');
Route::get('/checkout', \App\Livewire\CheckoutPage::class)->name('checkout');
Route::get('/checkout/success', \App\Livewire\CheckoutSuccessPage::class)->name('checkout.success');
Route::get('/order-track', \App\Livewire\OrderTrackPage::class)->name('order.track');
Route::post('/webhook/midtrans', [\App\Http\Controllers\WebhookController::class, 'handle']);

Route::get('/admin', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

Route::get('/admin/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.settings');

Route::post('/admin/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.settings.update');

Route::resource('/admin/categories', App\Http\Controllers\Admin\CategoryController::class)
    ->middleware(['auth', 'role:admin'])
    ->except(['show'])
    ->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

Route::post('/admin/products/bulk', [App\Http\Controllers\Admin\ProductController::class, 'bulk'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.products.bulk');

Route::resource('/admin/products', App\Http\Controllers\Admin\ProductController::class)
    ->middleware(['auth', 'role:admin'])
    ->except(['show'])
    ->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);

Route::resource('/admin/orders', App\Http\Controllers\Admin\OrderController::class)
    ->middleware(['auth', 'role:admin'])
    ->only(['index', 'show', 'update'])
    ->names([
        'index' => 'admin.orders.index',
        'show' => 'admin.orders.show',
        'update' => 'admin.orders.update',
    ]);

Route::resource('/admin/customers', App\Http\Controllers\Admin\CustomerController::class)
    ->middleware(['auth', 'role:admin'])
    ->names([
        'index' => 'admin.customers.index',
        'create' => 'admin.customers.create',
        'store' => 'admin.customers.store',
        'show' => 'admin.customers.show',
        'edit' => 'admin.customers.edit',
        'update' => 'admin.customers.update',
        'destroy' => 'admin.customers.destroy',
    ]);

Route::post('/admin/orders/{order}/tracking', [App\Http\Controllers\Admin\OrderController::class, 'updateTracking'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.orders.tracking');

Route::get('/admin/orders/{order}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.orders.invoice');

Route::post('/logout', function (App\Livewire\Actions\Logout $logout) {
    $logout();
    return redirect('/');
})->middleware('auth')->name('logout');

Route::get('dashboard', App\Livewire\Customer\ProfilePage::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('profile', App\Livewire\Customer\ProfilePage::class)->name('dashboard.profile');
    Route::get('wishlist', App\Livewire\Customer\WishlistPage::class)->name('dashboard.wishlist');
    Route::get('orders', App\Livewire\Customer\OrderHistoryPage::class)->name('dashboard.orders');
    Route::get('address-book', App\Livewire\Customer\AddressBookPage::class)->name('dashboard.address-book');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
