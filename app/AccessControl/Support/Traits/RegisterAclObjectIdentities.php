<?php

namespace Microffice\AccessControl\Support\Traits;

/**
 * This traits registers object identities from $this->objectIdentities array
 * You MUST add
 * $this->registerAclObjectIdentities($events);
 * in your boot method
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\AccessControl\DecisionMaker;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

trait RegisterAclObjectIdentities
{
    // This traits registers object identities from $this->objectIdentities array
    //
    // You need to add
    // $this->registerAclObjectIdentities($events);
    // in your boot method

    /**
     * Register object identities for Access Control.
     *
     * @param Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    protected function registerAclObjectIdentities(DispatcherContract $events)
    {
        if (isset($this->objectIdentities)) {
            foreach ((array) $this->objectIdentities as $objectIdentity) {
                $events->listen(DecisionMaker::$gatherAllOIEventName, function () use ($objectIdentity) {
                    return $objectIdentity;
                });
            }
        }
    }
}
