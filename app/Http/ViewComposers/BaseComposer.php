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
     * Bind :before, :after and :compose event data to any view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Store parent view name
        session(['view.parent' => 
            ($this->events->hasListeners($view->getName().':parent')) ? array_filter($this->events->fire($view->getName().':parent'))[0] : false]);

        // Store view tree
        if(! session('view.parent'))
        {
            session(['view.tree' => '']);
        }
        else
        {
            if(strpos(session('view.tree'), session('view.parent')) !== false)
            {
                session(['view.tree' => substr(session('view.tree'), 0, strpos(session('view.tree'), session('view.parent')) + strlen(session('view.parent')))]);
            }
        }
        $tree = array_filter(explode('/', session('view.tree')));
        array_push($tree, $view->getName());
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
        if(! empty($ret = $this->fireCompose($view)))
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
            $this->registerListeners($ret['before'], $view->getName());
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
            $this->registerListeners($ret['after'], $view->getName());
        }
        return $ret;
    }

    /**
     * Fire :compose event on view.
     *
     * @param  View  $view
     * @return void
     */
    public function fireCompose(View $view)
    {
        $ret = [];
        foreach(array_filter($this->events->fire($view->getName().':compose', [$view])) as $array)
        {
            foreach ($array as $key => $value)
            {
                $value = (array) $value;
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
                    $this->registerListeners($value, $view->getName());
                }
                foreach ($value as $val)
                {
                    $ret[$key][] = $val;
                }
            }
        }
        return $ret;
    }

    /**
     * Register parent: listeners
     *
     * @param  array  $views
     * @return void
     */
    public function registerListeners($views, $parentName)
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