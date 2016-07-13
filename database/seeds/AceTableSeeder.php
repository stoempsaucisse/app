<?php

use Microffice\User;
use Microffice\AccessControl\Acl;
use Microffice\AccessControl\Ace;
use Illuminate\Database\Seeder;

class AceTableSeeder extends Seeder
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
            include('private_aces.php');
        }
        foreach ($users as $user) {
            $userId = User::where('name', $user['name'])->first()->id;
            $acl = Acl::where(['user_id' => $userId])->first();
            foreach ($aces[$user['name']] as $ace)
            {
                $acl->aces()->save(new Ace($ace));
            }
        }
    }
}
