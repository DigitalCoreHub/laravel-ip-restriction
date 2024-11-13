<?php

namespace DigitalCoreHub\LaravelIpRestriction\Tests\Feature;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DigitalCoreHub\LaravelIpRestriction\Tests\TestCase;
use DigitalCoreHub\LaravelIpRestriction\Middleware\RestrictToSpecificIp;

class RestrictToSpecificIpTest extends TestCase
{
    /** @test */
    public function it_allows_access_for_allowed_ip()
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $middleware = new RestrictToSpecificIp();

        $response = $middleware->handle($request, function () {
            return response('success');
        });

        $this->assertEquals('success', $response->getContent());
    }

    /** @test */
    public function it_denies_access_for_disallowed_ip()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '192.168.1.1']);
        $middleware = new RestrictToSpecificIp();

        $middleware->handle($request, function () {
            return response('success');
        });
    }
}