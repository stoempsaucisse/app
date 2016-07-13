<?php

if (! function_exists('removeValidationRule')) {
    /**
     * Strip validation rules from any unique rule
     *
     * @param  array|string     $rules
     * @return array|string     $rules
     *
     */
    function removeValidationRule($rules, $badRuleName)
    {
        //  Make sure $rules is an array
        $rulesArr = (array) $rules;

        foreach ($rulesArr as $field => $rule) {
            // Tested regexes for php on http://www.phpliveregex.com/
            $newRule = preg_replace("/($badRuleName(:[\w,]+)?(\||(?!.)))/i", '', $rulesArr[$field]);
            // $newRule = preg_replace("/(unique:[\w,]+)(\||(?!.))/i", '', $rulesArr[$field]);
            // Strip trailing '|' if present
            $rulesArr[$field] = preg_replace('/\|$/i', '', $newRule);
        }

        // For convienience to return string if $rules is string
        return is_string($rules) ? array_pop($rulesArr) : $rulesArr;
    }
}

if (! function_exists('getStandaloneValidationRules')) {
    /**
     * Return rules for given fields striped from any
     * rule that depends on any other field
     *
     * @param  array|string     $rules
     * @param  array|string     $fields
     * @return array|string     $rules
     *
     */
    function getStandaloneValidationRules($rules, $fields = null)
    {
        // Rules with always depend on anotherfield
        // confirmed depends on a field under naming convention
        // therefor it is not present in $rulesWithDep array
        // and is statically present in regex
        $rulesWithDep = [
            'different',
            'in_array',
            'required_\w+',
            'same'
        ];

        // Rules that sometimes depend on anotherfield
        $rulesSometimesDep = [
            'after',
            'before'
        ];

        //  Make sure $rules is an array
        $rulesArr = (array) $rules;

        //  Make sure $fields is an array
        $fieldsArr = $fields;
        switch (true) {
            case is_null($fields):
                $fieldsArr = array_keys($rulesArr);
                break;
            case is_string($fields):
                $fieldsArr = explode(',', $fields);
                break;
            case is_array($fields):
                break;
            
            default:
                throw new Exception('$fields should be a string or an array', 1);
                
                break;
        }

        // Check if after: and before: depend on a fieldname listed in $fields
        // If so, add them too $rulesWithDep
        if (!is_null($fields)) {
            foreach ($rulesSometimesDep as $badRule) {
                $counter = 0;
                foreach ($rulesArr as $rule) {
                    if (preg_match('/' . $badRule . ':(\w+)/i', $rule, $matches) && in_array($matches[1], $fieldsArr)) {
                        ++$counter;
                    }
                }
                if ($counter) {
                    array_push($rulesWithDep, $badRule);
                }
            }
        }

        // Create the regex
        // Tested regexes for php on http://www.phpliveregex.com/
        $regex = '/(confirmed|(' . implode('|', $rulesWithDep) . '):[\w,]+)(\||(?!.))/i';

        // Modify given rules
        foreach ($rulesArr as $field => $rule) {
            if (in_array($field, $fieldsArr)) {
                // Test against regex
                $newRule = preg_replace($regex, '', $rulesArr[$field]);
                // Strip trailing '|' if present
                $rulesArr[$field] = preg_replace('/\|$/i', '', $newRule);
            } else {
                // Remove rules for fields who are NOT in $fields
                unset($rulesArr[$field]);
            }
        }

        // For convienience return string if given $rules is string
        return is_string($rules) ? array_pop($rulesArr) : $rulesArr;
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
        return removeValidationRule($rules, 'unique');
    }
}
