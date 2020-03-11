<?php

namespace Rediscope\Tests\Http;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestResponse as LegacyTestResponse;
use Illuminate\Testing\TestResponse;
use Rediscope\Http\Middleware\Authorize;
use Orchestra\Testbench\Http\Middleware\VerifyCsrfToken;
use PHPUnit\Framework\Assert as PHPUnit;
use Rediscope\Tests\FeatureTestCase;

class RouteTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([Authorize::class, VerifyCsrfToken::class]);

        $this->registerAssertJsonExactFragmentMacro();
    }

    public function rediscopeIndexRoutesProvider()
    {
        return [
            'Redis' => ['/rediscope/api/scan'],
        ];
    }

    /**
     * @dataProvider rediscopeIndexRoutesProvider
     */
    public function test_route($endpoint)
    {
        $this->post($endpoint)
            ->assertSuccessful()
            ->assertJsonStructure(['entries' => []]);
    }

    private function registerAssertJsonExactFragmentMacro()
    {
        $assertion = function ($expected, $key) {
            $jsonResponse = $this->json();

            PHPUnit::assertEquals(
                $expected,
                $actualValue = data_get($jsonResponse, $key),
                "Failed asserting that [$actualValue] matches expected [$expected].".PHP_EOL.PHP_EOL.
                json_encode($jsonResponse)
            );

            return $this;
        };

        if (Application::VERSION === '7.x-dev' || version_compare(Application::VERSION, '7.0', '>=')) {
            TestResponse::macro('assertJsonExactFragment', $assertion);
        } else {
            LegacyTestResponse::macro('assertJsonExactFragment', $assertion);
        }
    }

    public function test_named_route()
    {
        $this->assertEquals(
            url(config('rediscope.path')),
            route('rediscope')
        );
    }
}
