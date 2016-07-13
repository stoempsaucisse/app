<?php

use Microffice\User;
use Microffice\AccessControl\Contracts\DecisionMaker as DecisionMakerContract;
use Microffice\AccessControl\DecisionMaker;
use Microffice\AccessControl\Acl;
use Microffice\AccessControl\Policies\CorePolicy;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

// use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class CorePolicyTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // Mocking a user
        $user = User::where('name', 'Dworkin')->first();
        $this->actingAs($user);
    }

    /**
     * Test CorePolicy should deny if MaskBuilder::MASK_$ability does not exist
     *
     * @return void
     */
    public function testCorePolicyShouldReturnFalsWhenMaskDoesNotExists()
    {
        $maskName = 'tata';
        $this->assertFalse(defined(MaskBuilder::class . '::MASK_'. $maskName));
        $this->assertTrue(Gate::denies($maskName, [User::class, 2]));
    }

    /**
     * Test CorePolicy should call grants() once
     *
     * @return void
     */
    public function testCorePolicyShouldCallIsGrantedOnce()
    {
        $objectIdentity = Acl::class;
        $objectId = Acl::where('user_id', auth()->user()->id)->first()->id;
        $ability = 'view';
        // Mock DecisionMaker@grants that should be called
        // Following mock instance asserts :
        //              * how many times it is called
        //              * the amount of arguments passed to the method
        $mock = $this->getMockBuilder(DecisionMaker::class)
                     ->setConstructorArgs([auth()->user(), app(MaskBuilderContract::class), app(DispatcherContract::class)])
                     ->setMethods(['grants'])
                     ->getMock();
        $mock->expects($this->once())
             ->method('grants')
             ->with($this->anything(), [$objectIdentity, $objectId])
             ->willReturn(true);
        // Inject the mock via IoC container
        $this->app->instance(DecisionMakerContract::class, $mock);

        Gate::denies($ability, [$objectIdentity, $objectId]);
    }
}
