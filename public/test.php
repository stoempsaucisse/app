<?php
$flat = [];
$multi = [];
//$s = 123456;
$s = 's6tbdfgj222dJGk';
$rs = str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 10);
$numGen = function() {
    return rand(1, 9999999);
};
$strGen = function() {
    global $rs;
    return substr(str_shuffle($rs), 0, rand(10, 50));
};
// $gen = $numGen;
// $gen = $strGen;
$array_set = function(&$array, $key, $value)
{
    if (is_null($key)) {
        return $array = $value;
    }

    $keys = explode('.', $key);

    while (count($keys) > 1) {
        $key = array_shift($keys);

        // If the key doesn't exist at this depth, we will just create an empty array
        // to hold the next value, allowing us to create the arrays to hold final
        // values at the correct depth. Then we'll keep digging into the array.
        if (! isset($array[$key]) || ! is_array($array[$key])) {
            $array[$key] = [];
        }

        $array = &$array[$key];
    }

    $array[array_shift($keys)] = $value;

    return $array;
};

$array_get = function($array, $key, $default = null)
{
    if (is_null($key)) {
        return $array;
    }

    if (array_key_exists($key, $array)) {
        return $array[$key];
    }
    foreach (explode('.', $key) as $segment) {
        if (array_key_exists($array, $segment)) {
            $array = $array[$segment];
        } else {
            return $default;
        }
    }

    return $array;
};

$storedPath = false;

foreach ([10000, 1000, 100, 10] as $c) {
    echo "N={$c}\n";
    for ($i = 0; $i < $c; $i++) {
        $path = $strGen() . ((rand(0, 5) === 0) ? '' : '.' . $numGen()) . ((rand(0, 5) === 0) ? '' : '.' . $strGen());
        $value = $numGen();
        // if($i === 0) {$storedPath = $path;}
        // if($i === $c / 2) {$storedPath = $path;}
        // if($i === $c - 1) {$storedPath = $path;}
        $flat[$path] = $value;
        $array_set($multi, $path, $value);
    }
    // var_dump($multi, $flat);

    $path = ($storedPath) ? $storedPath : $strGen() . ((rand(0, 5) === 0) ? '' : '.' . $numGen()) . ((rand(0, 5) === 0) ? '' : '.' . $strGen());
    $t = microtime(1);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    $e = $array_get($multi, $path);
    echo "array_get:       ", microtime(1) - $t, PHP_EOL;
    $t = microtime(1);
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    $e = @$flat[$path];
    echo "array[path]:     ", microtime(1) - $t, PHP_EOL;
}