<?php

namespace Microffice\AccessControl\Support;

/**
 * This class casts a value to a Symfony\Component\Security\Acl\Permission\MaskBuilder
 * or add() it if an original value is provided.
 * The original value is expected to be the result of an unCast() call
 *
 * Accepted values are :
 *      - a single mask code or mask name (resolved by the MaskBuilder::add())
 *      - an array or object from which values are extracted and added one to another
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Contracts\Support\CalculateStrategy;
use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Microffice\Core\Support\AbstractDataCastingStrategy;
use Microffice\Core\Support\SumStrategy;
use Microffice\Core\Support\Traits\BaseUncast;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

class MaskDataCastingStrategy extends AbstractDataCastingStrategy implements DataCastingStrategyContract
{
    use BaseUncast;

    /**
     * Create implode/explode array data casting strategy.
     *
     * @param  array    $keys
     * @return void
     */
    public function __construct(array $keys = null)
    {
        parent::__construct($keys);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyCast($value, $originalValue)
    {
        if ($originalValue === null) {
            $maskBuilder = app(MaskBuilderContract::class);
        } else {
            $maskBuilder = $originalValue;
        }
        if (is_a($value, MaskBuilderContract::class)) {
            $value = $value->get();
        }
        foreach ($values as $value) {
            // add() resolves masks too
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
