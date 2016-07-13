<?php namespace Microffice\Contracts\Core;

interface IsViewableResource extends IsResource {
    
    /**
    * Display a form to create a new Resource.
    *
    * @return Illuminate\View
    */
    public function create();
    
    /**
    * Display a form to edit an existing Resource.
    *
    * @param int $id
    * @return Illuminate\View
    */
    public function edit($id);
    
}