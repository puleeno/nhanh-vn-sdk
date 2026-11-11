<?php

namespace Puleeno\NhanhVn\Tests\Unit\Modules;

use PHPUnit\Framework\TestCase;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Modules\OAuthModule;
use Puleeno\NhanhVn\Services\OAuthService;

class OAuthModuleTest extends TestCase
{
    private function logger(): LoggerInterface
    {
        $l = $this->createMock(LoggerInterface::class);
        foreach (['debug','info','warning','error','critical','emergency','alert','notice','log'] as $m) { $l->method($m); }
        return $l;
    }

    public function testGetOAuthUrlDelegatesToService(): void
    {
        $service = $this->createMock(OAuthService::class);
        $service->expects($this->once())
            ->method('getOAuthUrl')
            ->with('https://cb', 'verify123456789', 'pos')
            ->willReturn('https://nhanh.vn/oauth?x=1');

        $module = new OAuthModule($service, $this->logger());
        $this->assertSame('https://nhanh.vn/oauth?x=1', $module->getOAuthUrl('https://cb', 'verify123456789', 'pos'));
    }

    public function testExchangeAccessCodeDelegatesToService(): void
    {
        $service = $this->createMock(OAuthService::class);
        $service->expects($this->once())
            ->method('exchangeAccessCode')
            ->with('ACODE', 'VERIFY')
            ->willReturn(['accessToken' => 'T']);

        $module = new OAuthModule($service, $this->logger());
        $this->assertSame('T', $module->exchangeAccessCode('ACODE', 'VERIFY')['accessToken'] ?? null);
    }
}
