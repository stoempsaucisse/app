<?php

namespace Microffice\AccessControl\Contracts;

/**
 * Object Identity Factory Interface
 *
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

interface ObjectIdentityFactory
{
    /**
     * Make a new Object Identity
     *
     * @param  string       $name
     * @param  array|void   $details
     * @return ObjectIdentity
     */
    public function make($name);

    /**
     * Get key names
     *
     * @return array    $keyNames
     */
    public function getKeyNames();
}
