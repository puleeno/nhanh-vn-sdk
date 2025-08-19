<?php

namespace Puleeno\NhanhVn\Repositories;

use Puleeno\NhanhVn\Entities\Customer\Customer;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchRequest;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse;

/**
 * Customer Repository
 *
 * Handles data access for customer-related operations
 *
 * @package Puleeno\NhanhVn\Repositories
 */
class CustomerRepository
{
    /**
     * Create a Customer entity
     *
     * @param array $data
     * @return Customer
     */
    public function createCustomer(array $data): Customer
    {
        return new Customer($data);
    }

    /**
     * Create multiple Customer entities
     *
     * @param array $customersData
     * @return Customer[]
     */
    public function createCustomers(array $customersData): array
    {
        return array_map([$this, 'createCustomer'], $customersData);
    }

    /**
     * Create a CustomerSearchRequest entity
     *
     * @param array $data
     * @return CustomerSearchRequest
     */
    public function createCustomerSearchRequest(array $data): CustomerSearchRequest
    {
        return new CustomerSearchRequest($data);
    }

    /**
     * Create a CustomerSearchResponse entity
     *
     * @param array $data
     * @return CustomerSearchResponse
     */
    public function createCustomerSearchResponse(array $data): CustomerSearchResponse
    {
        return new CustomerSearchResponse($data);
    }

    /**
     * Create Customer entities from search response
     *
     * @param CustomerSearchResponse $response
     * @return Customer[]
     */
    public function createCustomersFromSearchResponse(CustomerSearchResponse $response): array
    {
        $customersData = $response->getCustomers();
        return $this->createCustomers($customersData);
    }
}
