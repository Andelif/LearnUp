<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
