<?php

namespace Puleeno\NhanhVn\Services;

use Puleeno\NhanhVn\Repositories\CustomerRepository;
use Puleeno\NhanhVn\Entities\Customer\Customer;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchRequest;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse;
use Puleeno\NhanhVn\Entities\Customer\CustomerAddRequest;
use Puleeno\NhanhVn\Entities\Customer\CustomerAddResponse;

/**
 * Customer Service
 *
 * Handles business logic for customer-related operations
 *
 * @package Puleeno\NhanhVn\Services
 */
class CustomerService
{
    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * Constructor
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Search customers
     *
     * @param array $searchParams
     * @return CustomerSearchResponse
     * @throws \InvalidArgumentException
     */
    public function searchCustomers(array $searchParams): CustomerSearchResponse
    {
        $request = $this->customerRepository->createCustomerSearchRequest($searchParams);

        if (!$request->isValid()) {
            throw new \InvalidArgumentException(
                'Dữ liệu tìm kiếm không hợp lệ: ' . json_encode($request->getErrors())
            );
        }

        // TODO: Implement actual API call to Nhanh.vn
        // For now, return mock data
        $mockResponse = $this->getMockSearchResponse($request);

        return $this->customerRepository->createCustomerSearchResponse($mockResponse);
    }

    /**
     * Search customer by ID
     *
     * @param int $customerId
     * @return CustomerSearchResponse
     */
    public function searchCustomerById(int $customerId): CustomerSearchResponse
    {
        return $this->searchCustomers(['id' => $customerId]);
    }

    /**
     * Search customer by mobile
     *
     * @param string $mobile
     * @return CustomerSearchResponse
     */
    public function searchCustomerByMobile(string $mobile): CustomerSearchResponse
    {
        return $this->searchCustomers(['mobile' => $mobile]);
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
        return $this->searchCustomers([
            'type' => $type,
            'page' => $page,
            'icpp' => $icpp
        ]);
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
        return $this->searchCustomers([
            'updatedAtFrom' => $fromDate,
            'updatedAtTo' => $toDate,
            'page' => $page,
            'icpp' => $icpp
        ]);
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
        return $this->searchCustomers([
            'page' => $page,
            'icpp' => $icpp
        ]);
    }

