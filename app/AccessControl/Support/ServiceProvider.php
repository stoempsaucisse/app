<?php

namespace Microffice\AccessControl\Support;

use Microffice\AccessControl\Ace;
use Microffice\AccessControl\Acl;
use Microffice\AccessControl\Contracts\DecisionMaker as DecisionMakerContract;
use Microffice\AccessControl\Contracts\ObjectIdentity as ObjectIdentityContract;
use Microffice\AccessControl\Contracts\ObjectIdentityFactory as ObjectIdentityFactoryContract;
use Microffice\AccessControl\DecisionMaker;
use Microffice\AccessControl\ObjectIdentity;
use Microffice\AccessControl\ObjectIdentityFactory;
use Microffice\AccessControl\Policies\CorePolicy;
use Microffice\AccessControl\Support\Traits\RegisterAclMasks as MasksTrait;
use Microffice\AccessControl\Support\Traits\RegisterAclObjectIdentities as ObjectIdentitiesTrait;
use Microffice\User;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class ServiceProvider extends BaseServiceProvider
{
    use MasksTrait, ObjectIdentitiesTrait;

    /**
     * The available object identities for the Access Control package.
     *
     * @var array
     */
    protected $objectIdentities = [
        Acl::class,
        Ace::class,
        User::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @param  \Symfony\Component\Security\Acl\Permission\MaskBuilderInterface  $maskBuilder
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(MaskBuilderContract $maskbuilder, GateContract $gate, DispatcherContract $events)
    {
        $this->registerCorePolicies($maskbuilder, $gate);
        $this->registerAclObjectIdentities($events);
        $reflection = new \ReflectionClass(MaskBuilder::class);
        $masks = collect($reflection->getConstants())->filter(function ($code, $mask) {
            return strpos($mask, 'MASK_') === 0;
        });
        $this->registerAclMasks($events, $masks->toArray());
        $this->registerAclEvents();

    }

    /**
     * Register the core policies.
     *
     * Here we define global core AccessControl policies
     * These use the Symphony security-acl MaskBuilder and AbstractMaskBuilder
     * classes and follow the package masks naming convention
     *
     * We run through all the MaskBuilder constants
     * Select only those who start with 'MASK_'
     * Define lowercase and uppercase policy entries for
     * both the mask name and mask code (aka. first letter)
     * This is fine since we overload all those methods in
     * the CorePolicy class
     *
     * @return void
     */
    protected function registerCorePolicies(MaskBuilderContract $maskbuilder, GateContract $gate)
    {
        $reflection = new \ReflectionClass(get_class($maskbuilder));
        $constants = $reflection->getConstants();
        foreach ($constants as $key => $value) {
            if (strpos($key, 'MASK_') === 0) {
                $mask = str_replace('MASK_', '', $key);
                $methodName = CorePolicy::class . '@' . $mask;
                $this->registerMask($gate, $mask, $methodName);
                $this->registerCode($gate, $mask, $methodName, $constants);
            }
        }
    }

    protected function registerMask(GateContract $gate, $mask, $methodName)
    {
        $gate->define(strtolower($mask), $methodName);
        $gate->define(strtoupper($mask), $methodName);
    }

    protected function registerCode(GateContract $gate, $mask, $methodName, $constants)
    {
        if (isset($constants['CODE_' . $mask])) {
            $code = $constants['CODE_' . $mask];
            $gate->define(strtolower($code), $methodName);
            $gate->define(strtoupper($code), $methodName);
        }
    }

    /**
     * Register the core AccessControl events listeners.
     *
     *
     * @return void
     */
    protected function registerAclEvents()
    {
        // Register an User dreated event listener that:
        // creates and attach an Acl and
        // creates and attach an User Ace for the User
        User::created(function ($user) {
            $acl = Acl::create(['user_id' => $user->id]);
            $acl->aces()->saveMany([
                new Ace([
                    'object' => 'Microffice\User',
                    'object_id' => $user->id,
                    'mask' => app(MaskBuilderContract::class)
                                ->add('view')
                        ]),
                new Ace([
                    'object' => 'Microffice\User',
                    'object_id' => $user->id,
                    'mask' => app(MaskBuilderContract::class)
                                ->add('edit')
                        ]),
            ]);
        });
        // Register event listener that softdeletes the Acl when a User is softdeleted
        User::deleted(function ($user) {
            // Check that the user has been softdeleted
            // When removed from DB, the cascade property
            // takes care of deleting the Acl and Aces
            if ($user->trashed()) {
            // Softdelete Acl
                $acl = Acl::where('user_id', $user->id)
                            ->first();
                $acl->delete();
            }
        });
        // Register event listener that restores the Acl when a User is restored
        User::restored(function ($user) {
            $acl = Acl::withTrashed()
                        ->where('user_id', $user->id)
                        ->first();
            $acl->restore();
        });
        // Register event listener that softdeletes the Aces when a Acl is softdeleted
        Acl::deleted(function ($acl) {
            // Check that the acl has been softdeleted
            // When removed from DB, the cascade property
            // takes care of deleting the Aces
            if ($acl->trashed()) {
            // Softdelete Aces
                Ace::where('acl_id', $acl->id)
                    ->delete();
            }
        });
        // Register event listener that restores the Ace when a Acl is restored
        Acl::restored(function ($acl) {
            Ace::withTrashed()
                ->where('acl_id', $acl->id)
                ->restore();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDecisionMaker();
        $this->registerMaskBuilder();
        $this->registerObjectIdentityClasses();
    }

    /**
     * Register the access control service.
     *
     * @return void
     */
    protected function registerDecisionMaker()
    {
        $this->app->singleton(DecisionMakerContract::class, function () {
            return app(DecisionMaker::class);
        });
    }

    /**
     * Register the access control maskbuilder.
     *
     * @return void
     */
    protected function registerMaskBuilder()
    {
        $this->app->bind(MaskBuilderContract::class, MaskBuilder::class);
    }

    /**
     * Register the access control service.
     *
     * @return void
     */
    protected function registerObjectIdentityClasses()
    {
        $this->app->singleton(ObjectIdentityFactoryContract::class, function ($app) {
            // Attention, first entry is name, others are details
            $objectNameKey = 'object';
            $objectKeys = [
                'object_id',
                'field'
            ];
            return new ObjectIdentityFactory($objectNameKey, $objectKeys);
        });
         $this->app->bind(ObjectIdentityContract::class, ObjectIdentity::class);
    }
}
