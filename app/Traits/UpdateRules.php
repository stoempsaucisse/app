<?php

namespace Microffice\Traits;

trait UpdateRules
{
    // This traits extends Models by adding a method that
    // modify the validations rules with "unique" restraint
    // to except given id


    /**
    * Validation rules for current Model
    * where unique constraints accept this Model's value
    * @param int|string $id
    */
    public function updateRules($id = null)
    {
        $id = ($id === null) ? $this->id : $id;
        $rules = self::$rules;
        foreach($rules as $field => $rule)
        {
            // Extract unique rule as is
            preg_match('/unique:\w+(,\w+){0,3}/i', $rule, $arr);
            if(! empty($arr))
            {
                $original = array_shift($arr);
                // Check if column is present or add $field as column
                $unique = preg_replace('/^(unique:\w+)(?!,\w+)$/i', '$1,' . $field , $original);
                // Add id and idColumn to rules
                $unique = preg_replace('/^unique:\w+,\w+$/i', '$0,' . $id . ',' . $this->getKeyName(), $unique);
                $rules[$field] = str_replace($original, $unique, $rule);
            }
        }
        return $rules;
    }
}
