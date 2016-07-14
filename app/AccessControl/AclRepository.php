<?php

namespace Microffice\AccessControl;

/**
 * Acl Repository
 *
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Repositories\AbstractBaseEloquentRepository;

class AclRepository extends AbstractBaseEloquentRepository
{
    /**
     * Create a new ace composer.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(Acl::class);
    }

    /**
     * {@inheritdoc}
     */
    public function mutateRulesBeforeUpdate($data, $rules)
    {
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function mutateDataBeforeUpdate($data)
    {
        return $data;
    }
}
