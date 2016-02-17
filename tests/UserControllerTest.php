<?php

use Microffice\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    /**
     * Test /user returns User list.
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
}
