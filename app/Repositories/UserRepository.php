<?php

namespace Microffice\Repositories;

use Microffice\User;

use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Gate;
use Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\UrlGenerator;

class UserRepository
{
    /**
     * The events dispatcher.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * Create a new base composer.
     *
     * @param  Dispatcher $events
     * @return void
     */
    public function __construct(Dispatcher $events)
    {
        // Dependencies automatically resolved by service container...
        $this->events = $events;
    }

    /**
     * Get all Users.
     *
     * @return Collection
     */
    public function findAll()
    {
        if(Gate::denies('findAll', User::class))
        {
            return User::find($this->allowedIds())->sortBy('name');
        }
        return User::all()->sortBy('name');
    }

    /**
     * Get list of id allowed for current authenticated user.
     *
     * @return Collection
     */

    // THIS IS A STUB and should provide a real array of ids.
    public function allowedIds()
    {
        return User::all()->modelKeys();
    }
    
    /**
     * Get one User.
     *
     * @param  integer $userId
     * @return User | Exception
     */
    public function findById($userId)
    {
        return User::findOrFail($userId);
    }

    /**
    * Validate the specified User data.
    *
    * @param array $data
    * @param array $rules
    * @return Bool
    */
    public function validate($data, $rules = null)
    {
        $validator = Validator::make(array_dot($data), is_null($rules) ? User::$rules : $rules);
        if($validator->fails()) throw new ValidationException(
                $validator, 
                back()->withInput(request()->except('user.password'))
                      ->withErrors($validator->errors()->getMessages(), 'default')
        );
        return true;
    }

    /**
     * Create and save a new user to database.
     *
     * @param  array $data
     * @return User instance
     */
    public function saveNew($data)
    {
        if(Gate::denies('create', User::class))
        {
            throw new AuthorizationException(trans('error.create', ['resource' => trans_choice('user.user', 1)]), 403);
        }
        // Validate $data
        $this->validate($data);
        $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => bcrypt($data['password'])]);
        // Fire create event
        $this->events->fire('user:create', [$user]);
        return $user;
    }

    /**
     * Update existing user.
     *
     * @param  integer $userId
     * @param  array validated $data
     * @return bool
     */
    public function update($userId, $data)
    {
        if(Gate::denies('update', [User::class, $userId]))
        {
            throw new AuthorizationException(trans('error.update', ['resource' => trans_choice('user.user', 1)]), 403);
        }
        // Retrieve user to update
        $user = User::findOrFail($userId);
        // Fire update event for this user
        $this->events->fire('user:update', [$user]);

        // Gather update rules
        $rules = $user->updateRules();
        // Remove password rules if password is not updated
        if(! isset($data['password']))
        {
            unset($rules['password']);
        }
        // Validate data
        $this->validate($data, $rules);
        // Remove data that has not changed
        foreach ($data as $key => $value)
        {
            if($user->$key == $data[$key])
            {
                unset($data[$key]);
            }
        }
        // Encrypt password
        if(isset($data['password']))
        {
            $data['password'] = bcrypt($data['password']);
        }
        return $user->update($data);
    }

    /**
     * Delete user from database.
     *
     * @param  integer $userId
     * @return bool
     */
    public function delete($userId)
    {
        if(Gate::denies('delete', [User::class, $userId]))
        {
            throw new AuthorizationException(trans('error.delete', ['resource' => trans_choice('user.user', 1)]), 403);
        }
        return User::destroy($userId);
    }
}