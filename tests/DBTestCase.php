<?php

use Illuminate\Support\Facades\Artisan;

class DBTestCase extends TestCase
{
    /**
     * SetUp
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }
}
