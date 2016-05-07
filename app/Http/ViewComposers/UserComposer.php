<?php

namespace Microffice\Http\ViewComposers;

use Microffice\User;
use Illuminate\View\View;

class UserComposer
{
    /**
     * Bind data to the user.fieldset view.
     *
     * @param  View  $view
     * @return void
     */
    public function fieldset(View $view)
    {
        $rules = User::$rules;
        if($view->getData()['action'] == 'update')
        {
            unset($rules['password']);
        }
        $view->with('rules', $rules);
    }
}