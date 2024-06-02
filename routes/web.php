<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Supervisor;
use App\Http\Middleware\Intern;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\InternController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/newuser', function () {
    return view('newuser');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware([Admin::class])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'getUsers'])->name('admin.users');
        Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::get('/admin/final-reports', [AdminController::class, 'viewFinalReports'])->name('admin.finalReports');
        Route::get('/admin/assign', [AdminController::class, 'showAssignForm'])->name('admin.showAssignForm');
    Route::post('/admin/assign', [AdminController::class, 'assignInternsToSupervisors'])->name('admin.assignInternsToSupervisors');

    });
    Route::middleware([Supervisor::class])->group(function () {
        Route::get('/supervisor/dashboard', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
        Route::get('/supervisor/tasks', [SupervisorController::class, 'viewTasks'])->name('supervisor.tasks');
        Route::get('/supervisor/tasks/create', [SupervisorController::class, 'createTask'])->name('supervisor.tasks.create');
        Route::post('/supervisor/tasks', [SupervisorController::class, 'storeTask'])->name('supervisor.tasks.store');
        Route::get('/supervisor/tasks/{task}/edit', [SupervisorController::class, 'editTask'])->name('supervisor.tasks.edit');
        Route::put('/supervisor/tasks/{task}', [SupervisorController::class, 'updateTask'])->name('supervisor.tasks.update');
        Route::delete('/supervisor/tasks/{task}', [SupervisorController::class, 'destroyTask'])->name('supervisor.tasks.destroy');
        Route::get('/supervisor/weekly-reports', [SupervisorController::class, 'viewWeeklyReports'])->name('supervisor.weeklyReports');
    });
    Route::middleware([Intern::class])->group(function () {
        Route::get('/tasks/filter', [InternController::class, 'filter'])->name('tasks.filter');
        Route::get('/tasks/search', [InternController::class, 'search'])->name('tasks.search');
        Route::put('/tasks/{task}/complete', [InternController::class, 'completeTask'])->name('task.complete');
        Route::get('/intern/dashboard', [InternController::class, 'index'])->name('intern.dashboard');
        Route::get('/intern/tasks', [InternController::class, 'viewTasks'])->name('intern.tasks');
        Route::get('/intern/reports', [InternController::class, 'viewReports'])->name('intern.reports');
        Route::post('/intern/reports', [InternController::class, 'submitReport'])->name('intern.submitReport');
        Route::get('/intern/attestation', [InternController::class, 'attestation'])->name('intern.attestation');
    });
        // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
