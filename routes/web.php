<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

use App\Http\Controllers\LecturerController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\LearningFileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;

// Root route: redirect authenticated users to their dashboards, otherwise show Welcome
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->roles()->where('name', 'admin')->exists()) {
            return redirect('/admin/dashboard');
        }
        // default to user dashboard
        return redirect('/user/dashboard');
    }

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// API routes (protected by auth)
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Admin-only endpoints
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':admin'])->group(function () {
        Route::apiResource('lecturers', LecturerController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('classes', SchoolClassController::class);
        Route::apiResource('menus', MenuController::class);
        Route::get('users', [UserController::class, 'index']);
        Route::patch('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);
    });

    // Shared endpoints (admin + lecturer)
    Route::apiResource('agendas', AgendaController::class);
    Route::apiResource('files', LearningFileController::class);
    Route::get('files/{learningFile}/download', [LearningFileController::class, 'download']);
    Route::apiResource('attendances', AttendanceController::class);
});

// Admin web routes (Inertia) — protected by auth + role:admin
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('Admin/Dashboard'); })->name('admin.dashboard');

    Route::get('/dosen', function () { return Inertia::render('Admin/Lecturers/Index'); })->name('admin.dosen.index');
    Route::get('/dosen/create', function () { return Inertia::render('Admin/Lecturers/Create'); })->name('admin.dosen.create');
    Route::get('/dosen/{id}/edit', function ($id) { return Inertia::render('Admin/Lecturers/Edit', ['id' => $id]); })->name('admin.dosen.edit');

    Route::get('/mapel', function () { return Inertia::render('Admin/Subjects/Index'); })->name('admin.mapel.index');
    Route::get('/kelas', function () { return Inertia::render('Admin/Classes/Index'); })->name('admin.kelas.index');

    Route::get('/agenda', function () { return Inertia::render('Agendas/Index'); })->name('admin.agenda.index');
    Route::get('/files', function () { return Inertia::render('Files/Index'); })->name('admin.files.index');

    Route::get('/users', function () { return Inertia::render('Admin/Users/Index'); })->name('admin.users.index');
    Route::get('/wewenang', function () { return Inertia::render('Admin/Permissions/Index'); })->name('admin.wewenang.index');
    Route::get('/hak-akses', function () { return Inertia::render('Admin/Roles/Index'); })->name('admin.roles.index');
});

// User (lecturer) web routes — protected by auth + role:lecturer
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':lecturer'])->prefix('user')->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('User/Dashboard'); })->name('user.dashboard');
    Route::get('/agenda', function () { return Inertia::render('Agendas/Index'); })->name('user.agenda.index');
    Route::get('/files', function () { return Inertia::render('Files/Index'); })->name('user.files.index');
});
