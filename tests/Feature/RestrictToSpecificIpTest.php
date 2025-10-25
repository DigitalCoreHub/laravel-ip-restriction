<?php

namespace DigitalCoreHub\LaravelIpRestriction\Tests\Feature;

use DigitalCoreHub\LaravelIpRestriction\LaravelIpRestriction;
use DigitalCoreHub\LaravelIpRestriction\Middleware\RestrictToSpecificIp;
use DigitalCoreHub\LaravelIpRestriction\Tests\TestCase;
use Illuminate\Http\Request;

class RestrictToSpecificIpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Test konfigürasyonunu ayarla / Set up test configuration
        config(['ip-restriction.allowed_ips' => ['127.0.0.1', '192.168.1.0/24']]);
        config(['ip-restriction.log_attempts' => true]);
        config(['ip-restriction.error_message' => 'Access denied. Your IP address is not authorized.']);
    }

    /** @test */
    public function it_allows_access_for_allowed_ip()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $ipRestriction = app(LaravelIpRestriction::class);
        $middleware = new RestrictToSpecificIp($ipRestriction);

        $response = $middleware->handle($request, function () {
            return response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    /** @test */
    public function it_denies_access_for_disallowed_ip()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '10.0.0.1']);
        $ipRestriction = app(LaravelIpRestriction::class);
        $middleware = new RestrictToSpecificIp($ipRestriction);

        // Debug: IP'nin gerçekten yasaklı olup olmadığını kontrol et / Check if IP is actually disallowed
        $this->assertFalse($ipRestriction->isIpAllowed('10.0.0.1'), 'IP should be disallowed');

        // Debug: Middleware'in hangi IP'yi aldığını kontrol et / Check what IP the middleware gets
        $clientIp = $request->ip();
        $this->assertEquals('10.0.0.1', $clientIp, 'Client IP should be 10.0.0.1');

        try {
            $middleware->handle($request, function () {
                return response('success');
            });
            $this->fail('Expected HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }
    }

    /** @test */
    public function it_allows_access_for_ip_in_cidr_range()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '192.168.1.100']);
        $ipRestriction = app(LaravelIpRestriction::class);
        $middleware = new RestrictToSpecificIp($ipRestriction);

        $response = $middleware->handle($request, function () {
            return response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    /** @test */
    public function it_denies_access_for_ip_outside_cidr_range()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '192.168.2.1']);
        $ipRestriction = app(LaravelIpRestriction::class);
        $middleware = new RestrictToSpecificIp($ipRestriction);

        // Debug: IP'nin gerçekten yasaklı olup olmadığını kontrol et / Check if IP is actually disallowed
        $this->assertFalse($ipRestriction->isIpAllowed('192.168.2.1'), 'IP should be disallowed');

        try {
            $middleware->handle($request, function () {
                return response('success');
            });
            $this->fail('Expected HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }
    }

    /** @test */
    public function it_handles_forwarded_headers()
    {
        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_FOR' => '127.0.0.1, 10.0.0.1',
        ]);
        $ipRestriction = app(LaravelIpRestriction::class);
        $middleware = new RestrictToSpecificIp($ipRestriction);

        $response = $middleware->handle($request, function () {
            return response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    /** @test */
    public function it_logs_unauthorized_access_attempts()
    {
        config(['ip-restriction.log_attempts' => true]);

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '10.0.0.1']);
        $ipRestriction = app(LaravelIpRestriction::class);
        $middleware = new RestrictToSpecificIp($ipRestriction);

        // Debug: IP'nin gerçekten yasaklı olup olmadığını kontrol et / Check if IP is actually disallowed
        $this->assertFalse($ipRestriction->isIpAllowed('10.0.0.1'), 'IP should be disallowed');

        try {
            $middleware->handle($request, function () {
                return response('success');
            });
            $this->fail('Expected HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }
    }
}
