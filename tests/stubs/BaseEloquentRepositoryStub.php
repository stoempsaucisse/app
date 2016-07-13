<?php

namespace Microffice\Tests\Stubs;

use Microffice\Core\Repositories\AbstractBaseEloquentRepository;
use Microffice\Tests\Stubs\BaseEloquentStub;

class BaseEloquentRepositoryStub extends AbstractBaseEloquentRepository
{
    /**
     * Mutate validation rules before updating if needed.
     *
     * @param  array $data
     * @param  array $rules
     * @return array $rules
     */
    public function mutateRulesBeforeUpdate($data, $rules)
    {
        if(isset($data['data_to_remove']))
        {
            unset($rules['data_to_remove']);
        }
        return $rules;
    }

    /**
     * Mutate data before updating if needed.
     *
     * @param  array validated $data
     * @return array $data
     */
    public function mutateDataBeforeUpdate($data)
    {
        if(isset($data['data_to_remove']))
        {
            unset($data['data_to_remove']);
        }
        return $data;
    }
}


