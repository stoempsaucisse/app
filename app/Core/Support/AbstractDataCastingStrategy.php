<?php

namespace Microffice\Core\Support;

/**
 * All Data Casting classes SHOULD inherit from this abstract class
 * to ensure that $data always is filtered on eventual $keys
 * aka. final cast() calls applyCast() with extracted values from $data.
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Microffice\Core\Support\Traits\GetArrayableItems;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractDataCastingStrategy
{
    use GetArrayableItems;

    const SEGMENTS_SEPARATOR = '.';

    /**
     * The keys to use from given arrayable element
     *
     * @var array
     */
    protected $keys;

    /**
     * Create array data casting strategy.
     *
     * @param  array|null    $keys
     * @return void
     */
    public function __construct($keys)
    {
        $this->keys = ($keys !== null) ? (array) $keys : null;
    }

    /**
     * {@inheritdoc}
     */
    final public function cast($data, $default = null)
    {
        return $this->applyCast($this->extractValues($data), $default);
    }

    /**
     * Extract values from $data with $this->keys
     *
     * @param  array  $data
     * @return array
     */
    protected function extractValues($data)
    {
        if ($this->keys === null || !(is_array($data) || is_object($data))) {
            return $data;
        }
        return array_intersect_key($this->getArrayableItems($data), array_flip($this->keys));
        
    }
}
