<?php namespace Microffice\Core\Contracts;

/**
* Resources that CANNOT be altered from the UI.
*
* These Resources are fully mangable from the App using
* index(), store(), show(), update() and destroy()
* @return Response
*/
interface ResourceRepository
{
    
    /**
     * Get all Resources.
     *
     * @return Collection
     */
    public function findAll();
    
    /**
     * Get one Resource.
     *
     * @param  integer $resourceId
     * @return Resource instance | Exception
     */
    public function findById($resourceId);

    /**
     * Create and save a new resource to database.
     *
     * @param  array $data
     * @return Resource instance
     */
    public function saveNew($data);

    /**
     * Update existing resource.
     *
     * @param  integer $resourceId
     * @param  array validated $data
     * @return Resource instance
     */
    public function update($resourceId, $data);

    /**
     * Mutate validation rules before updating if needed.
     *
     * @param  array $data
     * @param  array $rules
     * @return array $rules
     */
    public function mutateRulesBeforeUpdate($data, $rules);

    /**
     * Mutate data before updating if needed.
     *
     * @param  array validated $data
     * @return array $data
     */
    public function mutateDataBeforeUpdate($data);

    /**
     * Delete resource from database.
     *
     * @param  integer $resourceId
     * @return bool
     */
    public function delete($resourceId);
}
