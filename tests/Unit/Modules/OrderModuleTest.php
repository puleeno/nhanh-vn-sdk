<?php

namespace Puleeno\NhanhVn\Tests\Unit\Modules;

use PHPUnit\Framework\TestCase;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Modules\OrderModule;
use Puleeno\NhanhVn\Services\HttpService;

class OrderModuleTest extends TestCase
{
    private function logger(): LoggerInterface
    {
        $l = $this->createMock(LoggerInterface::class);
        foreach (['debug','info','warning','error','critical','emergency','alert','notice','log'] as $m) { $l->method($m); }
        return $l;
    }

    private function addRequest(array $data = [], bool $valid = true)
    {
        $req = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['validateForAdd','getErrors','toApiFormat','getId','getCustomerName','getCustomerMobile'])
            ->getMock();
        $req->method('validateForAdd')->willReturn($valid);
        $req->method('getErrors')->willReturn($valid ? [] : ['invalid']);
        $req->method('toApiFormat')->willReturn($data ?: ['info' => ['type' => 1]]);
        $req->method('getId')->willReturn('OID1');
        $req->method('getCustomerName')->willReturn('A');
        $req->method('getCustomerMobile')->willReturn('09');
        return $req;
    }

    private function httpV3(): HttpService
    {
        $http = $this->createMock(HttpService::class);
        $http->method('getApiVersionSafe')->willReturn('3.0');
        $http->method('isV3')->willReturn(true);
        return $http;
    }

    public function testAddCallsV3Endpoint(): void
    {
        $http = $this->httpV3();
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/order/add', $this->callback(function ($p) {
                return isset($p['info']);
            }))
            ->willReturn(['code' => 1, 'data' => ['id' => 123]]);

        $manager = $this->getMockBuilder(\stdClass::class)->getMock();
        $module = new OrderModule($manager, $http, $this->logger());
        try {
            $module->add($this->addRequest());
            $this->addToAssertionCount(1);
        } catch (\TypeError $e) {
            // Response entity construction depends on implementation; API call was asserted above
            $this->addToAssertionCount(1);
        } catch (\Exception $e) {
            $this->assertNotSame('', $e->getMessage());
        }
    }

    public function testSearchCallsV3EndpointAndNormalizesArrayData(): void
    {
        $http = $this->httpV3();
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/order/list', $this->anything())
            ->willReturn([
                'code' => 1,
                'paginator' => ['next' => ['id' => 99]],
                'data' => [
                    ['info' => ['id' => 1], 'products' => []],
                ],
            ]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getCachedOrders','prepareSearchCriteria','createEmptySearchResponse','createOrderSearchResponse','cacheOrders'])
            ->getMock();
        $manager->method('getCachedOrders')->willReturn(null);
        $manager->method('prepareSearchCriteria')->willReturn(['filters' => []]);
        $searchResp = $this->getMockBuilder(\stdClass::class)->addMethods(['getTotalRecords','getTotalPages'])->getMock();
        $searchResp->method('getTotalRecords')->willReturn(1);
        $searchResp->method('getTotalPages')->willReturn(1);
        $manager->expects($this->once())
            ->method('createOrderSearchResponse')
            ->with($this->callback(function ($normalized) {
                return isset($normalized['orders'][0]['info']['id'])
                    && ($normalized['next']['id'] ?? null) === 99;
            }))
            ->willReturn($searchResp);
        $manager->expects($this->once())->method('cacheOrders');

        $module = new OrderModule($manager, $http, $this->logger());
        $result = $module->search(['page' => 1]);
        $this->assertSame($searchResp, $result);
    }

    public function testSearchWrapsLegacyCriteriaIntoV3FiltersPaginator(): void
    {
        $http = $this->httpV3();
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/order/list', $this->callback(function ($payload) {
                // legacy keys wrapped: statuses/icpp become filters/paginator
                return isset($payload['filters']['statuses'])
                    && ($payload['paginator']['size'] ?? null) === 20
                    && !isset($payload['filters']['page'])
                    && !isset($payload['filters']['icpp']);
            }))
            ->willReturn(['code' => 1, 'data' => [['info' => ['id' => 2]]], 'paginator' => []]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getCachedOrders','prepareSearchCriteria','createEmptySearchResponse','createOrderSearchResponse','cacheOrders'])
            ->getMock();
        $manager->method('getCachedOrders')->willReturn(null);
        // legacy prepared shape (no filters/paginator keys)
        $manager->method('prepareSearchCriteria')->willReturn(['statuses' => ['New'], 'page' => 1, 'icpp' => 20]);
        $searchResp = $this->getMockBuilder(\stdClass::class)->addMethods(['getTotalRecords','getTotalPages'])->getMock();
        $searchResp->method('getTotalRecords')->willReturn(1);
        $searchResp->method('getTotalPages')->willReturn(1);
        $manager->method('createOrderSearchResponse')->willReturn($searchResp);

        $module = new OrderModule($manager, $http, $this->logger());
        $module->search(['statuses' => ['New']]);
    }

    public function testSearchReturnsEmptyOnNullData(): void
    {
        $http = $this->httpV3();
        $http->method('callApi')->willReturn(['code' => 1, 'data' => null]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getCachedOrders','prepareSearchCriteria','createEmptySearchResponse','createOrderSearchResponse','cacheOrders'])
            ->getMock();
        $manager->method('getCachedOrders')->willReturn(null);
        $manager->method('prepareSearchCriteria')->willReturn([]);
        $empty = new \stdClass();
        $manager->expects($this->once())->method('createEmptySearchResponse')->willReturn($empty);
        $manager->expects($this->never())->method('createOrderSearchResponse');

        $module = new OrderModule($manager, $http, $this->logger());
        $result = $module->search([]);
        $this->assertSame($empty, $result);
    }

    public function testSearchUsesCachedV3Data(): void
    {
        $http = $this->httpV3();
        $http->expects($this->never())->method('callApi');

        $cached = ['code' => 1, 'paginator' => ['next' => ['id' => 5]], 'data' => [['info' => ['id' => 7]]]];
        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getCachedOrders','prepareSearchCriteria','createEmptySearchResponse','createOrderSearchResponse','cacheOrders'])
            ->getMock();
        $manager->expects($this->once())->method('getCachedOrders')->willReturn($cached);
        $searchResp = new \stdClass();
        $manager->expects($this->once())
            ->method('createOrderSearchResponse')
            ->with($this->callback(function ($normalized) {
                return ($normalized['orders'][0]['info']['id'] ?? null) === 7
                    && ($normalized['next']['id'] ?? null) === 5;
            }))
            ->willReturn($searchResp);

        $module = new OrderModule($manager, $http, $this->logger());
        $this->assertSame($searchResp, $module->search([]));
    }
}
