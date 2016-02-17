<?php

use Microffice\Repositories\UserRepository;
use Microffice\User;

use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Events\Dispatcher;

class UserRepositoryTest extends TestCase
{
    /**
     * Current UserRepository instance.
     */
    protected $user;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->setDB('sqlite_memory');

        $this->users = new UserRepository(app(Dispatcher::class));
    }

    /**
     * Test findAll() returns Collection of Users.
     *
     * @return void
     */
    public function testFindAllShouldReturnCollectionOfUsers()
    {
        factory(Microffice\User::class)->create();

        $allUsers = $this->users->findAll();
        $this->assertInstanceOf(Collection::class, $allUsers);
        $this->assertInstanceOf(Microffice\User::class, $allUsers[0]);
    }

    /**
     * Test allowedIds() returns array.
     *
     * @return void
     */
    public function testAllowedIdsShouldReturnArray()
    {
        $allowedIds = $this->users->allowedIds();
        $this->assertTrue(is_array($allowedIds));
    }

    /**
     * Test findAll() call allowedIds() for non-admin.
     *
     * @return void
     */
    public function testFindAllShouldCallAllowedIdsMethodForNonAdmin()
    {
        // Authenticating User but not Dworkin
        $user = factory(Microffice\User::class)->make([
            'name' => 'Abigail',
        ]);
        $this->actingAs($user);

        // Partial mock for allowedIds()
        $mock = Mockery::mock('Microffice\Repositories\UserRepository[allowedIds]', array(app(Dispatcher::class)));
        $mock->shouldReceive('allowedIds')->once()->andReturn([1,2]);

        $allUsers = $mock->findAll();
    }

    /**
     * Test findById() returns User with id = $userId.
     *
     * @return void
     */
    public function testFindByIdShouldReturnUser()
    {
        $userId = factory(Microffice\User::class)->create()->id;
        $user = $this->users->findById($userId);
        $this->assertInstanceOf(Microffice\User::class, $user);
        $this->assertEquals($userId, $user->id);
    }

    /**
     * Test validate() throws exception if fails.
     *
     * @return void
     */
    public function testValidateThrowsExceptionIfFails()
    {
        $user = factory(Microffice\User::class)->create();

        try
        {
            $this->users->validate([
                'name' => $user['name'],
                'email' => $user['email']
            ]);
        }
        catch(ValidationException $expected)
        {
            return;
        }
        $this->fail('ValidationException was not raised');
    }

    /**
     * Test saveNew() throws exception if policy denies.
     *
     * @return void
     */
    public function testSaveNewShouldThrowAuthExceptionIfPolicyDenies()
    {
        // Authenticating User
        $this->actingAs(factory(Microffice\User::class)->make());

        // Denying
        $this->deny('once');

        try
        {
            $newUser = $this->users->saveNew([]);
        }
        catch(AuthorizationException $expected)
        {
            return;
        }
        $this->fail('AuthorizationException was not raised');
    }

    /**
     * Test saveNew() returns instance of User.
     *
     * @return void
     */
    public function testSaveNewShouldReturnUser()
    {
        // Authenticating User
        $this->actingAs(factory(Microffice\User::class)->make());

        // Denying
        $this->allow('once');

        $data = factory(Microffice\User::class)->make()->toArray();
        $data['password'] = 'test11';
        $data['password_confirmation'] = $data['password'];
        $user = $this->users->saveNew($data);
        $this->assertInstanceOf(Microffice\User::class, $user);
        $this->seeInDatabase('users', ['email' => $user->email]);
    }

    /**
     * Test saveNew() fire user:create event.
     *
     * @return void
     */
    public function testSaveNewShouldFireCreateEvent()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->make();
        $this->actingAs($user);

        // Allowing
        $this->allow('once');

        $this->expectsEvents('user:create');

        $data = $user->toArray();
        $data['password'] = 'test11';
        $data['password_confirmation'] = $data['password'];
        $this->users->saveNew($data);
    }

    /**
     * Test saveNew() calls validate().
     *
     * @return void
     */
    public function testSaveNewShouldCallValidate()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->create();
        $this->actingAs($user);

        // Allowing
        $this->allow('once');

        $mock = Mockery::mock('Microffice\Repositories\UserRepository[validate]', array(app(Dispatcher::class)));
        $data = factory(Microffice\User::class)->make()->toArray();
        $data['password'] = 'test11';
        $data['password_confirmation'] = $data['password'];
        $mock->shouldReceive('validate')->withArgs([$data])->once()->andReturn(true);

        $mock->saveNew($data);
    }

    /**
     * Test update() throws exception if policy denies.
     *
     * @return void
     */
    public function testUpdateShouldThrowAuthExceptionIfPolicyDenies()
    {
        // Authenticating User
        $this->actingAs(factory(Microffice\User::class)->make());

        // Denying
        $this->deny('once');

        try
        {
            $user = $this->users->update(1, []);
        }
        catch(AuthorizationException $expected)
        {
            return;
        }
        $this->fail('AuthorizationException was not raised');
    }

    /**
     * Test update() updates DB.
     *
     * @return void
     */
    public function testUpdateShouldUpdateDB()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->create();
        $this->actingAs($user);

        // Allowing
        $this->allow('once');

        $data = factory(Microffice\User::class)->make()->toArray();
        $data['password'] = 'test11';
        $data['password_confirmation'] = $data['password'];
        $this->users->update($user->id, $data);
        $user = Microffice\User::findOrFail($user->id);

        $this->seeInDatabase('users', ['name' => $user->name]);
        $this->assertTrue(Hash::check($data['password'], $user->getAuthPassword()));
    }

    /**
     * Test update() returns bool.
     *
     * @return void
     */
    public function testUpdateShouldReturnBool()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->create();
        $this->actingAs($user);

        // Allowing
        $this->allow('once');

        $data = factory(Microffice\User::class)->make()->toArray();
        $data['password'] = 'test11';
        $data['password_confirmation'] = $data['password'];
        $bool = $this->users->update($user->id, $data);

        $this->assertTrue(is_bool($bool));
        $this->assertTrue($bool);
    }

    /**
     * Test update() calls validate().
     *
     * @return void
     */
    public function testUpdateShouldCallValidate()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->create();
        $this->actingAs($user);

        // Allowing
        $this->allow('once');

        $mock = Mockery::mock('Microffice\Repositories\UserRepository[validate]', array(app(Dispatcher::class)));
        $data = factory(Microffice\User::class)->make()->toArray();
        $mock->shouldReceive('validate')->withArgs([
            $data,
            [
                "name" => "required|max:255|unique:users",
                "email" => "required|email|max:255|unique:users"
            ]
        ])->once()->andReturn(true);

        $mock->update($user->id, $data);
    }

    /**
     * Test update() fire user:update event.
     *
     * @return void
     */
    public function testUpdateShouldFireUpdateEvent()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->create();
        $this->actingAs($user);

        // Allowing
        $this->allow('once');

        $this->expectsEvents('user:update');

        $this->users->update($user->id, factory(Microffice\User::class)->make()->toArray());
    }

    /**
     * Test delete() throws exception if policy denies.
     *
     * @return void
     */
    public function testDeleteShouldThrowExceptionIfPolicyDenies()
    {
        // Authenticating User
        $this->actingAs(factory(Microffice\User::class)->make());

        // Denying
        $this->deny('once');

        try
        {
            $user = $this->users->delete(1);
        }
        catch(AuthorizationException $expected)
        {
            return;
        }
        $this->fail('AuthorizationException was not raised');
    }

    /**
     * Test delete() returns true.
     *
     * @return void
     */
    public function testDeleteShouldReturnTrue()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->create();
        $this->actingAs($user);

        // Denying
        $this->allow('once');

        $this->users->delete($user->id);
        $this->notSeeInDatabase('users', ['id' => $user->id]);
    }

    /**
     * Test delete() fire user:delete event.
     *
     * @return void
     */
    public function testDeleteShouldFireUpdateEvent()
    {
        // Authenticating User
        $user = factory(Microffice\User::class)->create();
        $this->actingAs($user);

        // Allowing
        $this->allow('once');

        $this->expectsEvents('user:delete');

        $this->users->delete($user->id);
    }
}
