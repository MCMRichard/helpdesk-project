<?php

use App\Http\Controllers\ProblemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirect to login or dashboard
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Auth::routes();

// General authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/problems', [ProblemController::class, 'index'])->name('problems.index');
    Route::get('/problems/create', [ProblemController::class, 'create'])->name('problems.create');
    Route::post('/problems', [ProblemController::class, 'store'])->name('problems.store');
    Route::post('/problems/{problem}/assign', [ProblemController::class, 'assignSpecialist'])->name('problems.assign');
    Route::post('/problems/{problem}/resolve', [ProblemController::class, 'resolve'])->name('problems.resolve');
    Route::get('/problems/resolved', [ProblemController::class, 'resolved'])->name('problems.resolved');
    Route::post('/problems/{problem}/unassign', [ProblemController::class, 'unassignSpecialist'])->name('problems.unassign');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::post('/admin/users/{user}/expertise', [UserController::class, 'updateExpertise'])->name('admin.users.updateExpertise');
    Route::get('/admin/problems', [ProblemController::class, 'adminIndex'])->name('admin.problems');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
