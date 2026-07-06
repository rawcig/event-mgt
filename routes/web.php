<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Public - No Login Required)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

/*
|--------------------------------------------------------------------------
| Public Routes (Event Browsing & Registration) - MUST be before resource routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/browse-events', [EventController::class, 'publicIndex'])->name('events.public');
Route::get('/event/{event}', [EventController::class, 'publicShow'])->name('events.show.public');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All Logged-in Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/app-profile', [ProfileController::class, 'show'])->name('app-profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // User's Registered Events (Regular users only)
    Route::get('/my-events', [GuestController::class, 'myEvents'])->name('my-events');

    // Event Registration (Regular users only)
    Route::get('/event/{event}/register', [GuestController::class, 'publicRegister'])->name('events.register');
    Route::post('/event/{event}/register', [GuestController::class, 'publicRegisterStore'])->name('events.register.store');

    // Organizers (Admin/Organizer only)
    Route::middleware('role:admin,organizer')->group(function () {
        Route::resource('organizer', OrganizerController::class);
    });

    // Events (Admin/Organizer only)
    Route::middleware('role:admin,organizer')->group(function () {
        Route::resource('events', EventController::class);
        Route::get('/create-event', [EventController::class, 'create'])->name('create-event');
        Route::post('/events/bulk-delete', [EventController::class, 'bulkDelete'])->name('events.bulk-delete');
        Route::get('/events/{event}/guests', [GuestController::class, 'eventGuests'])->name('events.guests');
        Route::post('/guests/{guest}/check-in', [GuestController::class, 'checkIn'])->name('guests.check-in');
        Route::post('/guests/{guest}/check-out', [GuestController::class, 'checkOut'])->name('guests.check-out');
    });

    // Guests (Admin/Organizer only)
    Route::middleware('role:admin,organizer')->group(function () {
        Route::resource('guests', GuestController::class);
        Route::post('/guests/bulk-update', [GuestController::class, 'bulkUpdate'])->name('guests.bulk-update');
        
        // Export guests
        Route::get('/guests/export', [GuestController::class, 'exportGuests'])->name('guests.export');
        
        // Guest management routes
        Route::post('/guests/{guest}/confirm', [GuestController::class, 'confirm'])->name('guests.confirm');
        Route::post('/guests/{guest}/checkin', [GuestController::class, 'checkIn'])->name('guests.checkin');
        
        // Check-in routes
        Route::get('/events/{event}/guests', [GuestController::class, 'eventGuests'])->name('events.guests');
        Route::post('/guests/{guest}/check-in', [GuestController::class, 'checkIn'])->name('guests.check-in');
        Route::post('/guests/{guest}/check-out', [GuestController::class, 'checkOut'])->name('guests.check-out');
    });

    // Reports (Admin/Organizer only)
    Route::middleware('role:admin,organizer')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/events', [ReportController::class, 'events'])->name('reports.events');
        Route::get('/reports/organizers', [ReportController::class, 'organizers'])->name('reports.organizers');
    });

    // Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/inactive', [UserController::class, 'inactive'])->name('users.inactive');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
