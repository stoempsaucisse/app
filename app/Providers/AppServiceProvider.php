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
        // Bind :before, :after, :data and :composite data to any view.
        view()->composer('*', 'Microffice\Http\ViewComposers\BaseComposer');

        // Bind view specific data.
        view()->composer('user.fieldset', 'Microffice\Http\ViewComposers\UserComposer@fieldset');
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
