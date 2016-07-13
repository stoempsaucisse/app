<?php

namespace Microffice\Core\Support;

use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;

class ImplodeDataCastingStrategy extends AbstractArrayDataCastingStrategy implements DataCastingStrategyContract
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
    public function cast($data, $default = null)
    {
        $segments = $this->extractValues($data);
        return implode(self::SEGMENTS_SEPARATOR, $segments);
    }

    /**
     * {@inheritdoc}
     */
    public function unCast($data)
    {
        $data = explode(self::SEGMENTS_SEPARATOR, $data);
        if ($this->nullify) {
            foreach($data as $key => $value) {
                if($value === '') {
                    $data[$key] = null;
                }
            }
        }
        return array_combine($this->keys, $data);
    }
}
