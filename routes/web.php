<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Volt::route('goals', 'pages.goals.index')->middleware(['auth'])->name('goals.index');
Volt::route('goals/{goal}', 'pages.goals.show')->middleware(['auth'])->name('goals.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
