<?php

namespace Microffice\Core\Support\Traits;

/**
 * Provides Illuminate\Support\Collection@getArrayableItems().
 *
 * The Laravel framework is open-sourced software
 * licensed under the [MIT license](http://opensource.org/licenses/MIT).
 *
 */

trait GetArrayableItems
{
    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof Arrayable) {
            return $items->toArray();
        } elseif ($items instanceof Jsonable) {
            return json_decode($items->toJson(), true);
        } elseif ($items instanceof JsonSerializable) {
            return $items->jsonSerialize();
        }

        return (array) $items;
    }
}
