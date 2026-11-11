<?php

namespace Puleeno\NhanhVn\Tests\Unit\Modules;

use PHPUnit\Framework\TestCase;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Modules\CustomerModule;
use Puleeno\NhanhVn\Services\HttpService;

class CustomerModuleTest extends TestCase
{
    private function logger(): LoggerInterface
    {
        $l = $this->createMock(LoggerInterface::class);
        foreach (['debug','info','warning','error','critical','emergency','alert','notice','log'] as $m) { $l->method($m); }
        return $l;
    }

    private function searchResponseEntity(array $data)
    {
        if (class_exists(\Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse::class)
            && method_exists(\Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse::class, 'createSuccessResponse')) {
            return \Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse::createSuccessResponse($data);
        }
        return (object) $data;
    }

    public function testSearchBuildsV3PayloadAndNormalizesListResponse(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/customer/list', $this->callback(function ($payload) {
                $f = $payload['filters'] ?? [];
                $p = $payload['paginator'] ?? [];
                return ($f['type'] ?? null) === 1
                    && ($f['mobile'] ?? null) === '0988999999'
                    && ($p['size'] ?? null) === 10;
            }))
            ->willReturn([
                'code' => 1,
                'paginator' => ['next' => 'nxt'],
                'data' => [
                    ['id' => 1, 'name' => 'A', 'mobile' => '0988999999'],
                ],
            ]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createCustomerSearchResponse', 'createEmptySearchResponse'])
            ->getMock();
        $manager->expects($this->once())
            ->method('createCustomerSearchResponse')
            ->with($this->callback(function ($normalized) {
                return isset($normalized['customers'][0]['id'])
                    && $normalized['customers'][0]['id'] === 1
                    && ($normalized['next'] ?? null) === 'nxt';
            }))
            ->willReturn($this->searchResponseEntity(['customers' => [['id' => 1]]]));

        $module = new CustomerModule($manager, $http, $this->logger());
        $result = $module->search(['type' => 1, 'mobile' => '0988999999', 'icpp' => 10]);
        $this->assertNotNull($result);
    }

    public function testSearchReturnsEmptyWhenNoData(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $http->method('callApi')->willReturn(['code' => 1, 'data' => []]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createCustomerSearchResponse', 'createEmptySearchResponse'])
            ->getMock();
        $empty = $this->searchResponseEntity(['customers' => []]);
        $manager->expects($this->once())->method('createEmptySearchResponse')->willReturn($empty);
        $manager->expects($this->never())->method('createCustomerSearchResponse');

        $module = new CustomerModule($manager, $http, $this->logger());
        $result = $module->search(['page' => 1]);
        $this->assertSame($empty, $result);
    }

    public function testAddWrapsSingleCustomerIntoArrayForV3(): void
    {
        $http = $this->createMock(HttpService::class);
        $http->method('isV3')->willReturn(true);
        $http->expects($this->once())
            ->method('callApi')
            ->with('/v3.0/customer/add', $this->callback(function ($body) {
                return is_array($body) && isset($body[0]['name']) && $body[0]['name'] === 'Nguyen Van A';
            }))
            ->willReturn(['code' => 1, 'data' => [['id' => 10, 'mobile' => '0989']]]);

        $manager = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['createCustomerAddResponse'])
            ->getMock();
        $resp = new \stdClass();
        $manager->expects($this->once())->method('createCustomerAddResponse')->willReturn($resp);

        $module = new CustomerModule($manager, $http, $this->logger());
        $result = $module->add(['name' => 'Nguyen Van A', 'mobile' => '0989']);
        $this->assertSame($resp, $result);
    }

    public function testAddBatchRejectsOver100(): void
    {
        $http = $this->createMock(HttpService::class);
        $manager = $this->getMockBuilder(\stdClass::class)->addMethods(['createCustomerAddResponse'])->getMock();
        $module = new CustomerModule($manager, $http, $this->logger());
        $this->expectException(\Exception::class);
        $module->addBatch(array_fill(0, 101, ['name' => 'A', 'mobile' => '1']));
    }
}
