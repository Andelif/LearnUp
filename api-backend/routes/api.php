<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\TuitionRequestController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;

// Authentication Routes


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/tuition-requests', [TuitionRequestController::class, 'index']);
Route::get('/tuition-requests/{id}', [TuitionRequestController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/user/update-profile', [UserController::class, 'updateProfile']);

    Route::resource('learners', LearnerController::class);
    Route::resource('tutors', TutorController::class);
    Route::resource('admins', AdminController::class);

    Route::put('/tutors/{id}', [TutorController::class, 'update']);
    Route::put('/learners/{id}', [LearnerController::class, 'update']);

    Route::post('/tuition-requests', [TuitionRequestController::class, 'store']); 
    Route::put('/tuition-requests/{id}', [TuitionRequestController::class, 'update']);
    Route::delete('/tuition-requests/{id}', [TuitionRequestController::class, 'destroy']);


    Route::post('applications',  [ApplicationController::class, 'store']);
    //Route::resource('messages', MessageController::class);
    Route::resource('notifications', NotificationController::class);

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index']);  
    Route::post('/notifications', [NotificationController::class, 'store']); 
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']); 
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']); 




    //Admin Functionalities
    Route::get('/admin/learners', [AdminController::class, 'getLearners']);
    Route::get('/admin/tutors', [AdminController::class, 'getTutors']);
    Route::get('/admin/tuition-requests', [AdminController::class, 'getTuitionRequests']);
    Route::get('/admin/applications', [AdminController::class, 'getApplications']);
    Route::get('/admin/applications/{tuition_id}', [AdminController::class, 'getApplicationsByTuitionID']);

});
