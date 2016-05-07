<?php

namespace Microffice\Http\ViewComposers;

use ReflectionClass;
use Illuminate\View\View;
use Illuminate\Contracts\Events\Dispatcher;

class BaseComposer
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
     * Bind :before, :after and :composite event data to any view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Store parent view name
        session(['view.parent' => 
            ($this->events->hasListeners($view->getName().':parent')) ? array_filter($this->events->fire($view->getName().':parent'))[0] : false]);

        // Reset view.tree if view.parent is root view
        if(! session('view.parent'))
        {
            session(['view.tree' => '']);
        }
        else
        {
            // Trim all descendant of view.parent from view.tree
            // This way we "go up" in the tree
            $viewTree = session('view.tree');
            $viewParent = session('view.parent');
            if(strpos($viewTree, $viewParent) !== false)
            {
                session(['view.tree' => substr($viewTree, 0, strpos($viewTree, $viewParent) + strlen($viewParent))]);
            }
        }
        // Load last views tree from session and convert to array
        $tree = array_filter(explode('/', session('view.tree')));
        // Append current view name
        array_push($tree, $view->getName());
        // Save new views tree to session and convert to string
        // Format : view.name/otherview.name/...
        session(['view.tree' => implode('/', $tree)]);

        // Unset from previous view.
        $view->__unset('before');
        $view->__unset('after');
        // Data placeholder
        $data = [];
        if(! empty($before = $this->fireBefore($view)))
        {
            $data = array_merge_recursive($data, $before);
        }
        if(! empty($after = $this->fireAfter($view)))
        {
            $data = array_merge_recursive($data, $after);
        }
        if(! empty($ret = $this->fireData($view)))
        {
            $data = array_merge_recursive($data, $ret);
        }
        if(! empty($ret = $this->fireComposite($view)))
        {
            $data = array_merge_recursive($data, $ret);
        }
        foreach ($data as $key => $value)
        {
            $view->with($key, array_filter((array) $value));
        }
    }

    /**
     * Fire :before event on view.
     *
     * @param  View  $view
     * @return void
     */
    public function fireBefore(View $view)
    {
        $ret = [];
        foreach(array_filter($this->events->fire($view->getName().':before', [$view])) as $value)
        {
            $ret['before'] = (array) $value;
            // Unset :before views who are ancestors of this view
            foreach($ret['before'] as $key => $value)
            {
                if(strpos(session('view.tree'), $value) !== false)
                {
                    unset($ret['before'][$key]);
                }
            }
            $this->registerParentName($ret['before'], $view->getName());
        }
        return $ret;
    }

    /**
     * Fire :after event on view.
     *
     * @param  View  $view
     * @return void
     */
    public function fireAfter(View $view)
    {
        $ret = [];
        foreach(array_filter($this->events->fire($view->getName().':after', [$view])) as $value)
        {
            $ret['after'] = (array) $value;
            // Unset :after views who are ancestors of this view
            foreach($ret['after'] as $key => $value)
            {
                if(strpos(session('view.tree'), $value) !== false)
                {
                    unset($ret['after'][$key]);
                }
            }
            $this->registerParentName($ret['after'], $view->getName());
        }
        return $ret;
    }

    /**
     * Fire :data event on view.
     *
     * @param  View  $view
     * @return void
     */
    public function fireData(View $view)
    {
        $ret = [];
        foreach(array_filter($this->events->fire($view->getName().':data', [$view])) as $array)
        {
            $ret = array_merge_recursive($array, $ret);
        }
        return $ret;
    }

    /**
     * Fire :composite event on view.
     *
     * @param  View  $view
     * @return void
     */
    public function fireComposite(View $view)
    {
        $ret = [];
        foreach(array_filter($this->events->fire($view->getName().':composite', [$view])) as $array)
        {
            foreach ($array as $key => $value)
            {
                $value = (array) $value;
                // Handle before and after keys
                if(($key == 'before') || ($key == 'after'))
                {
                    // Unset :before and :after views who are ancestors of this view
                    foreach($value as $kkey => $vvalue)
                    {
                        if(strpos(session('view.tree'), $vvalue) !== false)
                        {
                            unset($value[$kkey]);
                        }
                    }
                    $this->registerParentName($value, $view->getName());
                }
                // Copy data from other keys to $ret
                // which populate the $data view variable
                foreach ($value as $val)
                {
                    $ret[$key][] = $val;
                }
            }
        }
        return $ret;
    }

    /**
     * Register event listeners that return the parent view.name for given views
     *
     * @param  array  $views
     * @return void
     */
    public function registerParentName($views, $parentName)
    {
        foreach($views as $viewName)
        {
            if(! $this->events->hasListeners($viewName.':parent'))
            {
                $this->events->listen($viewName.':parent', function()use($parentName){
                    return $parentName;
                });
            }
        }
    }

}