<?php

namespace Microffice\AccessControl\Policies;

use Microffice\AccessControl\Contracts\DecisionMaker as DecisionMakerContract ;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class CorePolicy
{

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given user has given ability on given resource with optional id
     *
     * @param  string           $ability
     * @param  array|null       $arguments
     *                          [User $user, string $resourceClassName, int|string $id = null]
     * @return bool
     */
    public function __call($ability, $arguments)
    {
        if (defined(MaskBuilder::class . '::MASK_'. $ability)) {
            $decisionMaker = app(DecisionMakerContract::class);
            return $decisionMaker->grants($ability, array_slice($arguments, 1));
        }
        return false;
    }
}
