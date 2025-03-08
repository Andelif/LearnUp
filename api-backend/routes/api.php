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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConfirmedTuitionController;
<<<<<<< Updated upstream
use App\Http\Controllers\PaymentController;

=======
>>>>>>> Stashed changes



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/tuition-requests/all', [TuitionRequestController::class, 'index']);
Route::get('/tuition-requests/{id}', [TuitionRequestController::class, 'show']);
Route::get('/tuition-requests/filter', [TuitionRequestController::class, 'filterTuitionRequests']);

// Authentication Routes

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
    Route::get('/tuition-requests', [TuitionRequestController::class, 'getAllRequests']);

    Route::post('applications',  [ApplicationController::class, 'store']);
    Route::get('/tutor/{userId}/stats', [ApplicationController::class, 'getTutorStats']);
    Route::get('/applications/check/{tuition_id}', [ApplicationController::class, 'checkApplication']); 
    Route::get('/learner/{userId}/stats', [ApplicationController::class, 'getLearnerStats']);
    //Route::resource('messages', MessageController::class);
    Route::resource('notifications', NotificationController::class);

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index']);  
    Route::post('/notifications', [NotificationController::class, 'store']); 
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']); 
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    
    

    //Message Routes
    Route::post('/messages', [MessageController::class, 'sendMessage']); 
    Route::get('/messages/{userId}', [MessageController::class, 'getMessages']); 
    Route::put('/messages/seen/{senderId}', [MessageController::class, 'markAsSeen']); 


    //Message Routes
    Route::post('/messages', [MessageController::class, 'sendMessage']); 
    Route::get('/messages/{userId}', [MessageController::class, 'getMessages']); 
    Route::put('/messages/seen/{senderId}', [MessageController::class, 'markAsSeen']); 
    Route::get('/matched-users', [MessageController::class, 'getMatchedUsers']); 
    Route::post('/reject-tutor', [MessageController::class, 'rejectTutor']);


    //Admin Functionalities
    Route::get('/admin/learners', [AdminController::class, 'getLearners']);
    Route::delete('/admin/learners/{id}', [AdminController::class, 'deleteLearner']);
    Route::get('/admin/tutors', [AdminController::class, 'getTutors']);
    Route::delete('/admin/tutors/{id}', [AdminController::class, 'deleteTutor']);
    Route::get('/admin/tuition-requests', [AdminController::class, 'getTuitionRequests']);
    Route::get('/admin/applications', [AdminController::class, 'getApplications']);
    Route::get('/admin/applications/{tuition_id}', [AdminController::class, 'getApplicationsByTuitionID']);
    Route::post('/admin/match-tutor', [AdminController::class, 'matchTutor']);


    //Dashboard Route
    Route::get('/dashboard/{userId}/{role}', [DashboardController::class, 'getDashboardStats']);


    //Confirmed Tution
    Route::resource('confirmed-tuitions', ConfirmedTuitionController::class);

    Route::get('/confirmed-tuitions', [ConfirmedTuitionController::class, 'index']);  
    Route::post('/confirmed-tuitions', [ConfirmedTuitionController::class, 'store']);
<<<<<<< Updated upstream
    Route::get('/confirmed-tuition/invoice/{tutionId}', [ConfirmedTuitionController::class, 'getPaymentVoucher']);
    Route::post('/payment-marked/{tutionId}', [ConfirmedTuitionsController::class, 'markPayment']);
    Route::delete('/confirmed-tuitions/{id}', [ConfirmedTuitionController::class, 'destroy']);
   
    
    //Payment Routes
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);

=======
    Route::put('/confirmed-tuitions/{id}', [ConfirmedTuitionController::class, 'update']);
    Route::delete('/confirmed-tuitions/{id}', [ConfirmedTuitionController::class, 'destroy']);
    
>>>>>>> Stashed changes

});
