<?php

namespace Microffice\AccessControl\Contracts;

/**
 * Object Identity Interface
 *
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

interface ObjectIdentity
{
    /**
     * Get the Object Identity
     *
     * @return array
     */
    public function get();

    /**
     * Set portion on Object Identity
     *
     * @param  string   $name
     * @param  string   $value
     * @return array
     */
    public function set($name, $value);
}
