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

        /**
         *  Register view listeners here
         *  
         *  view.name:before        return view.name(s) to render 'before' this view.name
         *  view.name:after         return view.name(s) to render 'after' this view.name
         *  view.name:data          return an array of data to bound to this view
         *                          the keys are used as variable names
         *  view.name:composite     return an array with before, after and/or data values
         */

        // $events->listen('user.form:data', function($view){
        //     return ['userData' => 'avatar.avatar'];
        // });
        // $events->listen('user.form:data', function($view){
        //     return ['userData2' => 'avatar.avatar'];
        // });

        // $events->listen('avatar.chatLink:after', function($view){
        //     return 'avatar.avatar';
        // });

        // $events->listen('user.fieldset:after', function($view){
        //     // Stub to test without return value
        //     // This mean we may add logic to determine the return value
        //     // Example : check if user has contact => return contact.contact view
        //     $user = $view->offsetGet('user');
        //     if($user->name == 'Dworkin')
        //     {
        //         return 'avatar.avatar';
        //     }
        // });

        // Testing with return is array.
        // $events->listen('user.fieldset:composite', function($view){
        //     return ['before' => 'avatar.avatar',
        //         'after' => ['avatar.chatLink'],
        //         'test' => ['toto', 'tete']];
        // });

        // $events->listen('user.fieldset:before', function($view){
        //     return 'avatar.avatar-form';
        // });

        // $events->listen('user.fieldset:composite', function($view){
        //     return ['after' => 'avatar.chatLink',
        //         'test' => ['tata', 'titi']];
        // });

        // $events->listen('user.fieldset:before', function($view){
        //     // Stub to test without return value
        //     // This mean we may add logic to determine the return value
        //     // Example : check if user has contact => return contact.contact view
        //     $user = $view->offsetGet('user');
        //     if($user->name == 'Dworkin')
        //     {
        //         return 'avatar.avatar';
        //     }
        // });
    }
}
