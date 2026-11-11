<?php

namespace Puleeno\NhanhVn\Tests\Unit\Modules;

use PHPUnit\Framework\TestCase;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Entities\Shipping\ShippingCarrierResponse;
use Puleeno\NhanhVn\Entities\Shipping\ShippingFeeRequest;
use Puleeno\NhanhVn\Entities\Shipping\ShippingFeeResponse;
use Puleeno\NhanhVn\Modules\ShippingModule;
use Puleeno\NhanhVn\Services\HttpService;

class ShippingModuleTest extends TestCase
{
    private function logger(): LoggerInterface
    {
        $l = $this->createMock(LoggerInterface::class);
        foreach (['debug','info','warning','error','critical','emergency','alert','notice','log'] as $m) { $l->method($m); }
        return $l;
    }

    private function locationResponse(array $data = [])
    {
        $cls = \Puleeno\NhanhVn\Entities\Shipping\LocationSearchResponse::class;
        if (class_exists($cls) && method_exists($cls, 'createSuccess')) {
            return $cls::createSuccess($data);
        }
        return (object) ['data' => $data];
    }

    public function testSearchLocationBuildsV3Payload(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/shipping/location', $this->callback(function ($payload) {
                $f = $payload['filters'] ?? [];
                return ($f['locationVersion'] ?? null) === 'v1'
                    && ($f['type'] ?? null) === 'DISTRICT'
                    && ($f['parentId'] ?? null) === 254;
            }))
            ->willReturn(['code' => 1, 'data' => [['id' => 1, 'name' => 'Ba Dinh']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createLocationSearchResponse','validateLocationSearchData','getLocationSearchValidationErrors','createLocationSearchRequest','createEmptyLocationSearchResponse','createErrorLocationSearchResponse'])
            ->getMock();
        $resp = $this->locationResponse([['id'=>1,'name'=>'Ba Dinh']]);
        $manager->expects($this->once())->method('createLocationSearchResponse')->willReturn($resp);

        $module = new ShippingModule($manager, $http, $this->logger());
        $result = $module->search(['type' => 'DISTRICT', 'parentId' => 254, 'locationVersion' => 'v1']);
        $this->assertSame($resp, $result);
    }

    public function testGetCarriersCallsV3Endpoint(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/shipping/carrier', [])
            ->willReturn(['code' => 1, 'data' => [['id' => 2, 'name' => 'GHN']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createLocationSearchResponse','validateLocationSearchData','getLocationSearchValidationErrors','createLocationSearchRequest','createEmptyLocationSearchResponse','createErrorLocationSearchResponse'])
            ->getMock();

        $module = new ShippingModule($manager, $http, $this->logger());
        $result = $module->getCarriers();
        $this->assertInstanceOf(ShippingCarrierResponse::class, $result);
    }

    public function testCalculateFeeFromArrayPassesV3Filters(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $filters = [
            'type' => 1,
            'shippingWeight' => 2100,
            'shippingFrom' => ['cityId' => 254, 'districtId' => 331, 'wardId' => 1026, 'locationVersion' => 'v1'],
            'shippingTo' => ['cityId' => 254, 'districtId' => 331, 'wardId' => 1120, 'locationVersion' => 'v1'],
        ];
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/shipping/fee', ['filters' => $filters])
            ->willReturn(['code' => 1, 'data' => [['shipFee' => 30000]]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createLocationSearchResponse','validateLocationSearchData','getLocationSearchValidationErrors','createLocationSearchRequest','createEmptyLocationSearchResponse','createErrorLocationSearchResponse'])
            ->getMock();

        $module = new ShippingModule($manager, $http, $this->logger());
        $result = $module->calculateFeeFromArray(['filters' => $filters]);
        $this->assertInstanceOf(ShippingFeeResponse::class, $result);
    }

    public function testCalculateFeeSelfConnectUsesType2OnV3(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/shipping/fee', $this->callback(function ($payload) {
                return ($payload['filters']['type'] ?? null) === 2;
            }))
            ->willReturn(['code' => 1, 'data' => []]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createLocationSearchResponse','validateLocationSearchData','getLocationSearchValidationErrors','createLocationSearchRequest','createEmptyLocationSearchResponse','createErrorLocationSearchResponse'])
            ->getMock();

        $module = new ShippingModule($manager, $http, $this->logger());
        $result = $module->calculateFeeSelfConnectFromArray(['filters' => ['carrier' => ['id' => 8]]]);
        $this->assertNotNull($result);
    }
}
