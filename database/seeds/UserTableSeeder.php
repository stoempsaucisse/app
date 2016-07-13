<?php

use Microffice\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        include('users.php');
        if(is_file(dirname(__FILE__ . '/private_users.php')))
        {
            include('private_users.php');
        }
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
