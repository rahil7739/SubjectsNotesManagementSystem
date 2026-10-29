<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showUserLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginUser'])->name('login.store');
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginAdmin'])->name('login.store');
});

Route::middleware(['auth:web', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subject.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/create', [NoteController::class, 'create'])->name('notes.create');
    Route::get('/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::get('/attachments/{attachment}', [NoteController::class, 'download'])->name('attachments.download');
    Route::post('/logout', [AuthController::class, 'logoutUser'])->name('logout');
});

Route::middleware(['auth:admin', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'showAdmin'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateAdmin'])->name('profile.update');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::get('/users/deleted', [AdminController::class, 'deletedUsers'])->name('users.deleted');
    Route::post('/users/deleted/{user}/restore', [AdminController::class, 'restoreUser'])->name('users.restore');
    Route::delete('/users/deleted/{user}', [AdminController::class, 'forceDeleteUser'])->name('users.force-delete');
    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects.index');
    Route::get('/subjects/create', [AdminController::class, 'createSubject'])->name('subjects.create');
    Route::post('/subjects/assign', [AdminController::class, 'assignSubject'])->name('subjects.assign');
    Route::get('/notes', [AdminController::class, 'notes'])->name('notes.index');
    Route::get('/notes/create', [AdminController::class, 'createNote'])->name('notes.create');
    Route::post('/notes/assign', [AdminController::class, 'assignNote'])->name('notes.assign');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::post('/users/{user}/subjects', [AdminController::class, 'storeSubject'])->name('subjects.store');
    Route::put('/subjects/{subject}', [AdminController::class, 'updateSubject'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [AdminController::class, 'deleteSubject'])->name('subjects.destroy');
    Route::post('/users/{user}/notes', [AdminController::class, 'storeNote'])->name('notes.store');
    Route::put('/notes/{note}', [AdminController::class, 'updateNote'])->name('notes.update');
    Route::delete('/notes/{note}', [AdminController::class, 'deleteNote'])->name('notes.destroy');
    Route::get('/attachments/{attachment}', [AdminController::class, 'downloadAttachment'])->name('attachments.download');
    Route::get('/trash', [AdminController::class, 'trash'])->name('trash');
    Route::post('/trash/subjects/{subject}/restore', [AdminController::class, 'restoreSubject'])->name('subjects.restore');
    Route::delete('/trash/subjects/{subject}', [AdminController::class, 'forceDeleteSubject'])->name('subjects.force-delete');
    Route::post('/trash/notes/{note}/restore', [AdminController::class, 'restoreNote'])->name('notes.restore');
    Route::delete('/trash/notes/{note}', [AdminController::class, 'forceDeleteNote'])->name('notes.force-delete');
    Route::post('/logout', [AuthController::class, 'logoutAdmin'])->name('logout');
});
