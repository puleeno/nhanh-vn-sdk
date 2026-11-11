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
use Puleeno\NhanhVn\Exceptions\ApiException;
use Puleeno\NhanhVn\Services\HttpService;

class HttpServiceTest extends TestCase
{
    private function createLogger(): LoggerInterface
    {
        $logger = $this->createMock(LoggerInterface::class);
        foreach (['debug','info','warning','error','critical','emergency','alert','notice','log'] as $m) {
            $logger->method($m);
        }
        return $logger;
    }

    private function createConfig(array $overrides = []): ClientConfig
    {
        return new ClientConfig(array_merge([
            'appId' => '78822',
            'businessId' => '110668',
            'accessToken' => 'access-token',
            'apiDomain' => 'https://pos.open.nhanh.vn',
            'apiVersion' => '3.0',
        ], $overrides));
    }

    private function createService(array $responses, array &$history, ?ClientConfig $config = null): HttpService
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        $client = new Client(['handler' => $stack]);
        return new HttpService($config ?? $this->createConfig(), $this->createLogger(), $client);
    }

    public function testCallApiV3SendsQueryHeadersAndJsonBody(): void
    {
        $history = [];
        $service = $this->createService([new Response(200, [], json_encode(['code' => 1, 'data' => []]))], $history);

        $result = $service->callApi('/v3.0/product/list', [
            'filters' => ['name' => 'Ao'],
            'paginator' => ['size' => 50],
        ]);

        $this->assertSame(1, $result['code']);
        $this->assertCount(1, $history);
        $req = $history[0]['request'];
        $this->assertSame('POST', $req->getMethod());
        $this->assertSame('https://pos.open.nhanh.vn/v3.0/product/list', (string) $req->getUri()->withQuery(''));
        parse_str($req->getUri()->getQuery(), $q);
        $this->assertSame('78822', $q['appId'] ?? null);
        $this->assertSame('110668', $q['businessId'] ?? null);
        $this->assertSame('access-token', $req->getHeaderLine('Authorization'));
        $this->assertStringContainsString('application/json', $req->getHeaderLine('Content-Type'));
        $body = json_decode((string) $req->getBody(), true);
        $this->assertSame(['name' => 'Ao'], $body['filters'] ?? null);
        $this->assertSame(['size' => 50], $body['paginator'] ?? null);
    }

    public function testCallApiAddsAppIdBusinessIdToQuery(): void
    {
        $history = [];
        $service = $this->createService([new Response(200, [], json_encode(['code' => 1]))], $history);
        $service->callApi('/v3.0/customer/list', []);
        parse_str($history[0]['request']->getUri()->getQuery(), $q);
        $this->assertSame('78822', $q['appId'] ?? null);
        $this->assertSame('110668', $q['businessId'] ?? null);
    }

    public function testCallApiLegacyEndpointUsesApiPrefix(): void
    {
        $history = [];
        $service = $this->createService([new Response(200, [], json_encode(['code' => 1]))], $history);
        $service->callApi('/product/search', []);
        $this->assertSame('/api/product/search', $history[0]['request']->getUri()->getPath());
    }

    public function testCallApiThrowsOnErrorCode(): void
    {
        $history = [];
        $service = $this->createService([new Response(200, [], json_encode([
            'code' => 0,
            'errorCode' => 'ERR_403',
            'messages' => 'Forbidden',
        ]))], $history);
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('ERR_403' === 'x' ? '' : 'Forbidden');
        $service->callApi('/v3.0/product/list', []);
    }

    public function testCallApiFormatsFieldMessages(): void
    {
        $history = [];
        $service = $this->createService([new Response(200, [], json_encode([
            'code' => 0,
            'messages' => ['customer.mobile' => 'required', 'name' => 'invalid'],
        ]))], $history);
        try {
            $service->callApi('/v3.0/customer/add', [[]]);
            $this->fail('Expected ApiException');
        } catch (ApiException $e) {
            $this->assertStringContainsString('customer.mobile', $e->getMessage());
            $this->assertStringContainsString('required', $e->getMessage());
        }
    }

    public function testCallApiThrowsOnInvalidJson(): void
    {
        $history = [];
        $service = $this->createService([new Response(200, [], 'not-json')], $history);
        $this->expectException(ApiException::class);
        $service->callApi('/v3.0/product/list', []);
    }

    public function testCallApiThrowsOnMissingCode(): void
    {
        $history = [];
        $service = $this->createService([new Response(200, [], json_encode(['data' => []]))], $history);
        $this->expectException(ApiException::class);
        $service->callApi('/v3.0/product/list', []);
    }

    public function testIsV3(): void
    {
        $history = [];
        $service = $this->createService([], $history);
        $this->assertTrue($service->isV3());
        $this->assertSame('3.0', $service->getApiVersionSafe());
    }
}
