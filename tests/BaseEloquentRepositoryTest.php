<?php

use Microffice\Tests\Stubs\BaseEloquentRepositoryStub;
use Microffice\Tests\Stubs\BaseEloquentStub;

use Microffice\User;
use Microffice\AccessControl\DecisionMaker;
use Microffice\AccessControl\Ace;
use Microffice\AccessControl\Acl;
use Microffice\AccessControl\Contracts\DecisionMaker as DecisionMakerContract;
use Microffice\AccessControl\Policies\CorePolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

class BaseEloquentRepositoryTest extends TestCase
{
    /**
     * Current BaseEloquentRepositoryStub instance.
     */
    protected $resources;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // Mocking a user
        $user = User::where('name', 'Dworkin')->first();
        $this->actingAs($user);
        if (! Schema::hasTable('base_eloquent_stubs')) {
            Schema::create('base_eloquent_stubs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('mass_assignable');
                $table->timestamps();
            });

            $acl = Acl::where('user_id', $user->id)->first();

            $baseEloquentStub = BaseEloquentStub::create(['mass_assignable' => 'stub']);
            foreach (['view', 'create', 'edit', 'delete', 'undelete', 'master'] as $mask) {
                $acl->aces()
                ->save(factory(Ace::class)
                    ->make([
                        'object' => BaseEloquentStub::class,
                        'object_id' => $baseEloquentStub->id,
                        'mask' => app(MaskBuilderContract::class)
                            ->add($mask)]));
            }

