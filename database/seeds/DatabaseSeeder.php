<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

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
        $this->call('FakerPermissionSeeder');
        Artisan::call('cache:clear');
    }
}
