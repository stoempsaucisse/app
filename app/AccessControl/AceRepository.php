<?php

namespace Microffice\AccessControl;

use Microffice\Core\Repositories\AbstractBaseEloquentRepository;

class AceRepository extends AbstractBaseEloquentRepository
{
    /**
     * Create a new ace composer.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(Ace::class);
    }

    /**
     * Mutate validation rules before updating if needed.
     *
     * @param  array $data
     * @param  array $rules
     * @return array $rules
     */
    public function mutateRulesBeforeUpdate($data, $rules)
    {
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
        return $data;
    }
}
