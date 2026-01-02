<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Agenda;
use App\Models\LearningFile;
use App\Policies\AgendaPolicy;
use App\Policies\LearningFilePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // register model policies
        Gate::policy(Agenda::class, AgendaPolicy::class);
        Gate::policy(LearningFile::class, LearningFilePolicy::class);
    }
}
