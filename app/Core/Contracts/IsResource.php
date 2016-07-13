<?php namespace Microffice\Contracts\Core;

/**
* Resources that CANNOT be altered from the UI.
* 
* These Resources are fully mangable from the App using
* index(), store(), show(), update() and destroy()
* @return Response
*/
interface IsResource {
    
    /**
    * Return a listing of the resource.
    *
    * @return \Illuminate\Database\Eloquent\Collection
    */
    public function index();

    /**
    * Store a newly created resource in storage.
    *
    * @param array $data
    * @return \Illuminate\Database\Eloquent\Model
    */
    public function store($data);

    /**
    * Return the specified resource.
    *
    * @param int $id
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function show($id);

    /**
    * Update the specified resource in storage.
    *
    * @param int $id
    * @param array $data
    * @return \Illuminate\Database\Eloquent\Model
    */
    public function update($id, $data);

    /**
    * Remove the specified resource from storage.
    *
    * @param int $id
    * @return int (can be checked as boolean)
    */
    public function destroy($id);
    
}