<?php

namespace Microffice\Tests\Stubs;

use Microffice\Traits\UpdateRules;

/*use Jenssegers\Mongodb\Eloquent\Model;/*/
use Illuminate\Database\Eloquent\Model;/**/

class BaseEloquentStub extends Model
{
    use UpdateRules;

    /**
     * Set mutator to serialize the mass_assignable value.
     *
     * @return void
     */
    protected function setMassAssignableAttribute($value)
    {
        $this->attributes['mass_assignable'] = $value . '-' . $value;
    }

    /**
     * Set mutator to serialize the mass_assignable value.
     *
     * @return void
     */
    protected function setProtectedAttributeAttribute($value)
    {
        $this->attributes['protected_attribute'] = studly_case($value);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mass_assignable',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'hidden_from_json',
    ];

    /**
     * Validation Rules
     * @var array
     */
    public static $rules = array();
}


