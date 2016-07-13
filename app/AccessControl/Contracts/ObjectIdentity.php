<?php

namespace Microffice\AccessControl\Contracts;

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
