<?php

namespace Puleeno\NhanhVn\Services;

use Puleeno\NhanhVn\Repositories\CustomerRepository;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse;
use Puleeno\NhanhVn\Entities\Customer\CustomerAddResponse;

/**
 * Customer Service (legacy helper layer).
 *
 * NOTE: v3 module flows call HttpService directly. This service remains for
 * backward compatibility and basic validation/factory delegation.
 */
class CustomerService
{
    /** @var CustomerRepository */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getRepository(): CustomerRepository
    {
        return $this->customerRepository;
    }

    public function searchCustomers(array $searchParams): CustomerSearchResponse
    {
        // legacy mock behavior removed in v3; return empty compatible response
        return $this->customerRepository->createCustomerSearchResponse([
            'code' => 1,
            'data' => [],
            'paginator' => ['next' => null],
        ]);
    }

    public function searchCustomerById(int $customerId): CustomerSearchResponse
    {
        return $this->searchCustomers(['id' => $customerId]);
    }

    public function searchCustomerByMobile(string $mobile): CustomerSearchResponse
    {
        return $this->searchCustomers(['mobile' => $mobile]);
    }

    public function getCustomersByType(int $type, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->searchCustomers(['type' => $type, 'page' => $page, 'icpp' => $icpp]);
    }

    public function getCustomersByDateRange(string $fromDate, string $toDate, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->searchCustomers([
            'updatedAtFrom' => $fromDate,
            'updatedAtTo' => $toDate,
            'page' => $page,
            'icpp' => $icpp,
        ]);
    }

    public function getAllCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->searchCustomers(['page' => $page, 'icpp' => $icpp]);
    }

    public function validateSearchRequest(array $searchParams): bool
    {
        try {
            if (method_exists($this->customerRepository, 'createCustomerSearchRequest')) {
                $request = $this->customerRepository->createCustomerSearchRequest($searchParams);
                return method_exists($request, 'isValid') ? $request->isValid() : true;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getSearchRequestErrors(array $searchParams): array
    {
        try {
            if (method_exists($this->customerRepository, 'createCustomerSearchRequest')) {
                $request = $this->customerRepository->createCustomerSearchRequest($searchParams);
                if (method_exists($request, 'getErrors')) {
                    return $request->getErrors();
                }
            }
            return [];
        } catch (\Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    public function addCustomer(array $customerData): CustomerAddResponse
    {
        return $this->customerRepository->createCustomerAddResponse(['code' => 1, 'data' => []]);
    }

    public function addCustomers(array $customersData): CustomerAddResponse
    {
        return $this->customerRepository->createCustomerAddResponse(['code' => 1, 'data' => []]);
    }

    public function validateAddRequest(array $customerData): bool
    {
        try {
            if (method_exists($this->customerRepository, 'createCustomerAddRequest')) {
                $request = $this->customerRepository->createCustomerAddRequest($customerData);
                return method_exists($request, 'isValid') ? $request->isValid() : true;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAddRequestErrors(array $customerData): array
    {
        try {
            if (method_exists($this->customerRepository, 'createCustomerAddRequest')) {
                $request = $this->customerRepository->createCustomerAddRequest($customerData);
                if (method_exists($request, 'getErrors')) {
                    return $request->getErrors();
                }
            }
            return [];
        } catch (\Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    public function validateBatchAddRequest(array $customersData): bool
    {
        return is_array($customersData) && count($customersData) <= 100;
    }
}
