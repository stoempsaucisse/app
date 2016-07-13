<?php

namespace Microffice\Core\Repositories;

use Microffice\Core\Contracts\ResourceRepository as ResourceRepositoryContract;
use Microffice\AccessControl\Contracts\DecisionMaker as DecisionMakerContract ;

use Gate;
use Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * This abstract class implements nearly all the ResourceRepository interface methods.
 */
abstract class AbstractBaseEloquentRepository implements ResourceRepositoryContract
{
    /**
     * @var string
     */
    protected $resourceClassName;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * Create a new base composer.
     *
     * @return void
     */
    public function __construct($className = null, $resourceName = null)
    {
        // Dependencies automatically resolved by service container...
        $this->setResourceClassName($className);
        $this->setResourceName($resourceName);
    }

    /**
     * Set the Resource class name
     *
     * @param  string|null  $className
     * @return string       $className
     */
    protected function setResourceClassName($className = null)
    {
        $className = is_null($className) ? get_called_class() : $className;
        $this->resourceClassName = str_replace('Repository', '', $className);
    }

    /**
     * Set the Resource name
     *
     * @return void
     */
    protected function setResourceName($resourceName = null)
    {
        $resourceName = is_null($resourceName) ? $this->resourceClassName : $resourceName;
        $start = strrpos($resourceName, '\\');
        $this->resourceName = substr($resourceName, ++$start);
    }

    /**
     * Get the Resource class name
     *
     * @param  string|null  $className
     * @return string       $className
     */
    public function getResourceClassName()
    {
        return $this->resourceClassName;
    }

    /**
     * Get the Resource name
     *
     * @return void
     */
    public function getResourceName()
    {
        return $this->resourceName;
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

        $resource = app(DecisionMakerContract::class);
        if ($resource->decidesForAll('view', $resourceClassName)) {
            return $resourceClassName::all();
        } else {
            return $resourceClassName::find($resource->getAllowedIds('view', $resourceClassName));
        }
    }
    
    /**
     * Get one Resource.
     *
     * @param  integer $resourceId
     * @return Resource | Exception
     */
    public function findById($resourceId)
    {
        $resourceClassName = $this->resourceClassName;
        if (Gate::denies('view', [$resourceClassName, $resourceId])) {
            throw new AuthorizationException(trans('error.view', ['resource' => trans(lcfirst($this->resourceName) . '.' . lcfirst($this->resourceName))]), 403);
        }
        return $resourceClassName::findOrFail($resourceId);
    }

    /**
    * Validate the specified Resource data.
    *
    * @param array $data
    * @param array $rules
    * @return Bool
    */
    public function validate($data, $rules = null)
    {
        $resourceClassName = $this->resourceClassName;
        $validator = Validator::make(array_dot($data), is_null($rules) ? $resourceClassName::$rules : $rules);
        if ($validator->fails()) {
            throw new ValidationException(
                $validator,
                back()->withInput(request()->input())
                      ->withErrors($validator->errors()->getMessages(), 'default')
            );
        }
        return true;
    }

    /**
     * Create and save a new Resource to database.
     *
     * @param  array $data
     * @return Resource instance
     */
    public function saveNew($data)
    {
        $resourceClassName = $this->resourceClassName;
        if (Gate::denies('create', $resourceClassName)) {
            throw new AuthorizationException(trans('error.create', ['resource' => trans(lcfirst($this->resourceName) . '.' . lcfirst($this->resourceName))]), 403);
        }
        // Validate $data
        $this->validate($data);
        // Get a new Resource instance
        $resource = app($resourceClassName);
        // Set mass asignable attributes
        $fillable = [];
        if (count($resource->getFillable()) > 0) {
            $fillable = array_intersect_key($data, array_flip($resource->getFillable()));
            $resource->fill($fillable);
        }
        // Set protected attributes
        if (count($data) > count($fillable)) {
            $notFillable = array_diff($data, $fillable);
            foreach ($notFillable as $key => $value) {
                $resource->$key = $value;
            }
        }
        // Save to DB
        $resource->save();
        return $resource;
    }

    /**
     * Update existing Resource.
     *
     * @param  integer $resourceId
     * @param  array validated $data
     * @return bool
     */
    public function update($resourceId, $data)
    {
        $resourceClassName = $this->resourceClassName;
        if (Gate::denies('edit', [$resourceClassName, $resourceId])) {
            throw new AuthorizationException(trans('error.edit', ['resource' => trans(lcfirst($this->resourceName) . '.' . lcfirst($this->resourceName))]), 403);
        }
        // Retrieve Resource to update
        $resource = app($resourceClassName)->findOrFail($resourceId);
        // Gather update rules
        $rules = $resource->updateRules();
        // Give a chance to classes that extends this abstract class
        // to modify $rules, without to have to re-write the whole update()
        $rules = $this->mutateRulesBeforeUpdate($data, $rules);
        // Validate data
        $this->validate($data, $rules);
        // Remove data that has not changed
        foreach ($data as $key => $value) {
            if ($resource->$key == $data[$key]) {
                unset($data[$key]);
            }
        }
        // Give a chance to classes that extends this abstract class
        // to modify $data, without to have to re-write the whole update()
        $data = $this->mutateDataBeforeUpdate($data);
        // Collect mass asignable attributes
        $fillable = [];
        if (count($resource->getFillable()) > 0) {
            $fillable = array_intersect_key($data, array_flip($resource->getFillable()));
        }
        // Set protected attributes
        if (count($data) > count($fillable)) {
            $notFillable = array_diff($data, $fillable);
            foreach ($notFillable as $key => $value) {
                $resource->$key = $value;
            }
        }
        return $resource->update($fillable);
    }

    /**
     * Delete Resource from database.
     *
     * @param  integer $resourceId
     * @return bool
     */
    public function delete($resourceId)
    {
        $resourceClassName = $this->resourceClassName;
        if (Gate::denies('delete', [$resourceClassName, $resourceId])) {
            throw new AuthorizationException(trans('error.delete', ['resource' => trans(lcfirst($this->resourceName) . '.' . lcfirst($this->resourceName))]), 403);
        }
        $resource = app($resourceClassName)->findOrFail($resourceId);
        return $resource->delete();
    }
}
