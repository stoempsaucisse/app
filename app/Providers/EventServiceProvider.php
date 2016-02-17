<?php

namespace Microffice\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // 'Microffice\Events\ViewPrepare' => [
        //     'Microffice\Listeners\PrepareView',
        // ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        
        // $events->listen('user.user:after', function($view){
        //     return 'user.chatLink';
        // });
        // $events->listen('user.user:after', function($view){
        //     // Stub to test without return value
        //     // This mean we may add logic to determine the return value
        //     // Example : check if user has contact => return contact.contact view
        //     $user = $view->offsetGet('user');
        //     if($user->name == 'Dworkin')
        //     {
        //         return 'user.avatar';
        //     }
        // });
        // Testing with return is array.
        // $events->listen('user.user:compose', function($view){
        //     return ['before' => 'user.avatar',
        //         'after' => ['user.chatLink'],
        //         'test' => ['toto', 'tete']];
        // });
        // $events->listen('user.form:before', function($view){
        //     return 'user.avatar-form';
        // });
        // $events->listen('user.user:compose', function($view){
        //     return ['after' => 'user.chatLink',
        //         'test' => ['tata', 'titi']];
        // });
        // $events->listen('user.user:before', function($view){
        //     // Stub to test without return value
        //     // This mean we may add logic to determine the return value
        //     // Example : check if user has contact => return contact.contact view
        //     $user = $view->offsetGet('user');
        //     if($user->name == 'Dworkin')
        //     {
        //         return 'user.avatar';
        //     }
        // });
    }
}
