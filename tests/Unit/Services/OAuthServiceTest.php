<?php

namespace Puleeno\NhanhVn\Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Services\OAuthService;

class OAuthServiceTest extends TestCase
{
    private function createLogger(): LoggerInterface
    {
        $logger = $this->createMock(LoggerInterface::class);
        foreach (['debug','info','warning','error','critical','emergency','alert','notice','log'] as $m) {
            $logger->method($m);
        }
        return $logger;
    }

    private function createConfig(): ClientConfig
    {
        return new ClientConfig([
            'appId' => '78822',
            'secretKey' => 'secret-key',
            'returnLink' => 'https://example.com/cb',
            'apiDomain' => 'https://pos.open.nhanh.vn',
        ]);
    }

    public function testGetOAuthUrlV3(): void
    {
        $service = new OAuthService($this->createConfig(), $this->createLogger());
        $url = $service->getOAuthUrl('https://example.com/cb', 'VerifyCode123456', 'pos');
        parse_str(parse_url($url)['query'] ?? '', $q);
        $this->assertSame('78822', $q['appId'] ?? null);
        $this->assertSame('pos', $q['service'] ?? null);
        $this->assertSame('VerifyCode123456', $q['verifyCode'] ?? null);
        $this->assertSame('https://example.com/cb', $q['returnLink'] ?? null);
        $this->assertArrayNotHasKey('version', $q);
    }

    public function testExchangeAccessCodeV3RequestAndNormalization(): void
    {
        $history = [];
        $mock = new MockHandler([new Response(200, [], json_encode([
            'code' => 1,
            'data' => [
                'accessToken' => 'ACCESS',
                'expiredAt' => 1785603599,
                'businessId' => 110668,
                'permissions' => ['product'],
            ],
        ]))]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        $client = new Client(['handler' => $stack]);

        $service = new OAuthService($this->createConfig(), $this->createLogger(), $client);
        $result = $service->exchangeAccessCode('ACODE', 'VerifyCode123456');

        $this->assertSame('ACCESS', $result['accessToken'] ?? null);
        $this->assertSame(110668, $result['businessId'] ?? null);

        $this->assertCount(1, $history);
        $req = $history[0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertStringContainsString('/v3.0/app/getaccesstoken', $req->getUri()->getPath());
        parse_str($req->getUri()->getQuery(), $q);
        $this->assertSame('78822', $q['appId'] ?? null);
        $this->assertSame('secret-key', $req->getHeaderLine('Authorization'));
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame('ACODE', $body['accessCode'] ?? null);
        $this->assertSame('VerifyCode123456', $body['verifyCode'] ?? null);
    }

    public function testExchangeAccessCodeThrowsOnApiError(): void
    {
        $mock = new MockHandler([new Response(200, [], json_encode([
            'code' => 0,
            'messages' => 'Invalid access code',
        ]))]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $service = new OAuthService($this->createConfig(), $this->createLogger(), $client);
        $this->expectException(\Puleeno\NhanhVn\Exceptions\ApiException::class);
        $service->exchangeAccessCode('bad');
    }
}
