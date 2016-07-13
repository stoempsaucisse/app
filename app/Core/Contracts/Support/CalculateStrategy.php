<?php

namespace Microffice\Core\Contracts\Support;

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
