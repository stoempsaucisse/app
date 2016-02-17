<?php

namespace Microffice\Events;

use Microffice\Events\Event;
use Illuminate\Contracts\View\Factory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ViewPrepare extends Event
{
    use SerializesModels;

    public $viewFactory;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Factory $viewFactory, $data)
    {
        $this->viewFactory = $viewFactory;
        $this->data = $data;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
