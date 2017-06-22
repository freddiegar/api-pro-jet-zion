<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UserAdminSeeder');
        $this->call('PermissionsSeeder');
        // Test fakers
        $this->call('FakerUserSeeder');
    }
}
