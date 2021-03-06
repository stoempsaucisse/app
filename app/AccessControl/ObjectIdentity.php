<?php

namespace Microffice\AccessControl;

/**
 * Object Identity
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

use Microffice\AccessControl\Contracts\ObjectIdentity as ObjectIdentityContract;
use Microffice\AccessControl\Contracts\ObjectIdentityFactory as ObjectIdentityFactoryContract;
use InvalidArgumentException;

class ObjectIdentity implements ObjectIdentityContract
{
    /**
     * Object Identity.
     *
     * @var array
     */
    protected $objectIdentity;
    /**
     * Object Identity Factory.
     *
     * @var array
     */
    protected $factory;
    /**
     * Create an Object Identity.
     *
     * @param  ObjectIdentityFactoryContract    $objectIdentityFactory
     * @param  array    $objectKeys
     * @return void
     */
    public function __construct(ObjectIdentityFactoryContract $objectIdentityFactory, array $objectIdentity)
    {
        $this->factory = $objectIdentityFactory;
        $this->objectIdentity = $objectIdentity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->objectIdentity;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        if (! in_array($name, $this->factory->getKeyNames())) {
            throw new InvalidArgumentException("Object Identities do NOT have [$name] as object key.", 1);
        }
        $this->objectIdentity[$name] = $value;
    }
}
