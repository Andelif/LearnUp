<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\TuitionRequestController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;

// Authentication Routes


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::resource('learners', LearnerController::class);
    Route::resource('tutors', TutorController::class);
    Route::resource('tuition-requests', TuitionRequestController::class);
    Route::resource('applications', ApplicationController::class);
    Route::resource('messages', MessageController::class);
    Route::resource('notifications', NotificationController::class);
});
