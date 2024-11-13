<?php

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DigitalCoreHub\LaravelIpRestriction\LaravelIpRestrictionServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Test için temel konfigürasyon
        $app['config']->set('iprestriction.allowed_ips', ['127.0.0.1']);
    }
}