<?php

namespace Microffice\Core\Support;

use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractArrayDataCastingStrategy
{
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
     * @param  array    $keys
     * @return void
     */
    public function __construct(array $keys)
    {
        $this->keys = (array) $keys;
    }

    /**
     * Extract values from $data with $keys
     *
     * @param  array  $data
     * @return array
     */
    protected function extractValues($data)
    {
        return array_intersect_key($this->getArrayableItems($data), array_flip($this->keys));
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof Arrayable) {
            return $items->toArray();
        } elseif ($items instanceof Jsonable) {
            return json_decode($items->toJson(), true);
        } elseif ($items instanceof JsonSerializable) {
            return $items->jsonSerialize();
        }

        return (array) $items;
    }
}
