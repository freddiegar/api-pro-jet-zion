<?php

use App\Constants\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::disableCreatedBy();
        User::create([
            'status' => UserStatus::ACTIVE,
            'username' => 'jon@doe.com',
            'password' => hashing('Admin1234/'),
            'type' => User::class,
            'api_token' => 'JDJ5JDEwJFVubG9FSkk4QjJSQ3BhQzdkcGxJMHVlbTBjbE5HN1poNVI2YnU4MS5Db3Q0d0dTbWdOY3Bx',
        ]);
    }
}
