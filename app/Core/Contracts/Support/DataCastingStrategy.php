<?php

namespace Microffice\Core\Contracts\Support;

interface DataCastingStrategy
{
    /**
     * Cast data.
     *
     * @param  mixed        $value
     * @param  mixed        $default
     * @return mixed        mutated $value
     */
    public function cast($value, $default = null);

    /**
     * Uncast data.
     *
     * @param  string   $value
     * @return mixed    mutated $value
     */
    public function unCast($value);
}
