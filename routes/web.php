<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Login;

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
    Route::view('/dashboard', 'landing')->name('dashboard');
    // TODO: replace with real customer dashboard component
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/admin/dashboard', 'landing')->name('admin.dashboard');
    // TODO: replace with real admin dashboard component
});

// Shared: any authenticated user (catalog is viewable by both roles per the FRD)
Route::middleware(['auth'])->group(function () {
    Route::view('/catalog', 'landing')->name('catalog');
});