<?php

namespace Microffice\AccessControl\Support\Facades;

/**
 * @see \Microffice\AccessControl\Contracts\AccessControl
 */
class ACL extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Microffice\AccessControl\Contracts\AccessControl';
    }
}
