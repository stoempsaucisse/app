<?php

namespace Microffice\AccessControl;

use Microffice\AccessControl\Contracts\DecisionMaker as DecisionMakerContract;
use Microffice\AccessControl\Contracts\ObjectIdentityFactory as ObjectIdentityFactoryContract;
use Microffice\AccessControl\Support\MaskDataCastingStrategy;
use Microffice\Core\Support\ImplodeDataCastingStrategy;
use Microffice\Core\Support\UniqueItemsCollection;
use Microffice\User;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
// use Illuminate\Database\Eloquent\Collection as EloquentCollection;
// use Illuminate\Support\Collection;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;
use InvalidArgumentException;

class DecisionMaker implements DecisionMakerContract
{
    const KEY_SEPARATOR = '::';

    /**
     * Decision Maker gather available Object Identities event name
     * @var array
     */
    public static $gatherAllOIEventName = 'AccessControl.OI.all';

    /**
     * Decision Maker gather available masks event name
     * @var array
     */
    public static $gatherMaskEventName = 'AccessControl.mask.all';

    /**
     * The user
     *
     * @var Microffice\User
     */
    protected $user;

    /**
     * The User's acl
     *
     * @var Microffice\AccessControl\Acl
     */
    protected $acl;

    /**
     * The mask builder
     *
     * @var Symfony\Component\Security\Acl\Permission\MaskBuilderInterface
     */
    protected $maskBuilder;

    /**
     * The events dispatcher
     *
     * @var Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The available Object Identities
     *
     * @var array
     */
    protected $availableOI;

    /**
     * The available masks
     *
     * @var array
     */
    protected $availableMasks;

    /**
     * The Object Identity Factory.
     *
     * @var array
     */
    protected $objectIdentityFactory;

    /**
     * The compiled access list for each object / mask combination.
     *
     * @var Microffice\Core\Support\UniqueItemsCollection
     */
    protected $compiled;

    /**
     * The list of all object / mask combination where ability
     * is set for all objects.
     *
     * @var Microffice\Core\Support\UniqueItemsCollection
     */
    protected $guardAll;

    /**
     * Constructor.
     *
     * @param Microffice\User
     * @param Symfony\Component\Security\Acl\Permission\MaskBuilderInterface
     */
    public function __construct(UserContract $user, MaskBuilderContract $maskBuilder, DispatcherContract $events/*, ObjectIdentityFactoryContract $objectIdentityFactory/**/)
    {
    
        $this->user = $user;
        $this->maskBuilder = $maskBuilder;
        $this->events = $events;
        // $this->objectIdentityFactory = $objectIdentityFactory;
        $this->compiled = new UniqueItemsCollection(
            [],
            new ImplodeDataCastingStrategy(app(ObjectIdentityFactoryContract::class)->getKeyNames()),
            new MaskDataCastingStrategy(['mask'])
        );
        $this->availableOI = $this->events->fire(static::$gatherAllOIEventName);
        $this->compileAvailableMasks();
        /*$this->compiled = new EloquentCollection;
        $this->guardAll = new EloquentCollection;/**/
    }

    /**
     * Resolve available mask
     *
     * @param void
     * @return void
     */
    public function compileAvailableMasks()
    {
        foreach ($this->events->fire(static::$gatherMaskEventName) as $maskCode) {
            while (list($mask, $code) = each($maskCode)) {
                $this->availableMasks[$mask] = $code;
            }
        }
    }/**/

    /**
     * Checks if is granted ability for given object identity
     *
     * @param string    $ability
     * @param array     $arguments
     * @return bool
     */
    public function grants($ability, array $arguments)
    {
        $objectIdentity = $arguments[0];
        // Check this Object Identity exists in Microffice app
        if (! $this->objectIdentityExists($objectIdentity)) {
            return false;
        }



        return true;

        // Compile array_get() dot notation ($query)
        $field = isset($arguments[2]) ? $arguments[2] : null;
        $this->compilePartial($objectIdentity);
        $query = implode('.', $arguments);

        // Get guardAll key
        $mask = $this->resolveMask($ability);
        $key = $this->getKey($mask, $objectIdentity, $field);
        // Check if Object Identity is guarded for all
        // If true adapt $query to use 'all' instead of id
        // If false and no id was provided, return false
        if ($this->guardAll[$key]) {
            $query = $objectIdentity . '.all';
        } elseif (count($arguments) < 2) {
            return false;
        }
        // Grab user permission for given $arguments
        $permission = array_get($this->compiled, $query)->get();
        // Check if permission grants $ability
        return (bool) ($permission & $mask);
    }

    /**
     * Checks if is granted ability for given object identity
     *
     * @param string    $ability
     * @param array     $arguments
     * @return bool
     */
    public function denies($ability, $arguments)
    {
        return ! $this->isGranted($ability, $arguments);
    }

    /**
     * Check if given object identity exists.
     *
     * @param string $objectIdentity
     * @return bool
     */
    public function objectIdentityExists($objectIdentity)
    {
        return in_array($objectIdentity, $this->availableOI);
    }

