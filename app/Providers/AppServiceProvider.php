<?php

namespace Microffice\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Bind :before and :after data to any view.
        view()->composer('*', 'Microffice\Http\ViewComposers\BaseComposer');

        // Bind view specific data.
        // view()->composer('user.avatar', 'Microffice\Http\ViewComposers\UserComposer@avatar');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
