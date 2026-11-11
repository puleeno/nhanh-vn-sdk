<?php

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Managers\CustomerManager;
use Puleeno\NhanhVn\Services\HttpService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse;
use Puleeno\NhanhVn\Entities\Customer\CustomerAddResponse;
use Exception;

/**
 * Customer Module (v3).
 *
 * - POST /v3.0/customer/list  body: {filters, paginator}
 * - POST /v3.0/customer/add   body: [ {customer...}, ... ] (max 100)
 */
class CustomerModule
{
    protected CustomerManager $customerManager;
    protected HttpService $httpService;
    protected LoggerInterface $logger;

    public function __construct(CustomerManager $customerManager, HttpService $httpService, LoggerInterface $logger)
    {
        $this->customerManager = $customerManager;
        $this->httpService = $httpService;
        $this->logger = $logger;
    }

    /**
     * Tìm kiếm khách hàng (v3 /v3.0/customer/list).
     *
     * @param array $searchParams filters/paginator or legacy keys (id/mobile/type/page/icpp/lastBoughtDateFrom/lastBoughtDateTo/updatedAtFrom/updatedAtTo)
     */
    public function search(array $searchParams): CustomerSearchResponse
    {
        $this->logger->debug("CustomerModule::search() called", $searchParams);
        try {
            $payload = $this->buildListPayload($searchParams);
            $endpoint = $this->httpService->isV3() ? '/v3.0/customer/list' : '/customer/search';
            $this->logger->info("CustomerModule::search() calling API", ['endpoint' => $endpoint, 'payload' => $payload]);

            $response = $this->httpService->callApi($endpoint, $payload);
            $normalized = $this->normalizeListResponse($response, $payload);

            if (empty($normalized['customers'])) {
                $this->logger->warning("CustomerModule::search() - no customers");
                unset($response, $payload);
                return $this->customerManager->createEmptySearchResponse();
            }

            $searchResponse = $this->customerManager->createCustomerSearchResponse($normalized);
            unset($response, $payload, $normalized);
            return $searchResponse;
        } catch (Exception $e) {
            $this->logger->error("CustomerModule::search() error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function buildListPayload(array $params): array
    {
        // if caller already passed v3 shape
        if (isset($params['filters']) || isset($params['paginator'])) {
            $payload = [];
            if (!empty($params['filters']) && is_array($params['filters'])) {
                $payload['filters'] = $params['filters'];
            }
            if (!empty($params['paginator']) && is_array($params['paginator'])) {
                $payload['paginator'] = $params['paginator'];
            }
            return $payload;
        }

        $filters = [];
        if (isset($params['id'])) {
            $filters['ids'] = [(int) $params['id']];
        }
        if (!empty($params['ids']) && is_array($params['ids'])) {
            $filters['ids'] = array_values(array_map('intval', $params['ids']));
        }
        if (!empty($params['mobile'])) {
            $filters['mobile'] = (string) $params['mobile'];
        }
        if (isset($params['type'])) {
            $filters['type'] = (int) $params['type'];
        }
        // v3 uses date strings for lastBought*, timestamps for updatedAt*
        if (!empty($params['lastBoughtDateFrom'])) {
            $filters['lastBoughtDateFrom'] = (string) $params['lastBoughtDateFrom'];
        }
        if (!empty($params['lastBoughtDateTo'])) {
            $filters['lastBoughtDateTo'] = (string) $params['lastBoughtDateTo'];
        }
        if (!empty($params['updatedAtFrom'])) {
            $filters['updatedAtFrom'] = (int) $params['updatedAtFrom'];
        }
        if (!empty($params['updatedAtTo'])) {
            $filters['updatedAtTo'] = (int) $params['updatedAtTo'];
        }

        $paginator = [];
        if (isset($params['icpp']) || isset($params['size']) || isset($params['page']) || isset($params['next'])) {
            $size = (int) ($params['icpp'] ?? $params['size'] ?? 50);
            $paginator['size'] = max(1, min($size, 100));
            if (!empty($params['next'])) {
                $paginator['next'] = $params['next'];
            }
        }

        $payload = [];
        if (!empty($filters)) {
            $payload['filters'] = $filters;
        }
        if (!empty($paginator)) {
            $payload['paginator'] = $paginator;
        }
        return $payload;
    }

    private function normalizeListResponse(array $response, array $payload): array
    {
        $data = $response['data'] ?? [];
        $next = $response['paginator']['next'] ?? null;

        // v3: data is array of customers
        if (is_array($data) && $this->isList($data)) {
            return [
                'customers' => $data,
                'totalPages' => $next ? 2 : 1,
                'totalRecords' => count($data),
                'page' => 1,
                'next' => $next,
            ];
        }
        // legacy/v2: data.customers
        if (is_array($data) && isset($data['customers'])) {
            $data['next'] = $data['next'] ?? $next;
            return $data;
        }
        // fallback
        return [
            'customers' => is_array($data) ? $data : [],
            'totalPages' => 0,
            'totalRecords' => is_array($data) ? count($data) : 0,
            'next' => $next,
        ];
    }

    public function searchById(int $customerId): CustomerSearchResponse
    {
        return $this->search(['id' => $customerId]);
    }

    public function searchByMobile(string $mobile): CustomerSearchResponse
    {
        return $this->search(['mobile' => $mobile]);
    }

    public function getByType(int $type, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->search(['type' => $type, 'page' => $page, 'icpp' => $icpp]);
    }

    public function getByDateRange(string $fromDate, string $toDate, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->search([
            'updatedAtFrom' => is_numeric($fromDate) ? (int) $fromDate : strtotime($fromDate),
            'updatedAtTo' => is_numeric($toDate) ? (int) $toDate : strtotime($toDate),
            'page' => $page,
            'icpp' => $icpp,
        ]);
    }

    public function getByLastBoughtDateRange(string $fromDate, string $toDate, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->search([
            'lastBoughtDateFrom' => $fromDate,
            'lastBoughtDateTo' => $toDate,
            'page' => $page,
            'icpp' => $icpp,
        ]);
    }

    public function getAll(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->search(['page' => $page, 'icpp' => $icpp]);
    }

    public function getRetailCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->getByType(1, $page, $icpp);
    }

    public function getWholesaleCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->getByType(2, $page, $icpp);
    }

    public function getAgentCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->getByType(3, $page, $icpp);
    }

