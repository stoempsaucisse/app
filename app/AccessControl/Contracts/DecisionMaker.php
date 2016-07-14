<?php

namespace Microffice\AccessControl\Contracts;

/**
 * Decision Maker Interface
 *
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

interface DecisionMaker
{

    /**
     * Checks if is granted ability for given object identity
     *
     * @param string    $ability
     * @param array     $arguments
     * @return bool
     */
    public function grants($ability, array $arguments);

    /**
     * Checks if is granted ability for given object identity
     *
     * @param string    $ability
     * @param array     $arguments
     * @return bool
     */
    public function denies($ability, $arguments);

    /**
     * Checks if ability is guarded for all objects.
     *
     * @param string        $ability
     * @param string        $objectIdentity
     * @param string|void   $objectField
     * @return bool
     */
    public function decidesForAll($ability, $objectIdentity, $objectField = null);

    /**
     * Get allowed ids for given object / mask combination.
     *
     * @param string    $ability
     * @param string    $objectIdentity
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllowedIds($ability, $objectIdentity);
}
