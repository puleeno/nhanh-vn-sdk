<?php

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Managers\CustomerManager;
use Puleeno\NhanhVn\Services\HttpService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Entities\Customer\CustomerSearchResponse;
use Puleeno\NhanhVn\Entities\Customer\Customer;
use Puleeno\NhanhVn\Entities\Customer\CustomerAddResponse;
use Exception;

/**
 * Customer Module - Quản lý các thao tác liên quan đến khách hàng
 *
 * Module này cung cấp các method để tương tác với API khách hàng của Nhanh.vn
 * bao gồm: tìm kiếm, lấy chi tiết, quản lý cache và các thao tác khác
 */
class CustomerModule
{
    /** @var CustomerManager Quản lý business logic khách hàng */
    protected CustomerManager $customerManager;

    /** @var HttpService Service gọi HTTP API */
    protected HttpService $httpService;

    /** @var LoggerInterface Logger để ghi log */
    protected LoggerInterface $logger;

    /**
     * Constructor của CustomerModule
     *
     * @param CustomerManager $customerManager Quản lý business logic khách hàng
     * @param HttpService $httpService Service gọi HTTP API
     * @param LoggerInterface $logger Logger để ghi log
     */
    public function __construct(CustomerManager $customerManager, HttpService $httpService, LoggerInterface $logger)
    {
        $this->customerManager = $customerManager;
        $this->httpService = $httpService;
        $this->logger = $logger;
    }

    /**
     * Tìm kiếm khách hàng theo các tiêu chí
     *
     * @param array $searchParams Các tiêu chí tìm kiếm
     * @return CustomerSearchResponse Response chứa danh sách khách hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function search(array $searchParams): CustomerSearchResponse
    {
        // DEBUG: Log search criteria
        $this->logger->debug("CustomerModule::search() called with criteria", $searchParams);

        try {
            // Chuẩn bị search criteria theo API Nhanh.vn
            $searchData = $this->prepareSearchCriteria($searchParams);

            // Gọi API Nhanh.vn
            $this->logger->info("CustomerModule::search() calling Nhanh.vn API", $searchData);

            $response = $this->httpService->callApi('/customer/search', $searchData);

            // Parse response
            if (!isset($response['data'])) {
                $this->logger->warning("CustomerModule::search() - Response không có customer data");
                // Giải phóng memory
                unset($response, $searchData);

                // Return empty response for consistency
                return $this->customerManager->createEmptySearchResponse();
            }

            $this->logger->info("CustomerModule::search() - Found customers from API", [
                'totalPages' => $response['data']['totalPages'] ?? 0,
                'customerCount' => count($response['data']['customers'] ?? [])
            ]);

            // Create response entity
            $searchResponse = $this->customerManager->createCustomerSearchResponse($response);

            $this->logger->info("CustomerModule::search() - Created search response", [
                'totalCustomers' => $searchResponse->getTotalCustomers(),
                'totalPages' => $searchResponse->getTotalPages()
            ]);

            // Giải phóng memory trước khi return
            unset($response, $searchData);

            return $searchResponse;

        } catch (Exception $e) {
            $this->logger->error("CustomerModule::search() error", ['error' => $e->getMessage()]);
            // Giải phóng memory trong trường hợp lỗi
            if (isset($response)) unset($response);
            if (isset($searchData)) unset($searchData);
            throw $e;
        }
    }

    /**
     * Chuẩn bị search criteria theo format API Nhanh.vn
     *
     * @param array $searchParams Các tiêu chí tìm kiếm từ người dùng
     * @return array Các tiêu chí đã được format theo chuẩn API Nhanh.vn
     */
    private function prepareSearchCriteria(array $searchParams): array
    {
        $searchData = [];

        // Các field cơ bản
        if (isset($searchParams['page'])) {
            $searchData['page'] = (int) $searchParams['page'];
        }

        if (isset($searchParams['icpp'])) {
            $searchData['icpp'] = min((int) $searchParams['icpp'], 50); // Tối đa 50
        }

        if (isset($searchParams['id'])) {
            $searchData['id'] = (int) $searchParams['id'];
        }

        if (isset($searchParams['mobile'])) {
            $searchData['mobile'] = $searchParams['mobile'];
        }

        if (isset($searchParams['type'])) {
            $searchData['type'] = (int) $searchParams['type'];
        }

        if (isset($searchParams['lastBoughtDateFrom'])) {
            $searchData['lastBoughtDateFrom'] = $searchParams['lastBoughtDateFrom'];
        }

        if (isset($searchParams['lastBoughtDateTo'])) {
            $searchData['lastBoughtDateTo'] = $searchParams['lastBoughtDateTo'];
        }

        if (isset($searchParams['updatedAtFrom'])) {
            $searchData['updatedAtFrom'] = $searchParams['updatedAtFrom'];
        }

        if (isset($searchParams['updatedAtTo'])) {
            $searchData['updatedAtTo'] = $searchParams['updatedAtTo'];
        }

        return $searchData;
    }

