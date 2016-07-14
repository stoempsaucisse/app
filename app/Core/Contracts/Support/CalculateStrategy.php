<?php

namespace Microffice\Core\Contracts\Support;

/**
 * Calculate Strategy Interface
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

interface CalculateStrategy
{
    /**
     * Cast data.
     *
     * @param  array        $values
     * @param  numeric      $originalValue
     */
    public function calculate(array $values, $originalValue = null);
}