    /**
     * Accept given Object Identity or throw exception.
     *
     * @param string $objectIdentity
     * @return bool|InvalidArgumentException
     */
    public function acceptObjectIdentity($objectIdentity)
    {
        if (! $this->objectIdentityExists($objectIdentity)) {
            throw new InvalidArgumentException("[$objectIdentity] is not a supported object identity.");
        }
    }

    /**
     * Resolve the given mask.
     *
     * @param string|int $mask
     * @return int
     */
    public function resolveMask($mask)
    {
        return $this->maskBuilder->resolveMask($mask);
    }

    /**
     * Get the name of given mask
     *
     * @param int|string    $mask
     * @return string       $maskName
     */
    public function getMaskName($mask)
    {
        $mask = $this->resolveMask($mask);

        $reflection = new \ReflectionClass(get_class($this->maskBuilder));
        foreach ($reflection->getConstants() as $name => $cMask) {
            if (0 !== strpos($name, 'MASK_') || $mask !== $cMask) {
                continue;
            }
            return substr($name, 5);
        }

        throw new InvalidArgumentException(sprintf('The mask "%d" is not supported.', $mask));
    }

    /**
     * Compile Acl portion from Aces
     *
     * @param string    $objectIdentity
     * @param bool      $force
     * @return void
     */
    public function compilePartial($objectIdentity, $force = false)
    {
        if (! $this->isCompiled($objectIdentity) || $force) {
            $this->acl->load(['aces' => function ($query) use ($objectIdentity) {
                $query->where('object', $objectIdentity);
            }]);
            if ($this->acl->aces->isEmpty()) {
                $this->compiled[$objectIdentity] = new EloquentCollection;
            } else {
                $this->performCompile($this->acl->aces()->get());
            }
        }
    }

    /**
     * Compile Acl from Aces
     *
     * @param bool      $force
     * @return void
     */
    public function compile($force = false)
    {
        if ($this->compiled->isEmpty() || $force) {
            foreach ($this->availableOI as $objectIdentity) {
                $this->compilePartial($objectIdentity, $force);
            }
        }
    }

    /**
     * Compile Acl from Aces
     *
     * @param string        $objectIdentity
     * @param string|void   $objectId
     * @param string|void   $field
     * @return bool
     */
    public function isCompiled($objectIdentity)
    {
        // $key = $this->compiled->resolveElementKey($objectIdentity, null);
        return $this->compiled->offsetExists($objectIdentity);
    }

    /**
     * Perfom Ace compilation and caching
     *
     * @param Illuminate\Database\Eloquent\Collection $aceGroup
     * @return void
     */
    protected function performCompile(EloquentCollection $aceGroup)
    {
        $this->compiled[$aceGroup->first()->object] = new EloquentCollection;
        $aceGroup->each(function ($ace) {
            while (list($mask, $code) = each($this->availableMasks)) {
                if ($ace->mask->get() & $code) {
                    $key = $this->getKey($code, $ace->object, $ace->field);
                    if (is_null($ace->object_id)) {
                        $this->guardAll[$key] = true;
                        $arrayKey = 'all';
                    } else {
                        $this->guardAll[$key] = isset($this->guardAll[$key]) ? $this->guardAll[$key] || false : false;
                        $arrayKey = $ace->object_id;
                    }
                    if (isset($this->compiled[$ace->object][$arrayKey])) {
                        $this->compiled[$ace->object][$arrayKey]->add($code);
                    } else {
                        $this->compiled[$ace->object][$arrayKey] = $ace->mask;
                    }
                }
            }
            // !!! Reset array pointer at begining
            reset($this->availableMasks);
        });
    }

    /**
     * Checks if ability is guarded for all objects.
     *
     * @param string        $ability
     * @param string        $objectIdentity
     * @param string|void   $objectField
     * @return bool
     */
    public function decidesForAll($ability, $objectIdentity, $objectField = null)
    {
        $this->compilePartial($objectIdentity);
        $key = $this->getKey($ability, $objectIdentity);
        return $this->guardAll[$key];
    }

    /**
     * Get the cache key.
     *
     * @param string        $ability
     * @param string        $objectIdentity
     * @param string|void   $objectField
     * @return string       $key
     */
    public function getKey($ability, $objectIdentity, $objectField = null)
    {
        try {
            $maskName = $this->getMaskName($ability);
        } catch (Exception $e) {
            $maskName = $this->resolveMask($ability);
        }
        return $objectIdentity . static::KEY_SEPARATOR . ((is_null($objectField)) ? '' : $objectField . static::KEY_SEPARATOR) . $maskName;
    }

    /**
     * Get allowed ids for given object / mask combination.
     *
     * @param string    $ability
     * @param string    $objectIdentity
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllowedIds($ability, $objectIdentity)
    {
        $this->compilePartial($objectIdentity);
        if ($this->compiled[$objectIdentity]->isEmpty()) {
            return new Collection;
        }
        $mask = $this->resolveMask($ability);
        $allowedIds = $this->compiled[$objectIdentity]->filter(function ($permission, $id) use ($mask) {
            return  ($id == 'all') ? false : (bool) ($permission->get() & $mask);
        });
        return $allowedIds->keys();
    }
}
