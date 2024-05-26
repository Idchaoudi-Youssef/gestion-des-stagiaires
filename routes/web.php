<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Supervisor;
use App\Http\Middleware\Intern;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware([Admin::class])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'getUsers'])->name('admin.users');
        Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::get('/admin/final-reports', [AdminController::class, 'viewFinalReports'])->name('admin.finalReports');

    });
    Route::middleware([Supervisor::class])->group(function () {
        Route::get('/supervisor/tasks', [SupervisorController::class, 'viewTasks'])->name('supervisor.tasks');
        Route::get('/supervisor/tasks/create', [SupervisorController::class, 'createTask'])->name('supervisor.tasks.create');
        Route::post('/supervisor/tasks', [SupervisorController::class, 'storeTask'])->name('supervisor.tasks.store');
        Route::get('/supervisor/tasks/{task}/edit', [SupervisorController::class, 'editTask'])->name('supervisor.tasks.edit');
        Route::put('/supervisor/tasks/{task}', [SupervisorController::class, 'updateTask'])->name('supervisor.tasks.update');
        Route::delete('/supervisor/tasks/{task}', [SupervisorController::class, 'destroyTask'])->name('supervisor.tasks.destroy');
        Route::get('/supervisor/weekly-reports', [SupervisorController::class, 'viewWeeklyReports'])->name('supervisor.weeklyReports');
    });
    Route::middleware([Intern::class])->group(function () {

    });
        // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
