<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
    if (Schema::hasTable('school_profile')) {
        View::share('schoolProfile', \App\Models\SchoolProfile::first());
    }

    // Share active session & term on every admin view
    View::composer('admin.*', function ($view) {
        $view->with('globalActiveSession', \App\Models\AcademicSession::where('is_current', true)->first());
        $view->with('globalActiveTerm', \App\Models\Term::where('is_current', true)->first());
    });
}
}
