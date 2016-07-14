<?php

namespace Microffice\Core\Support;

/**
 * This class just returns the value as-is excepted when
 * the KEYABLE_VALUES flag is set which means that the values returned
 * will be used as array keys and should be either an integer or string.
 * In this case :
 *      - if value is an array or object we "reduce" it to a single value
 *        by popping (default) or shifting (if ARRAY_SHIFT flag is set)
 *        a key-value pair from the element and select
 *        the key or value if PREFER_KEY flag is set :
 *              + if selected value is an integer or string, return it
 *              + if selected value is a boolean or nul return the $default (which could be null)
 *      - if given value is null or boolean we return
 *        the $default value (which could be null)
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Microffice\Core\Support\Traits\BaseUncast;

class BaseDataCastingStrategy extends AbstractDataCastingStrategy implements DataCastingStrategyContract
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
    public function __construct($flags = 0x0, array $keys = null)
    {
        parent::__construct($keys);
        $this->flags = $flags;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyCast($data, $default)
    {
        if (($this->flags & self::KEYABLE_VALUES)) {
            if (is_array($data) || is_object($data)) {
                $data = $this->reduce($this->getArrayableItems($data));
            }
            return (is_bool($data) || $data === null) ? $default : $data;
        }
        return $data;
    }

    /**
     * Reduce array|object $data to something usable as key value.
     *
     * @param  array    $data
     * @return void
     */
    protected function reduce($data)
    {
        $keys = array_keys($data);
        $value = ($this->flags & self::ARRAY_SHIFT) ? array_shift($data) : array_pop($data);
        if (is_array($value) || is_object($value)) {
            return $this->reduce($this->getArrayableItems($value));
        }
        if ($this->flags & self::PREFER_KEY) {
            $value = ($this->flags & self::ARRAY_SHIFT) ? array_shift($keys) : array_pop($keys);
        }
        return $value;
    }
}
