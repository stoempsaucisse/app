<?php

use Microffice\AccessControl\Acl;
use Microffice\AccessControl\AclRepository;

use Microffice\User;
use Illuminate\Validation\ValidationException;

class AclRepositoryTest extends TestCase
{
    /**
     * Current AclRepository instance.
     */
    protected $acls;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // AclRepository instance
        $this->acls = new AclRepository();

        // Mocking a user
        $user = factory(User::class)->make();
        $this->actingAs($user);
    }

    /**
     * Test $resourceClassName = Microffice\AccessControl\Acl
     *
     * @return void
     */
    public function testResourceClassNameIsMicrofficeAccessControlAcl()
    {
        $this->assertEquals($this->acls->getResourceClassName(), Acl::Class);
    }

    /**
     * Test $resourceName = Acl
     *
     * @return void
     */
    public function testResourceClassNameIsAcl()
    {
        $start = strrpos(Acl::Class, '\\');
        $this->assertEquals($this->acls->getResourceName(), substr(Acl::Class, ++$start));
    }

    /**
     * Test validate() throws exception if fails on unique rule.
     *
     * @return void
     */
    public function testValidateThrowsExceptionIfFailsOnUniqueRule()
    {
        $user = User::all()->first();

        try {
            $this->acls->validate(['user_id' => $user->id]);
        } catch (ValidationException $expected) {
            return;
        }

        $this->fail(ValidationException::class . ' was not raised');
    }

    /**
     * Test validate() throws exception if fails on user id = null.
     *
     * @return void
     */
    public function testValidateThrowsExceptionIfFailsOnUserIdIsNull()
    {
        $user = factory(User::class)->make();

        try {
            $this->acls->validate(['user_id' => $user->id]);
        } catch (ValidationException $expected) {
            return;
        }

        $this->fail(ValidationException::class . ' was not raised');
    }
}