            $baseEloquentStub = BaseEloquentStub::create(['mass_assignable' => 'another']);
            foreach (['view', 'create', 'edit', 'delete', 'undelete', 'master'] as $mask) {
                $acl->aces()
                ->save(factory(Ace::class)
                    ->make([
                        'object' => BaseEloquentStub::class,
                        'object_id' => $baseEloquentStub->id,
                        'mask' => app(MaskBuilderContract::class)
                            ->add($mask)]));
            }
        }

        // BaseEloquentRepositoryStub instance
        $this->resources = new BaseEloquentRepositoryStub();

        
    }
    /**
     * teardown the test environment.
     */
    public function tearDown()
    {
        // Schema::drop('base_eloquent_stubs');

        parent::tearDown();
    }

    /**
     * Test findAll should check VIEW ability once.
     *
     * @return void
     */
    public function testFindAllShouldCheckViewAbilityOnce()
    {
        // Mock CorePolicy@VIEW that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(CorePolicy::class)
                     ->setMethods(['VIEW'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('VIEW')
             ->with(
                 $this->identicalTo(auth()->user()),
                 $this->stringContains(str_replace('Repository', '', BaseEloquentRepositoryStub::class))
             )
             ->willReturn(true);
        // Inject the mock via IoC container
        $this->app->instance(CorePolicy::class, $mock);

        $this->resources->findAll();
    }

    /**
     * Test findAll should throw authorization exception when VIEW ability is denied.
     *
     * @return void
     */
    public function testFindAllShouldThrowAuthorizationExceptionWhenAbilityIsDenied()
    {
        $this->deny('once');

        try {
            $this->resources->findAll();
        } catch (AuthorizationException $e) {
            return;
        }

        $this->fail(AuthorizationException::class . ' was not raised');
    }

    /**
     * Test findAll should return a collection of all Resources
     * when ability check returns true
     * and allowedIds has no entry for given object / mask combination
     *
     * @return void
     */
    public function testFindAllShouldReturnAllResourceWhenIsGuardingAll()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        $ability = 'view';

        $this->allow('once');

        // Mock DecisionMaker@decidesForAll that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(DecisionMaker::class)
                     ->setConstructorArgs([auth()->user(), app(MaskBuilderContract::class), app(DispatcherContract::class)])
                     ->setMethods(['decidesForAll'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('decidesForAll')
             ->with($ability, $object)
             ->willReturn(true);
        // Inject the mock via IoC container
        $this->app->instance(DecisionMakerContract::class, $mock);

        $allResources = $this->resources->findAll();

        $this->assertInstanceOf(Collection::class, $allResources);
        $this->assertInstanceOf($object, $allResources[0]);
        $this->assertEquals(count($allResources), count($object::all()));
    }

    /**
     * Test findAll should return a collection of some Resources
     * when ability check returns true
     * and allowedIds has no entry for given object / mask combination
     *
     * @return void
     */
    public function testFindAllShouldReturnSomeResourceWhenIsNotguardingAll()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        $ability = 'view';

        $this->allow('once');

        // Mock DecisionMaker@getAllowedIds that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(DecisionMaker::class)
                     ->setConstructorArgs([auth()->user(), app(MaskBuilderContract::class), app(DispatcherContract::class)])
                     ->setMethods(['decidesForAll', 'getAllowedIds'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('decidesForAll')
             ->with($ability, $object)
             ->willReturn(false);
        $allowedIds = [$object::all()->first()->id];
        $mock->expects($this->once())
             ->method('getAllowedIds')
             ->with($ability, $object)
             ->willReturn($allowedIds);
        // Inject the mock via IoC container
        $this->app->instance(DecisionMakerContract::class, $mock);

        $allResources = $this->resources->findAll();

        $this->assertInstanceOf(Collection::class, $allResources);
        $this->assertInstanceOf($object, $allResources[0]);
        $this->assertEquals(count($allowedIds), count($allResources));
    }

    /**
     * Test findById should check VIEW ability once.
     *
     * @return void
     */
    public function testFindByIdShouldCheckViewAbilityOnce()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        // Mock CorePolicy@VIEW that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(CorePolicy::class)
                     ->setMethods(['VIEW'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('VIEW')
             ->with(
                 $this->identicalTo(auth()->user()),
                 $this->stringContains($object)
             )
             ->willReturn(true);
        // Inject the mock via IoC container
        $this->app->instance(CorePolicy::class, $mock);

        $this->resources->findById($object::all()->first()->id);
    }

    /**
     * Test findById should throw authorization exception when VIEW ability is denied.
     *
     * @return void
     */
    public function testFindByIdShouldThrowAuthorizationExceptionWhenAbilityIsDenied()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        $this->deny('once');

        try {
            $this->resources->findById($object::all()->first()->id);
        } catch (AuthorizationException $e) {
            return;
        }

        $this->fail(AuthorizationException::class . ' was not raised');
    }

    /**
     * Test findById should return one Resource when ability check returns true
     *
     * @return void
     */
    public function testFindByIdShouldReturnOneResourceWhenAbilityCheckReturnsTrue()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        $this->allow('once');

        $oneResource = $this->resources->findById($object::all()->first()->id);

        $this->assertInstanceOf($object, $oneResource);
        $this->assertEquals(1, count($oneResource));
    }

    /**
     * Test saveNew should check CREATE ability once.
     *
     * @return void
     */
    public function testSaveNewShouldCheckCreateAbilityOnce()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        // Mock CorePolicy@CREATE that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(CorePolicy::class)
                     ->setMethods(['CREATE'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('CREATE')
             ->with(
                 $this->identicalTo(auth()->user()),
                 $this->stringContains($object)
             )
             ->willReturn(false);
        // Inject the mock via IoC container
        $this->app->instance(CorePolicy::class, $mock);

        try {
            $this->resources->saveNew([]);
        } catch (AuthorizationException $expected) {
            return;
        }
        
    }

    /**
     * Test saveNew should throw authorization exception when CREATE ability is denied.
     *
     * @return void
     */
    public function testSaveNewShouldThrowAuthorizationExceptionWhenAbilityIsDenied()
    {
        $this->deny('once');

        try {
            $this->resources->saveNew([]);
        } catch (AuthorizationException $expected) {
            return;
        }

        $this->fail(AuthorizationException::class . ' was not raised');
        
    }

    /**
     * Test saveNew should call validate() when ability check returns true.
     *
     * @return void
     */
    public function testSaveNewShouldCallValidateWhenAbilityAllows()
    {
        // $user = factory(Microffice\User::class)->create();
        // $user = User::where('name', 'Dworkin')->first();
        $data = ['user_id' => 'any_user_id'];

        // Mock BaseEloquentRepositoryStub@validate that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(BaseEloquentRepositoryStub::class)
                     ->setMethods(['validate'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('validate')
             ->with($data)
             ->will($this->throwException(new \Exception));

        $this->resources = $mock;

        $this->allow('once');

        try {
            $this->resources->saveNew($data);
        } catch (\Exception $expected) {
            return;
        }
    }

    /**
     * Test saveNew should call save() on Resource object when ability check returns true.
     *
     * @return void
     */
    public function testSaveNewShouldCallSaveOnResourceWhenAbilityAllows()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        $data = [
            'mass_assignable' => 'any_data',
            'protected_attribute' => 'some_other_data',
            'data_to_remove' => 'data_to_remove'
        ];

        $this->allow('once');

        // Mock Resource@save that should be called
        // This prevents any DB insertion
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder($object)
                     ->setMethods(['save'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('save')
             ->willReturn(true);
        // Inject the mock via IoC container
        $this->app->instance($object, $mock);

        $this->resources->saveNew($data);
    }

    /**
     * Test update should check EDIT ability once.
     *
     * @return void
     */
    public function testUpdateShouldCheckEditAbilityOnce()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        // Mock CorePolicy@EDIT that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(CorePolicy::class)
                     ->setMethods(['EDIT'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('EDIT')
             ->with(
                 $this->identicalTo(auth()->user()),
                 $this->stringContains($object)
             )
             ->willReturn(false);
        // Inject the mock via IoC container
        $this->app->instance(CorePolicy::class, $mock);

        try {
            $this->resources->update('any_id', []);
        } catch (AuthorizationException $expected) {
            return;
        }
        
    }

    /**
     * Test update should throw authorization exception when EDIT ability is denied.
     *
     * @return void
     */
    public function testUpdateShouldThrowAuthorizationExceptionWhenAbilityIsDenied()
    {
        $this->deny('once');

        try {
            $this->resources->update('any_id', []);
        } catch (AuthorizationException $expected) {
            return;
        }

        $this->fail(AuthorizationException::class . ' was not raised');
        
    }

    /**
     * Test update should call findOrFail(), updateRules() and update()
     * on Resource object when ability check returns true.
     *
     * @return void
     */
    public function testUpdateShouldCallFindOrFailUpdateRulesAndUpdateWhenAbilityAllows()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        $data = [
            'mass_assignable' => 'any_data',
            'protected_attribute' => 'some_other_data',
            'data_to_remove' => 'data_to_remove'
        ];

        // Mock Resource@findOrFail, updateRules and update that should be called
        // This prevents any DB insertion
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder($object)
                     ->setMethods(['findOrFail', 'updateRules', 'update'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('findOrFail')
             ->with('any_ace_id')
             ->will($this->returnSelf());/*/
             ->willReturn($mock);/**/
        $mock->expects($this->once())
             ->method('updateRules')
             ->willReturn([]);
        $mock->expects($this->once())
             ->method('update')
             ->with(['mass_assignable' => 'any_data'])
             ->willReturn(true);
        // Inject the mock via IoC container
        $this->app->instance($object, $mock);

        $this->allow('once');

        $this->resources->update('any_ace_id', $data);/**/
    }

    /**
     * Test update should call validate() when ability check returns true.
     *
     * @return void
     */
    public function testUpdateShouldCallValidateWhenAbilityAllows()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        $resourceId = $object::all()->first()->id;
        $data = ['mass_assignable' => 'some_data'];

        // Mock BaseEloquentRepositoryStub@validate that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(BaseEloquentRepositoryStub::class)
                     ->setConstructorArgs([$object])
                     ->setMethods(['validate'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('validate')
             ->with($data, $this->anything())
             ->will($this->throwException(new \Exception));

        $this->resources = $mock;
        
        $this->allow('once');

        try {
            $this->resources->update($resourceId, $data);
        } catch (\Exception $expected) {
            return;
        }
    }

    /**
     * Test delete should check DELETE ability once.
     *
     * @return void
     */
    public function testDeleteShouldCheckDeleteAbilityOnce()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        // Mock CorePolicy@DELETE that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(CorePolicy::class)
                     ->setMethods(['DELETE'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('DELETE')
             ->with(
                 $this->identicalTo(auth()->user()),
                 $this->stringContains($object)
             )
             ->willReturn(false);
        // Inject the mock via IoC container
        $this->app->instance(CorePolicy::class, $mock);

        try {
            $this->resources->delete('any_id');
        } catch (AuthorizationException $expected) {
            return;
        }
        
    }

    /**
     * Test delete should throw authorization exception when DELETE ability is denied.
     *
     * @return void
     */
    public function testDeleteShouldThrowAuthorizationExceptionWhenAbilityIsDenied()
    {
        $this->deny('once');

        try {
            $this->resources->delete('any_id');
        } catch (AuthorizationException $expected) {
            return;
        }

        $this->fail(AuthorizationException::class . ' was not raised');
        
    }

    /**
     * Test delete should call findOrFail() and delete()
     * on Resource object when ability check returns true.
     *
     * @return void
     */
    public function testDeleteShouldCallFindOrFailAndDeleteWhenAbilityAllows()
    {
        $object = str_replace('Repository', '', BaseEloquentRepositoryStub::class);
        // Mock Resource@findOrFail and delete that should be called
        // This prevents any real deletion
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder($object)
                     ->setMethods(['findOrFail', 'delete'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('findOrFail')
             ->with('any_ace_id')
             ->will($this->returnSelf());/*/
             ->willReturn($mock);/**/
        $mock->expects($this->once())
             ->method('delete')
             ->willReturn(true);
        // Inject the mock via IoC container
        $this->app->instance($object, $mock);

        $this->allow('once');

        $this->resources->delete('any_ace_id');/**/
    }
}
