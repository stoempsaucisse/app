<?php

namespace Microffice\Listeners;

use Microffice\Events\ViewPrepare;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrepareView
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ViewPrepare  $event
     * @return void
     */
    public function handle(ViewPrepare $event)
    {
        return $event->viewFactory->make('user.index', ['users' => $event->data])->render();
    }
}
