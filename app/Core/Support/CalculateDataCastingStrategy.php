<?php

namespace Microffice\Core\Support;

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
    public function __construct($keys, CalculateStrategy $calculateStrategy = null)
    {
        parent::__construct($keys);
        $this->calculateStrategy = ($calculateStrategy === null) ? new SumStrategy : $calculateStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function cast($data, $default = null)
    {
        if(is_array($data)) {
            $data = $this->extractValues($data);
        }
        return $this->calculateStrategy->calculate($data, $default);
    }
}
