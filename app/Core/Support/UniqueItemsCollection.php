<?php

namespace Microffice\Core\Support;

/**
 *
 * This class is used to create a collection without any duplicates.
 *
 * Any added element resolves to a key and value(s) pair by use of
 * appropriate Data Casting Strategies.
 *
 * To pass the original Illuminate\Support\Collection test this class has
 * been implemented to push single value elements (without creation of an
 * appropriate key) on the $items array. This NOT the expected behavior and
 * this will be modified in the future.
 *
 * Expected behaviour (when no key is provided or isn't casted) could/should be :
 *      - a single item collection with numeric key that just combine the values
 *        (with appropriate Data Casting Strategy)
 *      - a multi item collection with numeric keys where every value is unique
 *        (even when value is an array or object)
 *
 * !!! THIS HAS STILL TO BE IMPLEMENTED !!!
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Core\Contracts\Support\DataCastingStrategy as DataCastingStrategyContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use BadMethodCallException;
use InvalidArgumentException;

class UniqueItemsCollection extends BaseCollection
{
    const KEY_SEGMENTS_SEPARATOR = '.';
    const FLAT_TYPES = 'string/integer/boolean';

    /**
     * The strategy to use when setting and getting a key.
     *
     * @var array
     */
    protected $keyCasting;

    /**
     * The strategy to use when setting and getting a value.
     *
     * @var array
     */
    protected $valueCasting;

    /**
     * Type of elements in the collection.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new collection.
     *
     * @param  mixed    $elements
     * @param  Microffice\Core\Contracts\Support\DataCastingStrategy    $keyCastingStrategy
     * @param  Microffice\Core\Contracts\Support\DataCastingStrategy    $valueCastingStrategy
     * @return void
     */
    public function __construct($elements = [], DataCastingStrategyContract $keyCastingStrategy = null, DataCastingStrategyContract $valueCastingStrategy = null)
    {
        $this->keyCasting = ($keyCastingStrategy === null) ? new BaseDataCastingStrategy(0x3) : $keyCastingStrategy;
        $this->valueCasting = ($valueCastingStrategy === null) ? new BaseDataCastingStrategy : $valueCastingStrategy;
        $this->addElements($elements);
    }

    /**
     * Reset collection items when cloning.
     *
     * @param  void
     * @return void
     */
    protected function __clone()
    {
        $this->items = [];
    }

    /**
     * Set the type of ellements used to build the collection.
     * This is either array | class_name
     *
     * @param  mixed    $element
     * @return void
     */
    protected function setType($element)
    {
        if (gettype($element) == 'object') {
            $this->type = get_class($element);
            return;
        }
        if (gettype($element) == 'array') {
            $this->type = 'array';
            return;
        }
        $this->type = self::FLAT_TYPES;
    }

    /**
     * Set Casting Strategy.
     *
     * @param  DataCastingStrategyContract    $strategy
     * @param  string                         $strategyName
     * @return void
     */
    public function setStrategy(DataCastingStrategyContract $strategy, $strategyName, $onBadTypeOfElement = SKIP_ON_INVALID_ARG)
    {
        if (! empty($this->items)) {
            if ((bool) $onBadTypeOfElement & THROW_ON_INVALID_ARG) {
                throw new BadMethodCallException("Strategies are immutable once the collection has been populated !", 1);
            }
            return false;
        }
        $name = (strpos($strategyName, 'Casting')) ? $strategyName : $strategyName . 'Casting';
        $this->$name = $strategy;
        return true;
    }

    /**
     * Check if given element is of the right type
     *
     * @param  mixed    $element
     * @return void
     */
    protected function checkTypeOfElement($element)
    {
        if (! isset($this->type)) {
            $this->setType($element);
        }
        $type = gettype($element);
        if ($type == 'object') {
            return $this->type == get_class($element);
        }
        if ($type == 'array') {
            return $this->type == 'array';
        }
        return ($type == 'integer' || $type == 'string' || $type == 'boolean');
        
    }

    /**
     * Take action when an element is not of the right type
     *
     * @param  mixed    $element
     * @param  string   $onBadTypeOfElement
     * @return void
     */
    protected function onBadTypeOfElement($element, $onBadTypeOfElement)
    {
        if ((bool) ($onBadTypeOfElement & THROW_ON_INVALID_ARG)) {
            $type = (gettype($element) == 'object') ? get_class($element) : gettype($element);
            throw new InvalidArgumentException("Elements should be of type [$this->type], [$type] given.", 1);
        }
        return false;
    }

    /**
     * Add some elements to the collection.
     *
     * @param  array    $elements
     * @param  string   $onBadTypeOfElement
     * @return void
     */
    public function addElements($elements, $onBadTypeOfElement = THROW_ON_INVALID_ARG)
    {
        $elements = $this->getArrayableItems($elements);
        foreach ($elements as $key => $element) {
            $key = $this->resolveElementKey($element, $key, $onBadTypeOfElement);
            $originalValue = ($this->offsetExists($key)) ? $this->offsetGet($key) : null;
            // dd($element, $originalValue, $this->valueCasting);
            $value = $this->valueCasting->cast($element, $originalValue);
            $this->offsetSet($key, $value);
        }
    }

    /**
     * Add one element to the collection.
     *
     * @param  array    $element
     * @param  string   $onBadTypeOfElement
     * @return void
     */
    public function addElement($element, $onBadTypeOfElement = THROW_ON_INVALID_ARG)
    {
        // $element = $this->getArrayableItems($element);
        $key = $this->resolveElementKey($element, null, $onBadTypeOfElement);
        $originalValue = ($this->offsetExists($key)) ? $this->offsetGet($key) : null;
        $value = $this->valueCasting->cast($element, $originalValue);
        $this->offsetSet($key, $value);
    }

    /**
     * Resolve the key for given element.
     *
     * @param  mixed    $element
     * @param  mixed    $key
     * @param  string   $onBadTypeOfElement
     * @return string
     */
    public function resolveElementKey($element, $key, $onBadTypeOfElement = THROW_ON_INVALID_ARG)
    {
        if (! $this->checkTypeOfElement($element)) {
            return $this->onBadTypeOfElement($element, $onBadTypeOfElement);
        }
        return $this->keyCasting->cast($element, $key);
    }

    /**
     * Add items to the collection.
     *
     * @param  array    $items
     * @return void
     */
    protected function rawAddElements($items)
    {
        foreach ($items as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * Add items to the collection.
     *
     * @param  array    $items
     * @return void
     */
    protected function rawAddElementsCallback($items, $callback)
    {
        foreach ($items as $key => $value) {
            $value = $this->valueCasting->unCast($value);
            if ($callback($value, $key)) {
                $this->offsetSet($key, $value);
            }
        }
    }

    /**
     * Repopulate with items
     *
     * @param  array    $items
     * @return MonoTypeCollection
     */
    protected function repopulate($items)
    {
        $this->items = [];
        $this->rawAddElements($items);

        return $this;
    }

    /**
     * Repopulate with items if callback returns true
     *
     * @param  array        $items
     * @param  callable    $callback
     * @return MonoTypeCollection
     */
    protected function repopulateCallback($items, $callback)
    {
        $this->items = [];
        $this->rawAddElementsCallback($items, $callback);
        
        return $this;
    }

    /**
     * Clone this collection
     *
     * @return MonoTypeCollection
     */
    protected function cloneThis()
    {
        $return = clone $this;

        $return->rawAddElements($this->items);
        return $return;
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @param  mixed  $items
     * @return static
     */
    public static function make($items = [])
    {
        throw new BadMethodCallException("static " . get_called_class() . "::make() is not supported due to inheritance limitations. Methods from a child class must depend on the same amount of arguments than the parent class method.");
    }

    /**
     * Create a new collection consisting of every n-th element.
     *
     * @param  int  $step
     * @param  int  $offset
     * @return static
     */
    public function every($step, $offset = 0)
    {
        $return = clone $this;

        $position = 0;

        foreach ($this->items as $key => $value) {
            if ($position % $step === $offset) {
                $key = $this->resolveElementKey($value, null);
                $return->offsetSet($key, $value);
            }

            $position++;
        }

        return $return;
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param  mixed  $keys
     * @return static
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $return = clone $this;

        $return->rawAddElements(Arr::except($this->items, $keys));
        return $return;
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function filter(callable $callback = null)
    {
        $return = clone $this;
        if ($callback) {
            $return->rawAddElementsCallback($this->items, $callback);
            return $return;
        }
        $return->rawAddElements(array_filter($this->items));
        return $return;
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  bool  $strict
     * @return static
     */
    // public function where($key, $value, $strict = true)
    // {
    //     $return = clone $this;
    //     if ($strict ? $this->get($key) === $value
    //                 : $this->get($key) == $value) {
    //         $return->offsetSet($key, $this->offsetGet($key));
    //     }

    //     return $return;
    // }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  array  $values
     * @param  bool  $strict
     * @return static
     */
    // public function whereIn($key, array $values, $strict = true)
    // {
    //     return $this->filter(function ($item, $itemKey) use ($key, $values, $strict) {
    //         return $itemKey == $key && in_array($item, $values, $strict);
    //     });
    // }

    /**
     * Get the first item from the collection.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public function first(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($this->items) ? value($default) : reset($this->items);
        }

        foreach ($this->items as $key => $value) {
            $value = $this->valueCasting->unCast($value);
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return value($default);
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param  int  $depth
     * @return static
     */
    public function flatten($depth = INF)
    {
        $return = clone $this;
        if ($depth === INF) {
            $return->rawAddElements(Arr::flatten($this->items, $depth));
            return $return;
        }
        $elements = [];
        foreach (Arr::flatten($this->items, $depth) as $element) {
            $elements[] = $this->getArrayableItems($element);
        }
        $return->rawAddElements($elements);
        return $return;
    }

    /**
     * Flip the items in the collection.
     *
     * @return static
     */
    public function flip()
    {
        $return = clone $this;

        $return->rawAddElements(array_flip($this->items));
        return $return;
    }

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return value($default);
    }

    /**
     * Group an associative array by a field or using a callback.
     *
     * When only a boolean is given as argument
     * it is considered for $preserveKeys. This triggers default
     * action = groupBy value
     *
     * @param  callable|bool  $groupBy
     * @param  bool  $preserveKeys
     * @return static
     */
    public function groupBy($groupBy, $preserveKeys = false)
    {
        $preserveKeys = (is_bool($groupBy)) ? $groupBy : $preserveKeys;
        $groupBy = $this->valueRetriever($groupBy);

        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKeys = $groupBy($value, $key);

            if (! is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }
            foreach ($groupKeys as $groupKey) {
                if (! array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = clone $this;
                }

                $results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
            }
        }

        return new BaseCollection($results);
    }

    /**
     * Key an associative array by a field or using a callback.
     *
     * If boolean is given as argument this triggers default
     * action = keyBy value. Return new collection with key == value
     *
     * @param  callable|string  $keyBy
     * @return BaseCollection
     */
    public function keyBy($keyBy)
    {
        $keyBy = $this->valueRetriever($keyBy);


        $results = [];

        foreach ($this->items as $key => $item) {
            $results[$keyBy($item, $key)] = $item;
        }

        return new BaseCollection($results);
    }

    /**
     * Concatenate values.
     *
     * @param  string  $glue
     * @return string
     */
    // public function implode($glue, $bool = null)
    // {
    //     $results = [];

    //     foreach ($this->items as $key => $value) {
    //         $value = $this->valueCasting->unCast($value);
    //         $results[$key] = $value;
    //     }

    //     return implode($glue, $results);
    // }

    /**
     * Intersect the collection with the given items.
     *
     * @param  mixed  $items
     * @return static
     */
    public function intersect($items)
    {
        $return = clone $this;

        $results = [];
        foreach ($this->getArrayableItems($items) as $key => $item) {
            $results[$this->resolveElementKey($item, $key)] = $this->valueCasting->cast($item);
        }

        $return->rawAddElements(array_intersect($this->items, $results));
        return $return;
    }

    /**
     * Get the keys of the collection items.
     *
     * @return static
     */
    public function keys()
    {
        return new BaseCollection(array_keys($this->items));
    }

    /**
     * Get the values of a given key.
     *
     * @param  string  $value
     * @param  string|null  $key
     * @return static
     */
    public function pluck($value, $key = null)
    {
        return new BaseCollection(Arr::pluck($this->items, $value, $key));
        return $this->offsetGet($value);
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback)
    {
        $keys = array_keys($this->items);

        $items = [];
        foreach ($this->items as $item) {
            $items[] = $this->valueCasting->unCast($item);
        }
        $items = array_map($callback, $items, $keys);

        $return = clone $this;
        $return->rawAddElements(array_combine($keys, $items));

        return $return;
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  mixed  $items
     * @return static
     */
    public function merge($items)
    {
        $return = clone $this;

        $results = [];
        foreach ($this->getArrayableItems($items) as $key => $item) {
            $results[$this->resolveElementKey($item, $key)] = $this->valueCasting->cast($item);
        }

        $return->rawAddElements(array_merge($this->items, $results));
        return $return;
    }

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @param  mixed  $values
     * @return static
     */
    public function combine($values)
    {
        $return = clone $this;

        $return->rawAddElements(array_combine($this->all(), $this->getArrayableItems($values)));
        return $return;
    }

    /**
     * Union the collection with the given items.
     *
     * @param  mixed  $items
     * @return static
     */
    public function union($items)
    {
        $return = clone $this;

        $results = [];
        foreach ($this->getArrayableItems($items) as $key => $item) {
            $results[$this->resolveElementKey($item, $key)] = $this->valueCasting->cast($item);
        }

        $return->rawAddElements($this->items + $results);
        return $return;
    }

    /**
     * Get the items with the specified keys.
     *
     * @param  mixed  $keys
     * @return static
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $return = clone $this;

        $return->rawAddElements(Arr::only($this->items, $keys));
        return $return;
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param  mixed  $value
     * @param  mixed  $key
     * @return $this
     */
    public function prepend($value, $key = null)
    {
        if ($this->checkTypeOfElement($value)) {
            if ($this->type != self::FLAT_TYPES) {
                $value = $this->getArrayableItems($value);
            }
            $key = $this->resolveElementKey($value, $key);
            $value = $this->valueCasting->cast($value);
        }
        $this->items = Arr::prepend($this->items, $value, $key);

        return $this;
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param  mixed  $element
     * @return $this
     */
    public function push($element)
    {
        $this->addElement($element);

        return $this;
    }

    /**
     * Get one or more items randomly from the collection.
     *
     * @param  int  $amount
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function random($amount = 1)
    {
        if ($amount > ($count = $this->count())) {
            throw new InvalidArgumentException("You requested {$amount} items, but there are only {$count} items in the collection");
        }

        $keys = array_rand($this->items, $amount);

        if ($amount == 1) {
            return $this->items[$keys];
        }
        $return = clone $this;

        $return->rawAddElements(array_intersect_key($this->items, array_flip($keys)));
        return $return;
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse()
    {
        $return = clone $this;

        $return->rawAddElements(array_reverse($this->items, true));
        return $return;
    }

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param  mixed  $value
     * @param  bool   $strict
     * @return mixed
     */
    public function search($value, $strict = false)
    {
        if (! $this->useAsCallable($value)) {
            $value = $this->valueCasting->cast($value);
            return array_search($value, $this->items, $strict);
        }

        foreach ($this->items as $key => $item) {
            $item = $this->valueCasting->unCast($item);
            if (call_user_func($value, $item, $key)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Shuffle the items in the collection.
     *
     * @return static
     */
    public function shuffle()
    {
        $return = clone $this;

        $keys = array_keys($this->items);
        shuffle($keys);
        foreach ($keys as $key) {
            $return->offsetSet($key, $this->items[$key]);
        }

        return $return;
    }

    /**
     * Slice the underlying collection array.
     *
     * @param  int   $offset
     * @param  int   $length
     * @return static
     */
    public function slice($offset, $length = null)
    {
        $return = clone $this;

        $return->rawAddElements(array_slice($this->items, $offset, $length, true));
        return $return;
    }

    /**
     * Chunk the underlying collection array.
     *
     * @param  int   $size
     * @return static
     */
    public function chunk($size)
    {
        $chunks = [];

        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $return = clone $this;
            $return->rawAddElements($chunk);
            $chunks[] = $return;
        }

        return new BaseCollection($chunks);
    }

    /**
     * Sort through each item with a callback.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function sort(callable $callback = null)
    {
        $items = $this->items;

        $callback ? uasort($items, $callback) : uasort($items, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        });
        
        $return = clone $this;
        $return->rawAddElements($items);

        return $return;
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param  callable|string  $callback
     * @param  int   $options
     * @param  bool  $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        $results = [];

        $callback = $this->valueRetriever($callback);

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // and grab the corresponding values for the sorted keys from this array.
        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }

        $descending ? arsort($results, $options)
                    : asort($results, $options);

        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }
        
        $return = clone $this;
        $return->rawAddElements($results);

        return $return;
    }

    /**
     * Splice a portion of the underlying collection array.
     *
     * @param  int  $offset
     * @param  int|null  $length
     * @param  mixed  $replacement
     * @return static
     */
    public function splice($offset, $length = null, $replacement = [])
    {
        $return = clone $this;
        if (func_num_args() == 1) {
            $return->rawAddElements(array_splice($this->items, $offset));
        } else {
            $return->rawAddElements(array_splice($this->items, $offset, $length, $replacement));
        }

        return $return;
    }

    /**
     * Get the sum of the given values.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function sum($callback = null)
    {
        if (is_null($callback)) {
            $values = [];
            foreach ($this->items as $value) {
                $values[] = $this->valueCasting->unCast($value);
            }
            return array_sum($values);
        }

        $callback = $this->valueRetriever($callback);

        return $this->reduce(function ($result, $item) use ($callback) {
            return $result += $callback($item, $this->search($item));
        }, 0);
    }

    /**
     * Return only unique items from the collection array.
     *
     * @param  string|callable|null  $key
     * @return static
     */
    public function unique($key = null)
    {
        if (is_null($key)) {
            return new BaseCollection(array_unique($this->items, SORT_REGULAR));
        }

        $key = $this->valueRetriever($key);

        $exists = [];

        return $this->reject(function ($item) use ($key, &$exists) {
            if (in_array($id = $key($item), $exists)) {
                return true;
            }

            $exists[] = $id;
        });
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static
     */
    public function values()
    {
        return new BaseCollection(array_values($this->items));
    }

    /**
     * Get a value retrieving callback.
     *
     * @param  string  $value
     * @return callable
     */
    // protected function valueRetriever($value)
    // {
    //     if ($this->useAsCallable($value)) {
    //         return $value;
    //     }

    //     return function ($item, $key) use ($value) {
    //         return $this->offsetGet($key);
    //     };
    // }

    /**
     * Zip the collection together with one or more arrays.
     *
     * e.g. new Collection([1, 2, 3])->zip([4, 5, 6]);
     *      => [[1, 4], [2, 5], [3, 6]]
     *
     * @param  mixed ...$items
     * @return BaseCollection
     */
    public function zip($items)
    {
        $arrayableItems = array_map(function ($items) {
            return $this->getArrayableItems($items);
        }, func_get_args());
        // func_get_args() catches all args frim zip() => possible to give more than one array of items

        $params = array_merge([function () {
            // Here func_get_args() catches values from each array in $arrayableItems
            return new BaseCollection(func_get_args());
        }, $this->items], $arrayableItems);

        return new BaseCollection(call_user_func_array('array_map', $params));
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        if ($this->checkTypeOfElement($key)) {
            $key = $this->resolveElementKey($key, null);
        }
        return array_key_exists($key, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->valueCasting->unCast($this->items[$key]);
    }/**/

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $originalValue = ($this->offsetExists($key)) ? $this->offsetGet($key) : null;
        $value = $this->valueCasting->cast($value, $originalValue);
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }/**/
}