    /**
     * Thêm khách hàng (v3 /v3.0/customer/add). Body là mảng khách hàng (tối đa 100).
     */
    public function add(array $customerData): CustomerAddResponse
    {
        $this->logger->debug("CustomerModule::add() called", $customerData);
        try {
            $isBatch = $this->isBatchCustomers($customerData);
            $list = $isBatch ? array_values($customerData) : [$customerData];
            $endpoint = $this->httpService->isV3() ? '/v3.0/customer/add' : '/customer/add';
            $response = $this->httpService->callApi($endpoint, $list);
            $normalized = $this->normalizeAddResponse($response);
            return $this->customerManager->createCustomerAddResponse($normalized);
        } catch (Exception $e) {
            $this->logger->error("CustomerModule::add() error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Thêm nhiều khách hàng (max 100 theo v3).
     */
    public function addBatch(array $customersData): CustomerAddResponse
    {
        if (count($customersData) > 100) {
            throw new Exception('Mỗi request chỉ được gửi tối đa 100 khách hàng (v3)');
        }
        return $this->add(array_values($customersData));
    }

    private function isBatchCustomers(array $data): bool
    {
        if (empty($data)) return false;
        return $this->isList($data) && is_array($data[0] ?? null);
    }

    private function normalizeAddResponse(array $response): array
    {
        // v3 success: {code:1,data:[{id,mobile},...]} ; keep compatible shape
        if (!isset($response['code'])) {
            return ['code' => 0, 'messages' => ['Invalid response']];
        }
        return $response;
    }

    // Validation helpers kept for compatibility
    public function validateAddRequest(array $customerData): bool
    {
        try { return $this->customerManager->validateAddRequest($customerData); } catch (Exception $e) { return false; }
    }
    public function getAddRequestErrors(array $customerData): array
    {
        try { return $this->customerManager->getAddRequestErrors($customerData); } catch (Exception $e) { return ['general' => $e->getMessage()]; }
    }
    public function validateSearchRequest(array $searchParams): bool
    {
        try { return $this->customerManager->validateSearchRequest($searchParams); } catch (Exception $e) { return false; }
    }
    public function getSearchRequestErrors(array $searchParams): array
    {
        try { return $this->customerManager->getSearchRequestErrors($searchParams); } catch (Exception $e) { return ['general' => $e->getMessage()]; }
    }

    public function getManager(): CustomerManager { return $this->customerManager; }
    public function getCacheStatus(): array { return $this->customerManager->getCustomerCacheStatus(); }
    public function clearCache(): bool { return $this->customerManager->clearAllCustomerCache(); }
    public function isCacheAvailable(): bool { return $this->customerManager->isCustomerCacheAvailable(); }

    private function isList(array $arr): bool
    {
        if ($arr === []) return true;
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}