    /**
     * Validate search request
     *
     * @param array $searchParams
     * @return bool
     */
    public function validateSearchRequest(array $searchParams): bool
    {
        try {
            $request = $this->customerRepository->createCustomerSearchRequest($searchParams);
            return $request->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get mock search response for development/testing
     *
     * @param CustomerSearchRequest $request
     * @return array
     */
    private function getMockSearchResponse(CustomerSearchRequest $request): array
    {
        $page = $request->getPage();
        $icpp = $request->getIcpp();

        // Generate mock customers based on request
        $customers = [];
        $totalCustomers = min($icpp, 25); // Mock total for demo

        for ($i = 0; $i < $totalCustomers; $i++) {
            $customerId = ($page - 1) * $icpp + $i + 1;

            $customers[] = [
                'id' => $customerId,
                'type' => $request->getType() ?? rand(1, 3),
                'name' => 'Khách hàng ' . $customerId,
                'mobile' => '098' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'email' => 'customer' . $customerId . '@example.com',
                'gender' => rand(1, 2),
                'address' => 'Địa chỉ ' . $customerId,
                'birthday' => '1990-01-01',
                'code' => 'KH' . str_pad($customerId, 6, '0', STR_PAD_LEFT),
                'level' => 'Thành viên',
                'group' => 'Nhóm A',
                'totalMoney' => rand(100000, 10000000),
                'points' => rand(0, 1000),
                'cityLocationId' => rand(1, 63),
                'districtLocationId' => rand(1, 100),
                'wardLocationId' => rand(1, 100),
                'saleName' => 'Nhân viên ' . rand(1, 10),
                'startedDate' => '2020-01-01',
                'taxCode' => '',
                'businessName' => '',
                'businessAddress' => ''
            ];
        }

        return [
            'code' => 1,
            'data' => [
                'totalPages' => max(1, ceil($totalCustomers / $icpp)),
                'customers' => $customers
            ]
        ];
    }

    /**
     * Thêm một khách hàng mới
     *
     * @param array $customerData Dữ liệu khách hàng
     * @return CustomerAddResponse Response từ API
     * @throws \InvalidArgumentException Khi dữ liệu không hợp lệ
     */
    public function addCustomer(array $customerData): CustomerAddResponse
    {
        $request = $this->customerRepository->createCustomerAddRequest($customerData);

        if (!$request->isValid()) {
            throw new \InvalidArgumentException(
                'Dữ liệu khách hàng không hợp lệ: ' . json_encode($request->getErrors())
            );
        }

        // TODO: Implement actual API call to Nhanh.vn
        // For now, return mock response
        $mockResponse = $this->getMockAddResponse($request);

        return $this->customerRepository->createCustomerAddResponse($mockResponse);
    }

    /**
     * Thêm nhiều khách hàng cùng lúc
     *
     * @param array $customersData Mảng dữ liệu khách hàng
     * @return CustomerAddResponse Response từ API
     * @throws \InvalidArgumentException Khi dữ liệu không hợp lệ
     */
    public function addCustomers(array $customersData): CustomerAddResponse
    {
        // Validate batch size limit (max 50 customers per request)
        if (count($customersData) > 50) {
            throw new \InvalidArgumentException(
                'Mỗi request chỉ được gửi tối đa 50 khách hàng'
            );
        }

        $requests = $this->customerRepository->createCustomerAddRequests($customersData);

        // Validate all requests
        foreach ($requests as $request) {
            if (!$request->isValid()) {
                throw new \InvalidArgumentException(
                    'Dữ liệu khách hàng không hợp lệ: ' . json_encode($request->getErrors())
                );
            }
        }

        // TODO: Implement actual API call to Nhanh.vn
        // For now, return mock response
        $mockResponse = $this->getMockBatchAddResponse($requests);

        return $this->customerRepository->createCustomerAddResponse($mockResponse);
    }

    /**
     * Validate add customer request
     *
     * @param array $customerData
     * @return bool
     */
    public function validateAddRequest(array $customerData): bool
    {
        try {
            $request = $this->customerRepository->createCustomerAddRequest($customerData);
            return $request->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate batch add customers request
     *
     * @param array $customersData
     * @return bool
     */
    public function validateBatchAddRequest(array $customersData): bool
    {
        try {
            if (count($customersData) > 50) {
                return false;
            }

            $requests = $this->customerRepository->createCustomerAddRequests($customersData);
            foreach ($requests as $request) {
                if (!$request->isValid()) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get mock add response for development/testing
     *
     * @param CustomerAddRequest $request
     * @return array
     */
    private function getMockAddResponse(CustomerAddRequest $request): array
    {
        return [
            'code' => 1,
            'messages' => [],
            'data' => [
                [
                    'id' => rand(1000000, 9999999),
                    'mobile' => $request->getMobile(),
                    'name' => $request->getName(),
                    'type' => $request->getType() ?? 1,
                    'status' => 'active'
                ]
            ]
        ];
    }

    /**
     * Get mock batch add response for development/testing
     *
     * @param array $requests
     * @return array
     */
    private function getMockBatchAddResponse(array $requests): array
    {
        $data = [];

        foreach ($requests as $request) {
            $data[] = [
                'id' => rand(1000000, 9999999),
                'mobile' => $request->getMobile(),
                'name' => $request->getName(),
                'type' => $request->getType() ?? 1,
                'status' => 'active'
            ];
        }

        return [
            'code' => 1,
            'messages' => [],
            'data' => $data
        ];
    }
}
