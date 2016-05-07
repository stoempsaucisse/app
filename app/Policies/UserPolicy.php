<?php

namespace Microffice\Policies;

use Microffice\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

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
     * Give full access to super users.
     *
     * @return bool
     */
    public function before($user, $ability)
    {
        // if ($user->isSuperAdmin())
        // {
        //     return true;
        // }
    }

    /**
     * Determine if the current authenticated user may see all users.
     *
     * @return bool
     */
    public function findAll(User $user)
    {
        //  This is a stub, waiting for a real role + perms implementation
        return $user->name == 'Dworkin';
    }

    /**
     * Determine if the current authenticated user may create new users.
     *
     * @return bool
     */
    public function create(User $user)
    {
        //  This is a stub, waiting for a real role + perms implementation
        return $user->name == 'Dworkin';
    }

    /**
     * Determine if the current authenticated user may update this $userId
     * Thus himself or being Admin
     *
     * @return bool
     */
    public function update(User $user, $policyClass, $userId)
    {
        //  This is a stub, waiting for a real role + perms implementation
        return $user->id == $userId || $user->name == 'Dworkin';
    }

    /**
     * Determine if the current authenticated user may update this $userId's name
     * Thus himself
     *
     * @return bool
     */
    public function updateName(User $user, $policyClass, $userId)
    {
        //  This is a stub, waiting for a real role + perms implementation
        return $user->id == $userId;
    }

    /**
     * Determine if the current authenticated user may update this $userId's password
     * Thus himself
     *
     * @return bool
     */
    public function updatePassword(User $user, $policyClass, $userId)
    {
        //  This is a stub, waiting for a real role + perms implementation
        return $user->id == $userId;
    }

    /**
     * Determine if the current authenticated user may delete users
     *
     * @return bool
     */
    public function delete(User $user, $policyClass, $userId)
    {
        //  This is a stub, waiting for a real role + perms implementation
        if($user->id == $userId)
        {
            return false;
        }
        return $user->name == 'Dworkin';
    }
}
