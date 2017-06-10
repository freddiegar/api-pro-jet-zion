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
        User::create([
            'status' => UserStatus::ACTIVE,
            'username' => 'jon@doe.com',
            'password' => hashing('Admin1234/'),
            'type' => User::class,
            'api_token' => base64_decode('SkRKNUpERXdKRlZ1Ykc5RlNrazRRakpTUTNCaFF6ZGtjR3hKTUhWbGJUQmpiRTVITjFwb05WSTJZblU0TVM1RGIzUTBkMGRUYldkT1kzQng='),
        ]);
    }
}
