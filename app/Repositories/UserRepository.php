<?php

namespace Microffice\Repositories;

use Microffice\AccessControl\Contracts\AccessControl as AccessControlContract;
use Microffice\Core\Repositories\AbstractBaseEloquentRepository;

use Microffice\User;

use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Gate;
use Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\UrlGenerator;

class UserRepository extends AbstractBaseEloquentRepository
{
    /**
     * Create a new ace composer.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(User::class);
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
        if (! isset($data['password'])) {
            unset($rules['password']);
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
        return $data;
    }

    /**
     * Get all Resources.
     *
     * @return Collection
     */
    public function findAll()
    {
        $resourceClassName = $this->resourceClassName;
        if (Gate::denies('view', [$resourceClassName])) {
            throw new AuthorizationException(trans('error.view-list', ['resources' => trans_choice(lcfirst($this->resourceName) . '.' . lcfirst($this->resourceName), 2)]), 403);
        }

        $resource = app(AccessControlContract::class);
        if ($resource->decidesForAll('view', $resourceClassName)) {
            return $resourceClassName::withTrashed()->get();
        } else {
            return $resourceClassName::find($resource->getAllowedIds('view', $resourceClassName));
        }
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
        if ($validator->fails()) {
            throw new ValidationException(
                $validator,
                back()->withInput(request()->except('user.password'))
                      ->withErrors($validator->errors()->getMessages(), 'default')
            );
        }
        return true;
    }
}
