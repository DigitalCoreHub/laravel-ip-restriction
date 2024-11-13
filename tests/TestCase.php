<?php

namespace DigitalCoreHub\LaravelIpRestriction\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use DigitalCoreHub\LaravelIpRestriction\LaravelIpRestrictionServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'DigitalCoreHub\\LaravelIpRestriction\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelIpRestrictionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        $app['config']->set('iprestriction.allowed_ips', ['127.0.0.1']);
    }
}
