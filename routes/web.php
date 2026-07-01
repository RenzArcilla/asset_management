<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

Route::view('/', 'landing')->name('home');

Route::get('/register', Register::class)->name('register');

Route::get('/login', Login::class)->name('login');

// TODO: replace with real dashboard/catalog components once built
Route::view('/dashboard', 'landing')->name('dashboard');
Route::view('/admin/dashboard', 'landing')->name('admin.dashboard');
Route::view('/catalog', 'landing')->name('catalog');