<?php

use Microffice\AccessControl\Ace;
use Microffice\AccessControl\AceRepository;

use Microffice\User;
use Illuminate\Validation\ValidationException;

class AceRepositoryTest extends TestCase
{
    /**
     * Current AceRepository instance.
     */
    protected $aces;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // AceRepository instance
        $this->aces = new AceRepository();

        // Mocking a user
        $user = factory(User::class)->make();
        $this->actingAs($user);
    }

    /**
     * Test $resourceClassName = Microffice\AccessControl\Ace
     *
     * @return void
     */
    public function testResourceClassNameIsMicrofficeAccessControlAce()
    {
        $this->assertEquals($this->aces->getResourceClassName(), Ace::Class);
    }

    /**
     * Test $resourceName = Ace
     *
     * @return void
     */
    public function testResourceClassNameIsAce()
    {
        $start = strrpos(Ace::Class, '\\');
        $this->assertEquals($this->aces->getResourceName(), substr(Ace::Class, ++$start));
    }

    /**
     * Test validate() throws exception if fails on unique rule.
     *
     * @return void
     */
    /*public function testValidateThrowsExceptionIfFailsOnUniqueRule()
    {
        $user = User::all()->first();

        try
        {
            $this->aces->validate(['user_id' => $user->id]);
        }
        catch(ValidationException $expected)
        {
            return;
        }

        $this->fail(ValidationException::class . ' was not raised');
    }/**/

    /**
     * Test validate() throws exception if fails on user id = null.
     *
     * @return void
     */
    /*public function testValidateThrowsExceptionIfFailsOnUserIdIsNull()
    {
        $user = factory(User::class)->make();

        try
        {
            $this->aces->validate(['user_id' => $user->id]);
        }
        catch(ValidationException $expected)
        {
            return;
        }

        $this->fail(ValidationException::class . ' was not raised');
    }/***/
}
