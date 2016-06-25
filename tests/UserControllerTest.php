<?php

use Microffice\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    /**
     * Test GET /user returns User list.
     *
     * @return void
     */
    public function testGetUserReturnsUserList()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);

        $this->visit('/user')
             ->see(trans('user.index'))
             ->assertViewHas('users');
    }

    /**
     * Test GET /user/create returns a form.
     *
     * @return void
     */
    public function testGetUserCreateReturnsForm()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);

        $this->visit('/user/create')
             ->see(trans('user.create'))
             ->see(trans('form.save'))
             ->see(trans('form.reset'))
             ->see(trans('user.name'))
             ->see(trans('form.email'))
             ->see(trans('auth.password'))
             ->see(trans('auth.password_confirmation'));
    }

    /**
     * Test POST /user with valid user info redirects to user index.
     *
     * @return void
     */
    public function testPostValidUserInfoRedirectsToUserIndex()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);

        $newUser = factory(Microffice\User::class)->make();
        $this->visit('/user/create')
             ->type($newUser->name, 'user[name]')
             ->type($newUser->email, 'user[email]')
             ->type($newUser->password, 'user[password]')
             ->type($newUser->password, 'user[password_confirmation]')
             ->press(trans('form.save'))
             ->seePageIs('/user');
    }

    /**
     * Test POST /user with invalid user info redirects to create user form with input except password.
     *
     * @return void
     */
    public function testPostInvalidUserInfoRedirectsToUserCreate()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);

        $newUser = factory(Microffice\User::class)->make();
        $badEmail = 'email@mail';
        $this->visit('/user/create')
             ->type($newUser->name, 'user[name]')
             ->type($badEmail, 'user[email]')
             ->type($newUser->password, 'user[password]')
             ->type($newUser->password, 'user[password_confirmation]')
             ->press(trans('form.save'))
             ->seePageIs('/user/create')
             ->see($newUser->name)
             ->see($badEmail)
             ->see(str_replace(':attribute', trans('validation.attributes.email'), trans('validation.email')))
             ->dontSee($newUser->password);
    }

    /**
     * Test GET /user/{id} returns user form with user data.
     *
     * @return void
     */
    public function testGetUserIdReturnsFormWithUserData()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);

        $this->visit('/user/' . $user->id)
             ->see(trans('user.update'))
             ->see(trans('form.save'))
             ->see(trans('form.reset'))
             ->see(trans('form.delete'))
             ->see($user->name)
             ->see($user->email)
             ->dontSee($user->password);
    }

    /**
     * Test POST /user/{id} updates user data and redirects to user index.
     *
     * @return void
     */
    public function testPostUserIdUpdatesUserDataRedirectToUserIndex()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);
        $newName = rand(10000, 999999);

        $this->visit('/user/' . $user->id)
             ->type($newName, 'user[name]')
             ->press(trans('form.save'))
             ->seePageIs('/user');

        $user = User::findOrFail($user->id);
        $this->assertEquals($user->name, $newName);
    }

    /**
     * Test POST /user/{id} with invalid data redirects to user form.
     *
     * @return void
     */
    public function testPostUserIdInvalidDataRedirectToUserForm()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);
        $newPassword = 'baba';

        $this->visit('/user/' . $user->id)
             ->type($newPassword, 'user[password]')
             ->type($newPassword, 'user[password_confirmation]')
             ->press(trans('form.save'))
             ->seePageIs('/user/' . $user->id)
             ->see($user->name)
             ->see($user->email)
             ->see(str_replace(':min', 6, str_replace(':attribute', trans('validation.attributes.password'), trans('validation.min.string'))))
             ->dontSee($newPassword);
    }

    /**
     * Test DELETE /user/{id} delete user and redirects to user index.
     *
     * @return void
     */
    public function testDeleteUserIdReturnsToUserIndex()
    {
        // Authenticating User but not Dworkin
        $user = User::where('name', '!=', 'Dworkin')->first();
        $this->actingAs($user);

        $this->visit('/user/' . $user->id)
             ->press(trans('form.delete'))
             ->seePageIs('/user');
        
        try
        {
            $newUser = User::findOrFail($user->id);
        }
        catch(Illuminate\Database\Eloquent\ModelNotFoundException $expected)
        {
            return;
        }
        $this->fail('Illuminate\Database\Eloquent\ModelNotFoundException was not raised');
    }
}
