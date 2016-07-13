<?php

namespace Microffice\AccessControl;

use Microffice\Traits\UpdateRules;

/*use Jenssegers\Mongodb\Eloquent\Model;/*/
use Illuminate\Database\Eloquent\Model;/**/
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

class Acl extends Model
{
    use UpdateRules, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acls';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The user that belong to this AccessControlList.
     */
    public function user()
    {
        return $this->belongsTo('Microffice\User');
    }

    /**
     * The entries that belong to this AccessControlList.
     */
    public function aces()
    {
        return $this->hasMany('Microffice\AccessControl\Ace');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

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
        'user_id' => 'required|unique:acls,user_id'
    );
}
