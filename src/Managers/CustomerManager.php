<?php

namespace Puleeno\NhanhVn\Managers;

use Puleeno\NhanhVn\Services\CustomerService;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse;
use Puleeno\NhanhVn\Entities\Customer\CustomerAddResponse;
use Puleeno\NhanhVn\Repositories\CustomerRepository;

/**
 * Customer Manager
 *
 * Orchestrates customer-related operations between different layers
 */
class CustomerManager
{
    /** @var CustomerService */
    private $customerService;

    /** @var CustomerRepository|null */
    private $customerRepository;

    /**
     * @param CustomerService $customerService
     * @param CustomerRepository|null $customerRepository
     */
    public function __construct($customerService, $customerRepository = null)
    {
        $this->customerService = $customerService;
        $this->customerRepository = $customerRepository;
    }

    private function repo(): ?CustomerRepository
    {
        if ($this->customerRepository instanceof CustomerRepository) {
            return $this->customerRepository;
        }
        // best-effort: some DI setups may inject repository differently
        if (method_exists($this->customerService, 'getRepository')) {
            $r = $this->customerService->getRepository();
            if ($r instanceof CustomerRepository) return $r;
        }
        return null;
    }

    public function searchCustomers(array $searchParams): CustomerSearchResponse
    {
        return $this->customerService->searchCustomers($searchParams);
    }

    public function searchCustomerById(int $customerId): CustomerSearchResponse
    {
        return $this->customerService->searchCustomerById($customerId);
    }

    public function searchCustomerByMobile(string $mobile): CustomerSearchResponse
    {
        return $this->customerService->searchCustomerByMobile($mobile);
    }

    public function getCustomersByType(int $type, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->customerService->getCustomersByType($type, $page, $icpp);
    }

    public function getCustomersByDateRange(string $fromDate, string $toDate, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->customerService->getCustomersByDateRange($fromDate, $toDate, $page, $icpp);
    }

    public function getAllCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->customerService->getAllCustomers($page, $icpp);
    }

    public function validateSearchRequest(array $searchParams): bool
    {
        return $this->customerService->validateSearchRequest($searchParams);
    }

    public function getSearchRequestErrors(array $searchParams): array
    {
        if (method_exists($this->customerService, 'getSearchRequestErrors')) {
            return $this->customerService->getSearchRequestErrors($searchParams);
        }
        $ok = $this->validateSearchRequest($searchParams);
        return $ok ? [] : ['general' => 'Invalid search request'];
    }

    public function createEmptySearchResponse(): CustomerSearchResponse
    {
        // v3-compatible empty shape
        return new CustomerSearchResponse([
            'code' => 1,
            'data' => [],
            'paginator' => ['next' => null],
        ]);
    }

    public function createCustomerSearchResponse(array $apiResponse): CustomerSearchResponse
    {
        // Accept raw v3 {code,data,paginator}, legacy {code,data{customers,totalPages}}, or normalized {customers,...}
        if (class_exists(CustomerSearchResponse::class) && method_exists(CustomerSearchResponse::class, 'createFromApiResponse')) {
            return CustomerSearchResponse::createFromApiResponse($apiResponse);
        }
        return new CustomerSearchResponse($apiResponse);
    }

    public function addCustomer(array $customerData): CustomerAddResponse
    {
        return $this->customerService->addCustomer($customerData);
    }

    public function addCustomers(array $customersData): CustomerAddResponse
    {
        return $this->customerService->addCustomers($customersData);
    }

    public function createCustomerAddResponse(array $apiResponse): CustomerAddResponse
    {
        $repo = $this->repo();
        if ($repo && method_exists($repo, 'createCustomerAddResponse')) {
            return $repo->createCustomerAddResponse($apiResponse);
        }
        if (method_exists(CustomerAddResponse::class, 'createFromApiResponse')) {
            return CustomerAddResponse::createFromApiResponse($apiResponse);
        }
        return new CustomerAddResponse($apiResponse);
    }

    public function validateAddRequest(array $customerData): bool
    {
        return $this->customerService->validateAddRequest($customerData);
    }

    public function getAddRequestErrors(array $customerData): array
    {
        if (method_exists($this->customerService, 'getAddRequestErrors')) {
            return $this->customerService->getAddRequestErrors($customerData);
        }
        $ok = $this->validateAddRequest($customerData);
        return $ok ? [] : ['general' => 'Invalid add request'];
    }

    public function validateBatchAddRequest(array $customersData): bool
    {
        if (method_exists($this->customerService, 'validateBatchAddRequest')) {
            return $this->customerService->validateBatchAddRequest($customersData);
        }
        return true;
    }

    // Cache API used by CustomerModule (v3 modules call API directly; keep no-op compatibility)
    public function getCachedCustomers(array $searchParams): ?array { return null; }
    public function cacheCustomers(array $searchParams, array $data, int $ttl = 3600): bool { return false; }
    public function getCustomerCacheStatus(): array { return ['available' => false]; }
    public function clearAllCustomerCache(): bool { return true; }
    public function isCustomerCacheAvailable(): bool { return false; }
}
