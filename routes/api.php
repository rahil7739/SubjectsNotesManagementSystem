<?php

use App\Http\Controllers\Api\AdminController as ApiAdminController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\NoteController as ApiNoteController;
use App\Http\Controllers\Api\SubjectController as ApiSubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.key', 'throttle:10,1'])->prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
});


Route::middleware(['api.key', 'api.token'])->group(function () {
    Route::get('/auth/profile', [ApiAuthController::class, 'profile']);
    Route::put('/auth/profile', [ApiAuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);

    Route::apiResource('subjects', ApiSubjectController::class)->names([
        'index' => 'api.subjects.index',
        'store' => 'api.subjects.store',
        'show' => 'api.subjects.show',
        'update' => 'api.subjects.update',
        'destroy' => 'api.subjects.destroy',
    ]);

    
    Route::apiResource('notes', ApiNoteController::class)->names([
        'index' => 'api.notes.index',
        'store' => 'api.notes.store',
        'show' => 'api.notes.show',
        'update' => 'api.notes.update',
        'destroy' => 'api.notes.destroy',
    ]);
    Route::get('/attachments/{attachment}', [ApiNoteController::class, 'download']);
});

Route::middleware(['api.key', 'api.token', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);
    Route::get('/users', [ApiAdminController::class, 'users']);
    Route::get('/users/{user}', [ApiAdminController::class, 'user']);
    Route::delete('/users/{user}', [ApiAdminController::class, 'deleteUser']);
    Route::get('/trash', [ApiAdminController::class, 'trash']);
    Route::post('/trash/users/{user}/restore', [ApiAdminController::class, 'restoreUser']);
    Route::delete('/trash/users/{user}', [ApiAdminController::class, 'forceDeleteUser']);
    Route::post('/trash/subjects/{subject}/restore', [ApiAdminController::class, 'restoreSubject']);
    Route::delete('/trash/subjects/{subject}', [ApiAdminController::class, 'forceDeleteSubject']);
    Route::post('/trash/notes/{note}/restore', [ApiAdminController::class, 'restoreNote']);
    Route::delete('/trash/notes/{note}', [ApiAdminController::class, 'forceDeleteNote']);
    Route::delete('/attachments/{attachment}', [ApiAdminController::class, 'deleteAttachment']);
});
