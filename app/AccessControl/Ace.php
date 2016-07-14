<?php

namespace Microffice\AccessControl;

/**
 * Ace model
 *
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\Traits\UpdateRules;

/*use Jenssegers\Mongodb\Eloquent\Model;/*/
use Illuminate\Database\Eloquent\Model;/**/
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class Ace extends Model
{
    use UpdateRules, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aces';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get accessor to cast the mask to a MaskBuilder.
     *
     * @return Symfony\Component\Security\Acl\Permission\MaskBuilder
     */
    public function getMaskAttribute($value)
    {
        return new MaskBuilder($value);
    }

    /**
     * Set mutator to serialize the MaskBuilder mask value to int.
     *
     * @param int|string|MaskBuilder $mask
     * @return int
     */
    public function setMaskAttribute($mask)
    {
        if (! is_a($mask, MaskBuilder::class)) {
            $reflection = new \ReflectionClass(MaskBuilder::class);
            $constants = $reflection->getConstants();
            // Check if $mask is MASK_name
            switch (true) {
                // mask is int
                case is_int($mask):
                    $value = $mask;
                    break;
                // MASK_*
                case isset($constants['MASK_' . strtoupper($mask)]):
                    $value = $constants['MASK_' . strtoupper($mask)];
                    break;
                // CODE_*
                case $key = array_search(strtoupper($mask), $constants):
                    $value = $constants[str_replace('CODE_', 'MASK_', $key)];
                    break;
                // Else
                default:
                    throw new \InvalidArgumentException("The mask $mask is not supported.");
                    
                    break;
            }
            $this->attributes['mask'] = $value;
        } else {
            $this->attributes['mask'] = $mask->get();
        }
    }

    /**
     * The Access Control List that own this Access Control Entry.
     */
    public function acl()
    {
        return $this->belongsTo(Acl::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['acl_id', 'object', 'object_id', 'field', 'mask'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Validation Rules
     * @var array
     */
    public static $rules = array(
        'user_id' => 'unique:users'
    );
}
