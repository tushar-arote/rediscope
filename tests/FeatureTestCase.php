<?php

namespace Rediscope\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse as LegacyTestResponse;
use Illuminate\Testing\TestResponse;
use Rediscope\Rediscope;
use Rediscope\RediscopeServiceProvider;
use Orchestra\Testbench\TestCase;

class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (Application::VERSION === '7.x-dev' || version_compare(Application::VERSION, '7.0', '>=')) {
            TestResponse::macro('terminateRediscope', [$this, 'terminateRediscope']);
        } else {
            LegacyTestResponse::macro('terminateRediscope', [$this, 'terminateRediscope']);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            RediscopeServiceProvider::class,
        ];
    }

    protected function resolveApplicationCore($app)
    {
        parent::resolveApplicationCore($app);

        $app->detectEnvironment(function () {
            return 'self-testing';
        });
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('logging.default', 'errorlog');
    }

    protected function loadRediscopeEntries()
    {
        return Rediscope::scan();
    }
}
