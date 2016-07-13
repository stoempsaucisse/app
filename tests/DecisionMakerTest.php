<?php

use Microffice\User;
use Microffice\AccessControl\Contracts\DecisionMaker as DecisionMakerContract;
use Microffice\AccessControl\Contracts\ObjectIdentityFactory as ObjectIdentityFactoryContract;
use Microffice\AccessControl\DecisionMaker;
use Microffice\AccessControl\Acl;
use Microffice\AccessControl\Ace;
use Microffice\Core\Support\UniqueItemsCollection;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

// use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class DecisionMakerTest extends TestCase
{
    /**
     * Current DecisionMaker instance.
     */
    protected $decisionMaker;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // Mocking a user
        $user = User::where('name', 'Martin')->first();
        $this->actingAs($user);

        // Getting an DecisionMaker instance to test
        $this->decisionMaker = app(DecisionMakerContract::class);
    }

    // public function setSqlite()
    // {
    //     $this->setDB('sqlite_memory');
    // }

    // public function migrateDB()
    // {
    //     Artisan::call('migrate:refresh');
        
    //     $users = factory(User::class)->create([
    //         'name' => 'Dworkin',
    //         'email' => 'martin.silverberg@gmail.com',
    //         'password' => bcrypt('nitram11')]);

    //     $users->push(factory(User::class)->create([
    //         'name' => 'Martin',
    //         'email' => 'dieuonline@hotmail.com',
    //         'password' => bcrypt('nitram11')]));

    //     $users->push(factory(User::class, 2)->create());

    //     $users->each(function ($user) {
    //         $acl = factory(Acl::class)
    //             ->create(['user_id' => $user->id]);
    //         $acl->aces()
    //             ->save(factory(Ace::class)
    //             ->make([
    //                 'object' => Acl::class,
    //                 'object_id' => $acl->id,
    //                 'mask' => 'VIEW']));
    //         foreach (['view', 'create', 'edit', 'delete', 'undelete', 'master'] as $mask) {
    //             $acl->aces()
    //                 ->save(factory(Ace::class)
    //                     ->make([
    //                         'object' => User::class,
    //                         'object_id' => $user->id,
    //                         'mask' => app(MaskBuilderContract::class)
    //                             ->add($mask)]));
    //         }
            
    //     });

    //     $users->each(function ($user) {
    //         $acl = Acl::where('user_id', $user->id)->first();
    //         if ($user->name == 'Dworkin') {
    //             foreach (['view', 'create', 'edit', 'delete', 'undelete', 'master', 'owner'] as $mask) {
    //                 $acl->aces()
    //                     ->save(factory(Ace::class)
    //                         ->make([
    //                             'object' => User::class,
    //                             'mask' => app(MaskBuilderContract::class)
    //                                 ->add($mask)]));
    //             }
    //         } else {
    //             $acl->aces()->save(factory(Ace::class)->make(['object' => User::class, 'mask' => 'view']));
    //         }
    //     });
    // }

    /**
     * Test __construct.
     *
     * @return void
     */
    public function testConstructor()
    {
        $decisionMaker = new DecisionMaker(auth()->user(), app(MaskBuilderContract::class), app(DispatcherContract::class));
        $decisionMaker->isCompiled(app(ObjectIdentityFactoryContract::class)->make(User::class, ['object_id' => 5])->get());
        // dd($decisionMaker);
    }


    /**
     * Test __construct for User without Acl should throw exception.
     *
     * @return void
     */
    public function testConstructForUserWithoutAclShouldThrowException()
    {
        $user = factory(User::class)->make();

        try {
            $decisionMaker = new DecisionMaker($user, app(MaskBuilderContract::class), app(DispatcherContract::class));
        } catch (ModelNotFoundException $e) {
            var_dump($e->getMessage());
            return;
        }

        $this->fail(ModelNotFoundException::class . ' was not thrown');
    }

    /**
     * Test objectIdentityExists() returns false for un-available object identity.
     *
     * @return void
     */
    public function testObjectIdentityExistsShouldReturnFalseForUnaviableObjectIdentity()
    {
        $result = $this->decisionMaker->objectIdentityExists('objectIdentity');
        $this->assertFalse($result);
    }

    /**
     * Test objectIdentityExists() returns true for available object identity.
     *
     * @return void
     */
    public function testObjectIdentityExistsShouldReturnTrueForAviableObjectIdentity()
    {
        $result = $this->decisionMaker->objectIdentityExists(Acl::class);
        $this->assertTrue($result);
    }

    /**
     * Test acceptObjectIdentity() should throw excption for un-available object identity.
     *
     * @return void
     */
    public function testAcceptObjectIdentityShouldThrowExceptionForUnaviableObjectIdentity()
    {
        $result = $this->decisionMaker->acceptObjectIdentity(Acl::class);
        $this->assertTrue($result);
    }

    /**
     * Test acceptObjectIdentity() should return true for available object identity.
     *
     * @return void
     */
    public function testAcceptObjectIdentityShouldReturnTrueForAviableObjectIdentity()
    {
        try {
            $this->decisionMaker->acceptObjectIdentity('objectIdentity');
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
            return;
        }

        $this->fail(InvalidArgumentException::class . ' was not thrown');
    }

    /**
     * Test resolveMask() returns integer form.
     *
     * @return void
     */
    public function testResolveMaskShouldReturnIntegerForm()
    {
        $result = $this->decisionMaker->resolveMask('view');
        $this->assertEquals(1, $result);
        $this->assertInternalType('integer', $result);
    }

    /**
     * Test getMaskName() returns string.
     *
     * @return void
     */
    public function testGetMaskNameShouldReturnString()
    {
        $reflection = new \ReflectionClass(MaskBuilder::class);
        foreach ($reflection->getConstants() as $name => $cMask) {
            if (0 !== strpos($name, 'MASK_')) {
                continue;
            }
            $result = $this->decisionMaker->getMaskName($cMask);
            $this->assertInternalType('string', $result);
        }
    }

    /**
     * Test getMaskName() throws exception for unknown mask.
     *
     * @return void
     */
    public function testGetMaskNameShouldThrowExceptionForUnknownMask()
    {
        foreach ([500, 'GOD', 'v', 'V'] as $mask) {
            try {
                $this->decisionMaker->getMaskName($mask);
            } catch (InvalidArgumentException $e) {
                var_dump($e->getMessage());
                continue;
            }

            $this->fail(InvalidArgumentException::class . ' was not thrown');
        }
    }

    /**
     * Test isCompiled() returns false for un-available Object Identity.
     *
     * @return void
     */
    public function testIsCompiledShouldReturnFalseForUnaviableObjectIdentity()
    {
        $result = $this->decisionMaker->isCompiled('objectIdentity');
        $this->assertFalse($result);
    }

    /**
     * Test isCompiled() returns true for available Object Identity.
     *
     * @return void
     */
    public function testIsCompiledShouldReturnTrueForAviableObjectIdentity()
    {
        $this->decisionMaker->compilePartial(Ace::class);
        $result = $this->decisionMaker->isCompiled(Ace::class);
        $this->assertTrue($result);
    }

    /**
     * Test compilePartial().
     *
     * @return void
     */
    public function testCompilePartial()
    {
        /*$this->migrateDB();/*/
        $this->decisionMaker->compilePartial(Ace::class);
        $this->decisionMaker->compilePartial(User::class);
        $this->assertTrue($this->decisionMaker->isCompiled(Ace::Class));
        $this->assertTrue($this->decisionMaker->isCompiled(User::Class));
        $this->assertFalse($this->decisionMaker->isCompiled(Acl::Class));
    }

    /**
     * Test compile().
     *
     * @return void
     */
    public function testCompile()
    {
        $this->decisionMaker->compile();
        $this->assertTrue($this->decisionMaker->isCompiled(Ace::Class));
        $this->assertTrue($this->decisionMaker->isCompiled(Acl::Class));
        $this->assertTrue($this->decisionMaker->isCompiled(User::Class));
    }

    /**
     * Test grants() with un-available object identity should throw exception.
     *
     * @return void
     */
    public function testIsGrantedWithUnaviableObjectIdentityShouldThrowException()
    {
        try {
            $this->decisionMaker->grants('VIEW', ['objectIdentity']);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
            return;
        }

        $this->fail(InvalidArgumentException::class . ' was not thrown');
    }

    /**
     * Test grants() returns true when granted all.
     *
     * @return void
     */
    public function testIsGrantedShouldReturnTrueWhenGrantedAll()
    {
        $IDDQD = constant(get_class(app(MaskBuilderContract::class)) . '::MASK_IDDQD');
        $aceGrantedAll = Ace::where('object_id', null)
                            ->where('mask', '!=', $IDDQD)
                            ->first();
        $user = User::find($aceGrantedAll->acl()->first()->user_id);
        $this->actingAs($user);

        $result = $this->decisionMaker->grants($aceGrantedAll->mask->get(), [$aceGrantedAll->object]);
        $this->assertTrue($result);
    }

    /**
     * Test getKey() returns string.
     *
     * @return void
     */
    public function testGetKeyShouldReturnString()
    {
        $result = $this->decisionMaker->getKey('VIEW', Acl::class);
        $this->assertThat($result, $this->isType('string'));
        $this->assertContains('VIEW', $result);
        $this->assertContains(Acl::class, $result);
    }

    /**
     * Test getAllowedIds().
     *
     * @return void
     */
    public function testGetAllowedIds()
    {
        
        $user = User::where('name', 'Martin')->first();
        $decisionMaker = new DecisionMaker($user, app(MaskBuilderContract::class), app(DispatcherContract::class));
        $decisionMaker->compile();
        $result = $decisionMaker->getAllowedIds('OWNER', Ace::class);
        
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertNotContains('all', $result);
    }
}
