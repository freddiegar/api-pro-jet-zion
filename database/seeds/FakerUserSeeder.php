<?php

use App\Constants\UserStatus;
use App\Models\User;
use FreddieGar\Base\Constants\BlameColumn;
use Illuminate\Database\Seeder;

class FakerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!isDevelopment()) {
            return;
        }

        /** @noinspection PhpUndefinedClassInspection */
        DB::table('users')->insert([
            [
                'status' => UserStatus::ACTIVE,
                'username' => 'pedro@picapiedra.com',
                'password' => randomHashing(),
                'type' => User::class,
                BlameColumn::CREATED_BY => 1,
                BlameColumn::CREATED_AT => '2015-12-01 15:23:15',
                BlameColumn::UPDATED_BY => null,
                BlameColumn::UPDATED_AT => null,
                BlameColumn::DELETED_BY => null,
                BlameColumn::DELETED_AT => null,
            ],
            [
                'status' => UserStatus::ACTIVE,
                'username' => 'vilma@picapiedra.com',
                'password' => randomHashing(),
                'type' => User::class,
                BlameColumn::CREATED_BY => 2,
                BlameColumn::CREATED_AT => '2016-12-01 05:15:23',
                BlameColumn::UPDATED_BY => null,
                BlameColumn::UPDATED_AT => null,
                BlameColumn::DELETED_BY => null,
                BlameColumn::DELETED_AT => null,
            ],
            [
                'status' => UserStatus::BLOCKED,
                'username' => 'pablo@marmol.com',
                'password' => randomHashing(),
                'type' => User::class,
                BlameColumn::CREATED_BY => 3,
                BlameColumn::CREATED_AT => '2014-01-11 22:02:05',
                BlameColumn::UPDATED_BY => 1,
                BlameColumn::UPDATED_AT => '2014-02-10 23:59:59',
                BlameColumn::DELETED_BY => null,
                BlameColumn::DELETED_AT => null,
            ],
            [
                'status' => UserStatus::BLOCKED,
                'username' => 'pedro@gonzalez.com',
                'password' => randomHashing(),
                'type' => User::class,
                BlameColumn::CREATED_BY => 1,
                BlameColumn::CREATED_AT => '2016-09-20 08:59:00',
                BlameColumn::UPDATED_BY => 2,
                BlameColumn::UPDATED_AT => '2017-02-10 23:59:59',
                BlameColumn::DELETED_BY => null,
                BlameColumn::DELETED_AT => null,
            ],
            [
                'status' => UserStatus::INACTIVE,
                'username' => 'foo@var.com',
                'password' => randomHashing(),
                'type' => User::class,
                BlameColumn::CREATED_BY => 2,
                BlameColumn::CREATED_AT => '2017-07-02 00:00:01',
                BlameColumn::UPDATED_BY => 1,
                BlameColumn::UPDATED_AT => '2017-12-10 23:59:59',
                BlameColumn::DELETED_BY => 1,
                BlameColumn::DELETED_AT => '2017-12-11 00:00:00',
            ],
        ]);
    }
}
