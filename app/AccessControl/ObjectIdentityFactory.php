<?php

namespace Microffice\AccessControl;

use Microffice\AccessControl\Contracts\ObjectIdentityFactory as ObjectIdentityFactoryContract;
use Microffice\AccessControl\Contracts\ObjectIdentity as ObjectIdentityContract;
use InvalidArgumentException;

class ObjectIdentityFactory implements ObjectIdentityFactoryContract
{
    /**
     * The object name key for each Object Identity.
     *
     * @var string
     */
    protected $objectNameKey;
    /**
     * The key names for each Object Identity.
     *
     * @var array
     */
    protected $objectKeys;

    /**
     * Create a Object Identity Factory.
     *
     * @param  array    $objectKeys
     * @return void
     */
    public function __construct($objectNameKey, array $objectKeys)
    {
        $this->objectKeys = $objectKeys;
        $this->objectNameKey = $objectNameKey;
        // array_unshift($this->objectKeys, $objectNameKey);
    }

    /**
     * Make a new Object Identity
     *
     * @param  string       $name
     * @param  array|void   $details
     * @return ObjectIdentity
     */
    public function make($name)
    {
        $objectIdentity[$this->objectNameKey] = $name;
        if ($arguments = func_get_arg(1)) {
            if (count($errors = array_diff_key($arguments, array_flip($this->objectKeys))) > 0) {
                throw new InvalidArgumentException("Object Identities do NOT have [" . implode(', ', array_keys($errors)) ."] as object key(s).", 1);
            }
            foreach ($this->objectKeys as $key => $value) {
                $objectIdentity[$this->objectKeys[$key]] = (isset($arguments[$value])) ? $arguments[$value] : null;
            }
        }
        return app(ObjectIdentityContract::class, [$this, $objectIdentity]);
    }

    /**
     * Get key names
     *
     * @return array    $objectKeys
     */
    public function getKeyNames()
    {
        return array_merge((array) $this->objectNameKey, $this->objectKeys);
    }

    /**
     * Check key name exists
     *
     * @return bool
     */
    public function checkKeyName($name)
    {
        return in_array($name, $this->objectKeys);
    }
}
