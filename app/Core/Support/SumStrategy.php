<?php

namespace Microffice\Core\Support;

use Microffice\Core\Contracts\Support\CalculateStrategy;

class SumStrategy implements CalculateStrategy
{
    /**
     * {@inheritdoc}
     */
    public function calculate(array $values, $originalValue = null)
    {
        foreach ($values as $value) {
            $originalValue += $value;
        }

        return $originalValue;
    }
}
