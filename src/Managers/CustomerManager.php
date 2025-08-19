<?php

namespace Puleeno\NhanhVn\Managers;

use Puleeno\NhanhVn\Services\CustomerService;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse;
use Puleeno\NhanhVn\Entities\Customer\CustomerAddResponse;

/**
 * Customer Manager
 *
 * Orchestrates customer-related operations between different layers
 *
 * @package Puleeno\NhanhVn\Managers
 */
class CustomerManager
{
    /**
     * @var CustomerService
     */
    private CustomerService $customerService;

    /**
     * Constructor
     *
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Search customers
     *
     * @param array $searchParams
     * @return CustomerSearchResponse
     */
    public function searchCustomers(array $searchParams): CustomerSearchResponse
    {
        return $this->customerService->searchCustomers($searchParams);
    }

    /**
     * Search customer by ID
     *
     * @param int $customerId
     * @return CustomerSearchResponse
     */
    public function searchCustomerById(int $customerId): CustomerSearchResponse
    {
        return $this->customerService->searchCustomerById($customerId);
    }

    /**
     * Search customer by mobile
     *
     * @param string $mobile
     * @return CustomerSearchResponse
     */
    public function searchCustomerByMobile(string $mobile): CustomerSearchResponse
    {
        return $this->customerService->searchCustomerByMobile($mobile);
    }

    /**
     * Get customers by type
     *
     * @param int $type
     * @param int $page
     * @param int $icpp
     * @return CustomerSearchResponse
     */
    public function getCustomersByType(int $type, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->customerService->getCustomersByType($type, $page, $icpp);
    }

    /**
     * Get customers by date range
     *
     * @param string $fromDate
     * @param string $toDate
     * @param int $page
     * @param int $icpp
     * @return CustomerSearchResponse
     */
    public function getCustomersByDateRange(string $fromDate, string $toDate, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->customerService->getCustomersByDateRange($fromDate, $toDate, $page, $icpp);
    }

    /**
     * Get all customers with pagination
     *
     * @param int $page
     * @param int $icpp
     * @return CustomerSearchResponse
     */
    public function getAllCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->customerService->getAllCustomers($page, $icpp);
    }

    /**
     * Validate search request
     *
     * @param array $searchParams
     * @return bool
     */
    public function validateSearchRequest(array $searchParams): bool
    {
        return $this->customerService->validateSearchRequest($searchParams);
    }

    /**
     * Create empty search response
     *
     * @return CustomerSearchResponse
     */
    public function createEmptySearchResponse(): CustomerSearchResponse
    {
        return CustomerSearchResponse::createSuccessResponse([
            'totalPages' => 0,
            'customers' => []
        ]);
    }

    /**
     * Create customer search response from API response
     *
     * @param array $apiResponse
     * @return CustomerSearchResponse
     */
    public function createCustomerSearchResponse(array $apiResponse): CustomerSearchResponse
    {
        return CustomerSearchResponse::createFromApiResponse($apiResponse);
    }

    /**
     * Thêm một khách hàng mới
     *
     * @param array $customerData Dữ liệu khách hàng
     * @return CustomerAddResponse Response từ API
     */
    public function addCustomer(array $customerData): CustomerAddResponse
    {
        return $this->customerService->addCustomer($customerData);
    }

    /**
     * Thêm nhiều khách hàng cùng lúc
     *
     * @param array $customersData Mảng dữ liệu khách hàng
     * @return CustomerAddResponse Response từ API
     */
    public function addCustomers(array $customersData): CustomerAddResponse
    {
        return $this->customerService->addCustomers($customersData);
    }

    /**
     * Validate add customer request
     *
     * @param array $customerData
     * @return bool
     */
    public function validateAddRequest(array $customerData): bool
    {
        return $this->customerService->validateAddRequest($customerData);
    }

    /**
     * Validate batch add customers request
     *
     * @param array $customersData
     * @return bool
     */
    public function validateBatchAddRequest(array $customersData): bool
    {
        return $this->customerService->validateBatchAddRequest($customersData);
    }
}