    /**
     * Tìm kiếm khách hàng theo ID
     *
     * @param int $customerId ID khách hàng trên Nhanh.vn
     * @return CustomerSearchResponse Response chứa thông tin khách hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchById(int $customerId): CustomerSearchResponse
    {
        $this->logger->debug("CustomerModule::searchById() called with customerId", ['customerId' => $customerId]);

        return $this->search(['id' => $customerId]);
    }

    /**
     * Tìm kiếm khách hàng theo số điện thoại
     *
     * @param string $mobile Số điện thoại khách hàng
     * @return CustomerSearchResponse Response chứa thông tin khách hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchByMobile(string $mobile): CustomerSearchResponse
    {
        $this->logger->debug("CustomerModule::searchByMobile() called with mobile", ['mobile' => $mobile]);

        return $this->search(['mobile' => $mobile]);
    }

    /**
     * Lấy khách hàng theo loại
     *
     * @param int $type Loại khách hàng (1: Khách lẻ, 2: Khách sỉ, 3: Đại lý)
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $icpp Số lượng khách hàng trên 1 trang (mặc định: 10)
     * @return CustomerSearchResponse Response chứa danh sách khách hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getByType(int $type, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        $this->logger->debug("CustomerModule::getByType() called", [
            'type' => $type,
            'page' => $page,
            'icpp' => $icpp
        ]);

        return $this->search([
            'type' => $type,
            'page' => $page,
            'icpp' => $icpp
        ]);
    }

    /**
     * Lấy khách hàng theo khoảng thời gian cập nhật
     *
     * @param string $fromDate Ngày bắt đầu (Y-m-d H:i:s)
     * @param string $toDate Ngày kết thúc (Y-m-d H:i:s)
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $icpp Số lượng khách hàng trên 1 trang (mặc định: 10)
     * @return CustomerSearchResponse Response chứa danh sách khách hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getByDateRange(string $fromDate, string $toDate, int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        $this->logger->debug("CustomerModule::getByDateRange() called", [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'page' => $page,
            'icpp' => $icpp
        ]);

        return $this->search([
            'updatedAtFrom' => $fromDate,
            'updatedAtTo' => $toDate,
            'page' => $page,
            'icpp' => $icpp
        ]);
    }

    /**
     * Lấy tất cả khách hàng với phân trang
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $icpp Số lượng khách hàng trên 1 trang (mặc định: 10)
     * @return CustomerSearchResponse Response chứa danh sách khách hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getAll(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        $this->logger->debug("CustomerModule::getAll() called", [
            'page' => $page,
            'icpp' => $icpp
        ]);

        return $this->search([
            'page' => $page,
            'icpp' => $icpp
        ]);
    }

    /**
     * Lấy khách hàng lẻ
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $icpp Số lượng khách hàng trên 1 trang (mặc định: 10)
     * @return CustomerSearchResponse Response chứa danh sách khách hàng lẻ
     */
    public function getRetailCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->getByType(1, $page, $icpp); // TYPE_RETAIL = 1
    }

    /**
     * Lấy khách hàng sỉ
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $icpp Số lượng khách hàng trên 1 trang (mặc định: 10)
     * @return CustomerSearchResponse Response chứa danh sách khách hàng sỉ
     */
    public function getWholesaleCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->getByType(2, $page, $icpp); // TYPE_WHOLESALE = 2
    }

    /**
     * Lấy khách hàng đại lý
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $icpp Số lượng khách hàng trên 1 trang (mặc định: 10)
     * @return CustomerSearchResponse Response chứa danh sách khách hàng đại lý
     */
    public function getAgentCustomers(int $page = 1, int $icpp = 10): CustomerSearchResponse
    {
        return $this->getByType(3, $page, $icpp); // TYPE_AGENT = 3
    }

    /**
     * Validate search request data
     *
     * @param array $searchParams Dữ liệu tìm kiếm cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateSearchRequest(array $searchParams): bool
    {
        try {
            $isValid = $this->customerManager->validateSearchRequest($searchParams);

            $this->logger->debug("CustomerModule::validateSearchRequest()", [
                'params' => $searchParams,
                'isValid' => $isValid
            ]);

            return $isValid;
        } catch (Exception $e) {
            $this->logger->error("CustomerModule::validateSearchRequest() error", [
                'error' => $e->getMessage(),
                'params' => $searchParams
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách lỗi validation của search request
     *
     * @param array $searchParams Dữ liệu tìm kiếm cần validate
     * @return array Mảng chứa các lỗi validation
     */
    public function getSearchRequestErrors(array $searchParams): array
    {
        try {
            // Create a temporary request to get validation errors
            $customerRepository = new \Puleeno\NhanhVn\Repositories\CustomerRepository();
            $request = $customerRepository->createCustomerSearchRequest($searchParams);

            if ($request->isValid()) {
                return [];
            }

            return $request->getErrors();
        } catch (Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Lấy instance của CustomerManager
     *
     * @return CustomerManager Instance của CustomerManager
     */
    public function getManager(): CustomerManager
    {
        return $this->customerManager;
    }

    /**
     * Helper method để giải phóng memory cho cached data
     *
     * @param array|null $cachedData Dữ liệu cache cần xử lý
     * @param string $methodName Tên method để tạo entities
     * @return array Mảng các entities đã được tạo
     */
    private function createEntitiesWithMemoryManagement(?array $cachedData, string $methodName): array
    {
        if ($cachedData === null) {
            return [];
        }

        try {
            $result = $this->customerManager->$methodName($cachedData);
            // Giải phóng memory ngay lập tức
            unset($cachedData);
            return $result;
        } catch (Exception $e) {
            $this->logger->error("CustomerModule::$methodName() - Error creating entities", ['error' => $e->getMessage()]);
            unset($cachedData);
            return [];
        }
    }

    /**
     * Helper method để giải phóng memory cho API response data
     *
     * @param array $responseData Dữ liệu response từ API
     * @param string $methodName Tên method để tạo entities
     * @param string $dataKey Key chứa data trong response (mặc định: 'data')
     * @return array Mảng các entities đã được tạo
     */
    private function createEntitiesFromApiResponse(array $responseData, string $methodName, string $dataKey = 'data'): array
    {
        if (!isset($responseData[$dataKey]) || !is_array($responseData[$dataKey])) {
            unset($responseData);
            return [];
        }

        $data = $responseData[$dataKey];
        $entities = [];

        foreach ($data as $itemData) {
            try {
                $entity = $this->customerManager->$methodName($itemData);
                $entities[] = $entity;
                // Giải phóng memory ngay sau khi tạo entity
                unset($itemData);
            } catch (Exception $e) {
                $this->logger->error("CustomerModule::$methodName() - Error creating entity", ['error' => $e->getMessage()]);
                // Skip invalid data
            }
        }

        // Giải phóng memory
        unset($responseData, $data);

        return $entities;
    }

    /**
     * Thêm một khách hàng mới
     *
     * @param array $customerData Dữ liệu khách hàng
     * @return CustomerAddResponse Response từ API
     * @throws Exception Khi có lỗi xảy ra trong quá trình thêm khách hàng
     */
    public function add(array $customerData): CustomerAddResponse
    {
        $this->logger->debug("CustomerModule::add() called with data", $customerData);

        try {
            // Validate request data
            if (!$this->customerManager->validateAddRequest($customerData)) {
                $errors = $this->getAddRequestErrors($customerData);
                $this->logger->warning("CustomerModule::add() - Validation failed", ['errors' => $errors]);
                
                throw new Exception('Dữ liệu khách hàng không hợp lệ: ' . json_encode($errors));
            }

            // Chuẩn bị data cho API Nhanh.vn
            $apiData = $this->prepareAddData($customerData);

            // Gọi API Nhanh.vn
            $this->logger->info("CustomerModule::add() calling Nhanh.vn API", $apiData);

            $response = $this->httpService->callApi('/customer/add', $apiData);

            // Parse response
            if (!isset($response['code'])) {
                $this->logger->warning("CustomerModule::add() - Response không có code");
                throw new Exception('Response từ API không hợp lệ');
            }

            $this->logger->info("CustomerModule::add() - API response received", [
                'code' => $response['code'],
                'success' => $response['code'] === 1
            ]);

            // Create response entity
            $addResponse = $this->customerManager->createCustomerAddResponse($response);

            $this->logger->info("CustomerModule::add() - Created add response", [
                'success' => $addResponse->isSuccess(),
                'customerCount' => $addResponse->getSuccessCount()
            ]);

            // Giải phóng memory trước khi return
            unset($response, $apiData);

            return $addResponse;

        } catch (Exception $e) {
            $this->logger->error("CustomerModule::add() error", ['error' => $e->getMessage()]);
            // Giải phóng memory trong trường hợp lỗi
            if (isset($response)) unset($response);
            if (isset($apiData)) unset($apiData);
            throw $e;
        }
    }

    /**
     * Thêm nhiều khách hàng cùng lúc
     *
     * @param array $customersData Mảng dữ liệu khách hàng
     * @return CustomerAddResponse Response từ API
     * @throws Exception Khi có lỗi xảy ra trong quá trình thêm khách hàng
     */
    public function addBatch(array $customersData): CustomerAddResponse
    {
        $this->logger->debug("CustomerModule::addBatch() called with data", [
            'customerCount' => count($customersData)
        ]);

        try {
            // Validate batch size limit
            if (count($customersData) > 50) {
                throw new Exception('Mỗi request chỉ được gửi tối đa 50 khách hàng');
            }

            // Validate request data
            if (!$this->customerManager->validateBatchAddRequest($customersData)) {
                $errors = $this->getBatchAddRequestErrors($customersData);
                $this->logger->warning("CustomerModule::addBatch() - Validation failed", ['errors' => $errors]);
                
                throw new Exception('Dữ liệu khách hàng không hợp lệ: ' . json_encode($errors));
            }

            // Chuẩn bị data cho API Nhanh.vn
            $apiData = $this->prepareBatchAddData($customersData);

            // Gọi API Nhanh.vn
            $this->logger->info("CustomerModule::addBatch() calling Nhanh.vn API", [
                'customerCount' => count($customersData)
            ]);

            $response = $this->httpService->callApi('/customer/add', $apiData);

            // Parse response
            if (!isset($response['code'])) {
                $this->logger->warning("CustomerModule::addBatch() - Response không có code");
                throw new Exception('Response từ API không hợp lệ');
            }

            $this->logger->info("CustomerModule::addBatch() - API response received", [
                'code' => $response['code'],
                'success' => $response['code'] === 1,
                'processedCount' => count($response['data'] ?? [])
            ]);

            // Create response entity
            $addResponse = $this->customerManager->createCustomerAddResponse($response);

            $this->logger->info("CustomerModule::addBatch() - Created add response", [
                'success' => $addResponse->isSuccess(),
                'customerCount' => $addResponse->getSuccessCount()
            ]);

            // Giải phóng memory trước khi return
            unset($response, $apiData);

            return $addResponse;

        } catch (Exception $e) {
            $this->logger->error("CustomerModule::addBatch() error", ['error' => $e->getMessage()]);
            // Giải phóng memory trong trường hợp lỗi
            if (isset($response)) unset($response);
            if (isset($apiData)) unset($apiData);
            throw $e;
        }
    }

    /**
     * Validate add customer request
     *
     * @param array $customerData Dữ liệu khách hàng cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateAddRequest(array $customerData): bool
    {
        $this->logger->debug("CustomerModule::validateAddRequest() called", $customerData);

        try {
            $isValid = $this->customerManager->validateAddRequest($customerData);

            $this->logger->info("CustomerModule::validateAddRequest() result", [
                'params' => $customerData,
                'isValid' => $isValid
            ]);

            return $isValid;
        } catch (Exception $e) {
            $this->logger->error("CustomerModule::validateAddRequest() error", [
                'error' => $e->getMessage(),
                'params' => $customerData
            ]);
            return false;
        }
    }

    /**
     * Validate batch add customers request
     *
     * @param array $customersData Mảng dữ liệu khách hàng cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateBatchAddRequest(array $customersData): bool
    {
        $this->logger->debug("CustomerModule::validateBatchAddRequest() called", [
            'customerCount' => count($customersData)
        ]);

        try {
            $isValid = $this->customerManager->validateBatchAddRequest($customersData);

            $this->logger->info("CustomerModule::validateBatchAddRequest() result", [
                'customerCount' => count($customersData),
                'isValid' => $isValid
            ]);

            return $isValid;
        } catch (Exception $e) {
            $this->logger->error("CustomerModule::validateBatchAddRequest() error", [
                'error' => $e->getMessage(),
                'customerCount' => count($customersData)
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách lỗi validation của add request
     *
     * @param array $customerData Dữ liệu khách hàng cần validate
     * @return array Mảng chứa các lỗi validation
     */
    public function getAddRequestErrors(array $customerData): array
    {
        try {
            // Create a temporary request to get validation errors
            $customerRepository = new \Puleeno\NhanhVn\Repositories\CustomerRepository();
            $request = $customerRepository->createCustomerAddRequest($customerData);

            if ($request->isValid()) {
                return [];
            }

            return $request->getErrors();
        } catch (Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách lỗi validation của batch add request
     *
     * @param array $customersData Mảng dữ liệu khách hàng cần validate
     * @return array Mảng chứa các lỗi validation
     */
    public function getBatchAddRequestErrors(array $customersData): array
    {
        try {
            $errors = [];
            $customerRepository = new \Puleeno\NhanhVn\Repositories\CustomerRepository();

            foreach ($customersData as $index => $customerData) {
                $request = $customerRepository->createCustomerAddRequest($customerData);
                if (!$request->isValid()) {
                    $errors[$index] = $request->getErrors();
                }
            }

            return $errors;
        } catch (Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Chuẩn bị data cho API thêm khách hàng
     *
     * @param array $customerData Dữ liệu khách hàng
     * @return array Dữ liệu đã được format cho API
     */
    private function prepareAddData(array $customerData): array
    {
        $request = $this->customerManager->getManager()->getCustomerRepository()->createCustomerAddRequest($customerData);
        return $request->toApiFormat();
    }

    /**
     * Chuẩn bị data cho API thêm nhiều khách hàng
     *
     * @param array $customersData Mảng dữ liệu khách hàng
     * @return array Dữ liệu đã được format cho API
     */
    private function prepareBatchAddData(array $customersData): array
    {
        $apiData = [];
        $customerRepository = new \Puleeno\NhanhVn\Repositories\CustomerRepository();

        foreach ($customersData as $customerData) {
            $request = $customerRepository->createCustomerAddRequest($customerData);
            $apiData[] = $request->toApiFormat();
        }

        return $apiData;
    }
}
