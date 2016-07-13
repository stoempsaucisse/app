<?php

namespace Microffice\AccessControl\Support;

use Microffice\Core\Contracts\Support\CalculateStrategy;
use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Microffice\Core\Support\AbstractArrayDataCastingStrategy;
use Microffice\Core\Support\SumStrategy;
use Microffice\Core\Support\Traits\BaseUncast;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

class MaskDataCastingStrategy extends AbstractArrayDataCastingStrategy implements DataCastingStrategyContract
{
    use BaseUncast;

    /**
     * Create implode/explode array data casting strategy.
     *
     * @param  array    $keys
     * @return void
     */
    public function __construct($keys)
    {
        parent::__construct($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function cast($value, $originalValue = null)
    {
        $values = $this->extractValues($value);
        if ($originalValue === null) {
            $maskBuilder = app(MaskBuilderContract::class);
        } else {
            $maskBuilder = $originalValue;
        }
        if (is_a($value, MaskBuilderContract::class)) {
            $value = $value->get();
        }
        foreach ($values as $value) {
            // add() resolves mask too
            $maskBuilder->add($value);
        }
        return $maskBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function unCast($value)
    {
        return $value->get();
    }
}
