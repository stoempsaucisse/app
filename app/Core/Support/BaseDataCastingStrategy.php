<?php

namespace Microffice\Core\Support;

use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Microffice\Core\Support\Traits\BaseUncast;

class BaseDataCastingStrategy implements DataCastingStrategyContract
{
    use BaseUncast;

    const KEYABLE_VALUES = 0x1;
    const PREFER_KEY = 0x2;
    const ARRAY_SHIFT = 0x4;

    /**
     * Option flags
     *
     * @var string
     */
    protected $flags;

    /**
     * Create base data casting strategy.
     *
     * @param  array    $flags
     * @return void
     */
    public function __construct($flags = 0x0)
    {
        $this->flags = $flags;
    }

    /**
     * {@inheritdoc}
     */
    public function cast($data, $default = null)
    {
        if (($this->flags & self::KEYABLE_VALUES) && !(is_string($data) || is_numeric($data))) {
            if ($default !== null) {
                return $default;
            }
            return $this->reduce($this->getArrayableItems($data));
        }
        return (bool) ($this->flags & self::PREFER_KEY) ? $default : $data;

        // var_dump($data, $default);
        // if (is_string($data) || is_numeric($data)) {
        //     return ($default === null) ? $data : $default;
        // }
        // if (($this->flags & self::PREFER_KEY) && is_array($data)) {
        //     $value = ($this->flags & self::ARRAY_SHIFT) ? array_shift($data) : array_pop($data);
        //     return (empty($value) && ($this->flags & self::KEYABLE_VALUES)) ? $default : $value ;
        // }
        // return $default;
    }

    /**
     * Reduce array|object $data to something usable as key value.
     *
     * @param  array    $data
     * @return void
     */
    public function reduce($data)
    {
        if($this->flags & self::PREFER_KEY) {
            $keys = array_keys($data);
            $value = ($this->flags & self::ARRAY_SHIFT) ? array_shift($keys) : array_pop($keys);
            if (! empty($value)) {
                return $value;
            }
        }
        $value = ($this->flags & self::ARRAY_SHIFT) ? array_shift($data) : array_pop($data);
        if (is_string($value) || is_numeric($value)) {
            return $value;
        }
        
        return $this->reduce($this->getArrayableItems($value));
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
