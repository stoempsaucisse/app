<?php

namespace Microffice\Providers;

use Microffice\User;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'Microffice\User' => 'Microffice\Policies\UserPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate, DispatcherContract $events)
    {
        $this->registerPolicies($gate);

        //
    }
}
