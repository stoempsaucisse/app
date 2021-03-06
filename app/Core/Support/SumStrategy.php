<?php

namespace Microffice\Core\Support;

/**
 * This class sum given value(s)
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Contracts\Support\CalculateStrategy;

class SumStrategy implements CalculateStrategy
{
    /**
     * {@inheritdoc}
     */
    public function calculate($values, $originalValue = null)
    {
        if (is_string($values) || is_numeric($values)) {
            $values = (array) $values;
        }
        foreach ($values as $value) {
            $originalValue += $value;
        }

        return $originalValue;
    }
}
