<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AdminService;
use App\Services\TuitionRequestService;
use App\Services\UserService;
use App\Services\TutorService;
use App\Services\LearnerService;
use App\Services\ApplicationService;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(TuitionRequestService::class, function ($app) {
            return new TuitionRequestService();
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService();
        });

        $this->app->bind(TutorService::class, function ($app) {
            return new TutorService();
        });

        $this->app->bind(LearnerService::class, function ($app) {
            return new LearnerService();
        });

        $this->app->bind(ApplicationService::class, function ($app) {
            return new ApplicationService();
        });

        $this->app->bind(AdminService::class, function ($app) {
            return new AdminService();
        });

        $this->app->bind(NotificationService::class, function ($app) {
            return new NotificationService();
        });    
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
