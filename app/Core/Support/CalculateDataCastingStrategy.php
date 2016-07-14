<?php

namespace Microffice\Core\Support;

/**
 * This class apply a calculation Strategy to given value(s)
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Contracts\Support\CalculateStrategy;
use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Microffice\Core\Support\Traits\BaseUncast;

class CalculateDataCastingStrategy extends AbstractArrayDataCastingStrategy implements DataCastingStrategyContract
{
    use BaseUncast;

    /**
     * Create implode/explode array data casting strategy.
     *
     * @param  array    $keys
     * @return void
     */
    public function __construct(array $keys = null, CalculateStrategy $calculateStrategy = null)
    {
        parent::__construct($keys);
        $this->calculateStrategy = ($calculateStrategy === null) ? new SumStrategy : $calculateStrategy;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyCast($data, $default)
    {
        return $this->calculateStrategy->calculate($data, $default);
    }
}
