<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Items\Manager as ItemManager;
use App\Livewire\Admin\Items\StockMonitor;
use App\Livewire\Admin\Logs\Index as ActivityLogIndex;
use App\Livewire\Admin\Orders\Queue as OrderQueue;
use App\Livewire\Catalog\Index as CatalogIndex;
use App\Livewire\Orders\Tracker as OrderTracker;

Route::view('/', 'landing')->name('home');

Route::get('/register', Register::class)->name('register');
Route::get('/login', Login::class)->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout')->middleware('auth');

// Customer-only routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/my-requests', OrderTracker::class)->name('dashboard');
    // NOTE: route *name* kept as 'dashboard' since OrderTracker is referenced
    // by that name in the sidebar and elsewhere. URL path changed to
    // /my-requests to accurately reflect what the page actually is.
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/items', ItemManager::class)->name('admin.items');
    Route::get('/admin/items/stock', StockMonitor::class)->name('admin.items.stock');
    Route::get('/admin/orders', OrderQueue::class)->name('admin.orders');
    Route::get('/admin/logs', ActivityLogIndex::class)->name('admin.logs');
});

// Shared: any authenticated user (catalog is viewable by both roles per the FRD)
Route::middleware(['auth'])->group(function () {
    Route::get('/catalog', CatalogIndex::class)->name('catalog');
});