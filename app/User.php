<?php

namespace Microffice;

/**
 * User model
 *
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Traits\UpdateRules;

use Illuminate\Auth\Authenticatable;
/*use Jenssegers\Mongodb\Eloquent\Model;/*/
use Illuminate\Database\Eloquent\Model;/**/
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use UpdateRules, SoftDeletes, Authenticatable, Authorizable, CanResetPassword;

    /**
     * Set mutator to encrypt the password on insert
     *
     * @return void
     */
    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Validation Rules
     * @var array
     */
    public static $rules = array(
        'name' => 'required|max:255|unique:users',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|confirmed|min:6',
    );
}
