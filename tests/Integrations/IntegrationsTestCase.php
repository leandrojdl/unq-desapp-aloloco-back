<?php
namespace Tests\Integrations;

use Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class IntegrationsTestCase extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    protected function tearDown()
    {
        Artisan::call('migrate:rollback');

        parent::tearDown();
    }
}
