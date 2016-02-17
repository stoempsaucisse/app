<?php

namespace Microffice\Http\ViewComposers;

use Illuminate\View\View;

class UserComposer
{
    /**
     * Bind data to the user.avatar view.
     *
     * @param  View  $view
     * @return void
     */
    public function avatar(View $view)
    {
        $view->with('data', 'avatar');
    }
}