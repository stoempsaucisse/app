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
        // Set some usefull constants in globalscope
        define('THROW_ON_INVALID_ARG', 0x1);
        define('SKIP_ON_INVALID_ARG', 0x2);
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
