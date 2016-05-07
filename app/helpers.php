<?php

if (! function_exists('getStandaloneValidationRules')) {
    /**
     * Strip validation rules from any dependencie for given field(s)
     *
     * @param  array|string     $rules
     * @param  array|string     $fields
     * @return array|string     $rules
     *
     */
    function getStandaloneValidationRules($rules, $fields = null)
    {
        //  Make sure $rules is an array
        $rulesIsString = is_string($rules);
        $rules = (array) $rules;

        //  Make sure $fields is an array
        $fields = is_null($fields) ? array_keys($rules) : $fields;
        $fields = is_string($fields) ? explode(',', $fields) : (array) $fields;

        foreach ($rules as $field => $rule)
        {
            if (in_array($field, $fields))
            {
                // Tested regexes for php on http://www.phpliveregex.com/
                $newRule = preg_replace('/(confirmed|(different|same|required\w+):[\w,]+)(\||(?!.))/i', '', $rules[$field]);
                // Strip trailing '|' if present
                $rules[$field] = preg_replace('/\|$/i', '', $newRule);
            } else {
                // Remove rules for fields who are NOT in $fields
                unset($rules[$field]);
            }
            
        }

        // For convienience to return string if $rules is string
        return $rulesIsString ? array_pop($rules) : $rules;
    }
}

if (! function_exists('addExceptionsToUniqueRules')) {
    /**
     * Strip validation rules from any dependencies for given field(s)
     *
     * @param  array|string     $rules
     * @return array|string     $exceptions
     * @return array|string     $rules
     *
     */
    function addExceptionsToUniqueRules($rules, $exceptions)
    {
        //  Make sure $rules is an array
        $rulesIsString = is_string($rules);
        $rules = (array) $rules;

        //  Make sure $exceptions is an array
        $exceptions = (array) $exceptions;
        if(count(array_diff_key($rules, $exceptions)) > 0)
        {
            if(isset($exceptions[0]))
            {
                // Use exception for all rules
                $exception = array_pop($exceptions);
                foreach ($rules as $field => $rule)
                {
                    $exceptions[$field] = $exception;
                }
            }
            else
            {
                throw new Exception('$rules and $exception must have same keys', 1);
            }
        }

        foreach ($rules as $field => $rule)
        {
            // Tested regexes for php on http://www.phpliveregex.com/
            // Check for leading ','. Add one if needed.
            $exception = preg_replace('/^\w+/i', ',$0', $exceptions[$field]);
            // Extract unique rule as is
            preg_match('/unique:\w+(,\w+){0,3}/i', $rule, $arr);
            $original = array_shift($arr);
            // Strip id and id_column if present
            $unique = preg_replace('/(unique:\w+(,\w+)?)(,\w+){0,2}/i', '$1', $original);
            // Check if column is present or add $field as column
            $unique = preg_replace('/(unique:\w+)(?!,\w+)$/i', '$1,' . $field, $unique);
            if(preg_match('/\d+$/i', $unique)) throw new Exception('Supplied $rules and $fields are strings. Cannot deduct column name without array keys.', 1);
            
            // Add $exception at end of unique rule
            $rules[$field] = str_replace($original, $unique . $exception, $rule);
        }

        // For convienience to return string if $rules is string
        return $rulesIsString ? array_pop($rules) : $rules;
    }
}

if (! function_exists('removeUniqueRules')) {
    /**
     * Strip validation rules from any unique rule
     *
     * @param  array|string     $rules
     * @return array|string     $rules
     *
     */
    function removeUniqueRules($rules)
    {
        //  Make sure $rules is an array
        $rulesIsString = is_string($rules);
        $rules = (array) $rules;

        foreach ($rules as $field => $rule)
        {
            // Tested regexes for php on http://www.phpliveregex.com/
            $newRule = preg_replace('/(unique:[\w,]+)(\||(?!.))/i', '', $rules[$field]);
            // Strip trailing '|' if present
            $rules[$field] = preg_replace('/\|$/i', '', $newRule);
        }

        // For convienience to return string if $rules is string
        return $rulesIsString ? array_pop($rules) : $rules;
    }
}