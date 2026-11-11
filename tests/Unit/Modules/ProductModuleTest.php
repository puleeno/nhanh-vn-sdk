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

    public function testSearchCallsV3ProductList(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('getApiVersionSafe')->willReturn('3.0');
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/list', $this->callback(function ($p) {
                return ($p['filters']['name'] ?? null) === 'Ao';
            }))
            ->willReturn(['code' => 1, 'data' => [['id' => 1, 'name' => 'Ao']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createProduct','getCachedProductCategories'])
            ->getMock();
        $manager->method('createProduct')->willReturn(new \stdClass());
        $manager->method('getCachedProductCategories')->willReturn([]);

        $module = new class($manager, $http, $this->logger()) extends ProductModule {
            protected function createEntitiesFromApiResponse(array $responseData, string $methodName, string $dataKey = 'data'): array
            {
                // avoid depending on entity internals in unit test
                $data = $responseData[$dataKey] ?? [];
                if (!is_array($data)) return [];
                $out = [];
                foreach ($data as $item) { $out[] = (object) $item; }
                return $out;
            }
        };

        $result = $module->search(['name' => 'Ao']);
        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]->id);
    }

    public function testGetCategoriesCallsV3Endpoint(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/category', ['filters' => ['status' => 1]])
            ->willReturn(['code' => 1, 'data' => [['id' => 10, 'name' => 'Cat']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getCachedProductCategories','createProductCategory'])
            ->getMock();
        $manager->method('getCachedProductCategories')->willReturn(null);

        $module = new class($manager, $http, $this->logger()) extends ProductModule {
            protected function createEntitiesFromApiResponse(array $responseData, string $methodName, string $dataKey = 'data'): array
            {
                return array_map(fn($x) => (object)$x, $responseData[$dataKey] ?? []);
            }
        };

        $cats = $module->getCategories(['status' => 1]);
        $this->assertCount(1, $cats);
        $this->assertSame(10, $cats[0]->id);
    }

    public function testDetailCallsV3EndpointWithFilters(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('getApiVersionSafe')->willReturn('3.0');
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/product/detail', ['filters' => ['id' => 55]])
            ->willReturn(['code' => 1, 'data' => ['id' => 55, 'name' => 'P']]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createProduct','getCachedProductCategories'])
            ->getMock();
        $product = new \stdClass(); $product->id = 55;
        $manager->expects($this->once())->method('createProduct')->willReturn($product);

        $module = new ProductModule($manager, $http, $this->logger());
        $result = $module->detail(55);
        $this->assertSame($product, $result);
    }
}
