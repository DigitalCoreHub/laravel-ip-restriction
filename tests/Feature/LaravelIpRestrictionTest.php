<?php

namespace DigitalCoreHub\LaravelIpRestriction\Tests\Feature;

use DigitalCoreHub\LaravelIpRestriction\LaravelIpRestriction;
use DigitalCoreHub\LaravelIpRestriction\Tests\TestCase;

class LaravelIpRestrictionTest extends TestCase
{
    protected LaravelIpRestriction $ipRestriction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipRestriction = app(LaravelIpRestriction::class);
        config(['ip-restriction.allowed_ips' => ['127.0.0.1', '192.168.1.0/24', '10.0.0.1']]);
    }

    /** @test */
    public function it_checks_if_ip_is_allowed()
    {
        $this->assertTrue($this->ipRestriction->isIpAllowed('127.0.0.1'));
        $this->assertTrue($this->ipRestriction->isIpAllowed('192.168.1.100'));
        $this->assertTrue($this->ipRestriction->isIpAllowed('10.0.0.1'));

        $this->assertFalse($this->ipRestriction->isIpAllowed('192.168.2.1'));
        $this->assertFalse($this->ipRestriction->isIpAllowed('10.0.0.2'));
    }

    /** @test */
    public function it_handles_cidr_notation()
    {
        $this->assertTrue($this->ipRestriction->isIpAllowed('192.168.1.1'));
        $this->assertTrue($this->ipRestriction->isIpAllowed('192.168.1.254'));
        $this->assertFalse($this->ipRestriction->isIpAllowed('192.168.2.1'));
    }

    /** @test */
    public function it_validates_ip_addresses()
    {
        $this->assertTrue($this->ipRestriction->isIpAllowed('127.0.0.1'));
        $this->assertFalse($this->ipRestriction->isIpAllowed('invalid-ip'));
        $this->assertFalse($this->ipRestriction->isIpAllowed('999.999.999.999'));
    }

    /** @test */
    public function it_gets_client_ip_from_request()
    {
        $request = request();
        $request->server->set('REMOTE_ADDR', '192.168.1.100');

        $clientIp = $this->ipRestriction->getClientIp();
        $this->assertEquals('192.168.1.100', $clientIp);
    }

    /** @test */
    public function it_handles_forwarded_headers()
    {
        $request = request();
        $request->headers->set('X-Forwarded-For', '127.0.0.1, 10.0.0.1');

        $clientIp = $this->ipRestriction->getClientIp();
        $this->assertEquals('127.0.0.1', $clientIp);
    }

    /** @test */
    public function it_clears_cache()
    {
        // Önce cache'de bir şey olduğundan emin ol / First, ensure there's something in cache
        $this->ipRestriction->addAllowedIp('203.0.113.1');

        $result = $this->ipRestriction->clearCache();
        $this->assertTrue($result);
    }

    /** @test */
    public function it_adds_allowed_ip_runtime()
    {
        $result = $this->ipRestriction->addAllowedIp('203.0.113.1');
        $this->assertTrue($result);

        $this->assertTrue($this->ipRestriction->isIpAllowed('203.0.113.1'));
    }

    /** @test */
    public function it_removes_allowed_ip_runtime()
    {
        $this->ipRestriction->addAllowedIp('203.0.113.1');
        $this->assertTrue($this->ipRestriction->isIpAllowed('203.0.113.1'));

        $result = $this->ipRestriction->removeAllowedIp('203.0.113.1');
        $this->assertTrue($result);

        $this->assertFalse($this->ipRestriction->isIpAllowed('203.0.113.1'));
    }

    /** @test */
    public function it_handles_invalid_ip_when_adding()
    {
        $result = $this->ipRestriction->addAllowedIp('invalid-ip');
        $this->assertFalse($result);
    }

    /** @test */
    public function it_handles_duplicate_ip_when_adding()
    {
        $this->ipRestriction->addAllowedIp('203.0.113.1');
        $result = $this->ipRestriction->addAllowedIp('203.0.113.1');
        $this->assertTrue($result); // Should not fail, just not add duplicate
    }
}
