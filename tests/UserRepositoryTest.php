<?php

use Microffice\Repositories\UserRepository;

use Microffice\User;
use Illuminate\Validation\ValidationException;

class UserRepositoryTest extends TestCase
{
    /**
     * Current UserRepository instance.
     */
    protected $users;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // AclRepository instance
        $this->users = new UserRepository();

        // Mocking a user
        $user = factory(User::class)->make();
        $this->actingAs($user);
    }

    /**
     * Test $resourceClassName = Microffice\User
     *
     * @return void
     */
    public function testResourceClassNameIsMicrofficeUser()
    {
        $this->assertEquals($this->users->getResourceClassName(), User::Class);
    }

    /**
     * Test $resourceName = User
     *
     * @return void
     */
    public function testResourceClassNameIsUser()
    {
        $start = strrpos(User::Class, '\\');
        $this->assertEquals($this->users->getResourceName(), substr(User::Class, ++$start));
    }

    /**
     * Test validate() throws exception if fails.
     *
     * @return void
     */
    public function testValidateThrowsExceptionIfFails()
    {
        $user = User::where('name', 'Dworkin')->first();

        try {
            $this->users->validate([
                'name' => $user['name'],
                'email' => $user['email']
            ]);
        } catch (ValidationException $expected) {
            return;
        }
        $this->fail('ValidationException was not raised');
    }
}
