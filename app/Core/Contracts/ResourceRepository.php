<?php namespace Microffice\Core\Contracts;

/**
 * Resource Repository Interface
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
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
    * Validate the specified Resource data.
    *
    * @param array $data
    * @param array $rules
    * @return Bool
    */
    public function validate($data, $rules = null);

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
     * Delete resource from database.
     *
     * @param  integer $resourceId
     * @return bool
     */
    public function delete($resourceId);
}
