<?php

namespace Microffice\Core\Support;

/**
 * This class implodes an array of values to a string
 *
 * When $nullify === true empty segments are translated to null
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;

class ImplodeDataCastingStrategy extends AbstractDataCastingStrategy implements DataCastingStrategyContract
{
    /**
     * Nullify empty strings?
     *
     * @var string
     */
    protected $nullify;

    /**
     * Create implode/explode array data casting strategy.
     *
     * @param  array    $keys
     * @return void
     */
    public function __construct(array $keys, $nullify = true)
    {
        parent::__construct($keys);
        $this->nullify = (bool) $nullify;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyCast($data, $default)
    {
        return implode(self::SEGMENTS_SEPARATOR, (array) $data);
    }

    /**
     * {@inheritdoc}
     */
    public function unCast($data)
    {
        $data = explode(self::SEGMENTS_SEPARATOR, $data);
        if ($this->nullify) {
            foreach ($data as $key => $value) {
                if ($value === '') {
                    $data[$key] = null;
                }
            }
        }
        return array_combine($this->keys, $data);
    }
}
