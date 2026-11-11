<?php

namespace Puleeno\NhanhVn\Tests\Unit\Modules;

use PHPUnit\Framework\TestCase;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Modules\ProductModule;
use Puleeno\NhanhVn\Services\HttpService;

class ProductModuleTest extends TestCase
{
    private function logger(): LoggerInterface
    {
        $l = $this->createMock(LoggerInterface::class);
        foreach (['debug','info','warning','error','critical','emergency','alert','notice','log'] as $m) { $l->method($m); }
        return $l;
    }

    private function httpV3(): HttpService
    {
        $http = $this->createMock(HttpService::class);
        $http->method('getApiVersionSafe')->willReturn('3.0');
        $http->method('isV3')->willReturn(true);
        return $http;
    }

    private function productModule($manager, $http): ProductModule
    {
        return new class($manager, $http, $this->logger()) extends ProductModule {
            protected function createEntitiesFromApiResponse(array $responseData, string $methodName, string $dataKey = 'data'): array
            {
                $data = $responseData[$dataKey] ?? [];
                if (!is_array($data)) return [];
                $out = [];
                foreach ($data as $item) { $out[] = (object) $item; }
                return $out;
            }
        };
    }

    public function testSearchMapsLegacyKeysToV3Filters(): void
    {
        $http = $this->httpV3();
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/list', $this->callback(function ($p) {
                return ($p['filters']['name'] ?? null) === 'Ao'
                    && ($p['paginator']['size'] ?? null) === 20;
            }))
            ->willReturn(['code' => 1, 'data' => [['id' => 1, 'name' => 'Ao']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createProduct','getCachedProductCategories'])
            ->getMock();
        $module = $this->productModule($manager, $http);
        $result = $module->search(['name' => 'Ao', 'icpp' => 20]);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]->id);
    }

    public function testSearchPassesThroughV3Payload(): void
    {
        $http = $this->httpV3();
        $payload = ['filters' => ['status' => [1]], 'paginator' => ['size' => 50]];
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/list', $payload)
            ->willReturn(['code' => 1, 'data' => []]);

        $manager = $this->getMockBuilder(\stdClass::class)->addMethods(['getCachedProductCategories'])->getMock();
        $module = $this->productModule($manager, $http);
        $result = $module->search($payload);
        $this->assertCount(0, $result);
    }

    public function testGetCategoriesCallsV3Endpoint(): void
    {
        $http = $this->httpV3();
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/category', ['filters' => ['status' => 1]])
            ->willReturn(['code' => 1, 'data' => [['id' => 10, 'name' => 'Cat']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getCachedProductCategories','createProductCategory'])
            ->getMock();
        $manager->method('getCachedProductCategories')->willReturn(null);

        $module = $this->productModule($manager, $http);
        $cats = $module->getCategories(['status' => 1]);
        $this->assertCount(1, $cats);
        $this->assertSame(10, $cats[0]->id);
    }

    public function testDetailCallsV3EndpointWithFilters(): void
    {
        $http = $this->httpV3();
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/detail', ['filters' => ['id' => 55]])
            ->willReturn(['code' => 1, 'data' => ['id' => 55, 'name' => 'P']]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createProduct','getCachedProductCategories'])
            ->getMock();
        $product = new \stdClass(); $product->id = 55;
        $manager->expects($this->once())->method('createProduct')->with(['id' => 55, 'name' => 'P'])->willReturn($product);

        $module = new ProductModule($manager, $http, $this->logger());
        $result = $module->detail(55);
        $this->assertSame($product, $result);
    }

    public function testAddV3WrapsPayloadAsArray(): void
    {
        $http = $this->httpV3();

        $request = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['isValid','getErrors','toApiFormat','getId'])
            ->getMock();
        $request->method('isValid')->willReturn(true);
        $request->method('getErrors')->willReturn([]);
        $request->method('toApiFormat')->willReturn(['appProductId' => 'P1', 'name' => 'Prod']);
        $request->method('getId')->willReturn('P1');

        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/add', [['appProductId' => 'P1', 'name' => 'Prod']])
            ->willReturn(['code' => 1, 'data' => [['id' => 100, 'appProductId' => 'P1']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createProductAddRequest','createProductAddResponse','getCachedProductCategories'])
            ->getMock();
        $manager->method('createProductAddRequest')->willReturn($request);
        $addResp = $this->getMockBuilder(\stdClass::class)->addMethods(['getNhanhId','getTotalProducts','getSuccessCount','getSuccessRate'])->getMock();
        $addResp->method('getNhanhId')->willReturn(100);
        $addResp->method('getTotalProducts')->willReturn(1);
        $addResp->method('getSuccessCount')->willReturn(1);
        $addResp->method('getSuccessRate')->willReturn(100.0);
        $manager->method('createProductAddResponse')->willReturn($addResp);

        $module = $this->getMockBuilder(ProductModule::class)
            ->setConstructorArgs([$manager, $http, $this->logger()])
            ->onlyMethods(['clearProductCache'])
            ->getMock();
        $module->expects($this->once())->method('clearProductCache')->with('P1');

        $result = $module->add(['appProductId' => 'P1', 'name' => 'Prod']);
        $this->assertSame($addResp, $result);
    }

    public function testAddBatchV3EnforcesLimit100(): void
    {
        $http = $this->httpV3();
        $manager = $this->getMockBuilder(\stdClass::class)->addMethods(['getCachedProductCategories'])->getMock();
        $module = new ProductModule($manager, $http, $this->logger());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('100');
        $module->addBatch(array_fill(0, 101, ['appProductId' => 'X', 'name' => 'N']));
    }
}
