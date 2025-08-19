<?php

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Managers\OrderManager;
use Puleeno\NhanhVn\Services\HttpService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Entities\Order\Order;
use Puleeno\NhanhVn\Entities\Order\OrderSearchResponse;
use Illuminate\Support\Collection;
use Exception;

/**
 * Order Module - Quản lý các thao tác liên quan đến đơn hàng
 *
 * Module này cung cấp các method để tương tác với API đơn hàng của Nhanh.vn
 * bao gồm: tìm kiếm, lấy chi tiết, quản lý cache và các thao tác khác
 */
class OrderModule
{
    /** @var OrderManager Quản lý business logic đơn hàng */
    protected OrderManager $orderManager;

    /** @var HttpService Service gọi HTTP API */
    protected HttpService $httpService;

    /** @var LoggerInterface Logger để ghi log */
    protected LoggerInterface $logger;

    /**
     * Constructor của OrderModule
     *
     * @param OrderManager $orderManager Quản lý business logic đơn hàng
     * @param HttpService $httpService Service gọi HTTP API
     * @param LoggerInterface $logger Logger để ghi log
     */
    public function __construct(OrderManager $orderManager, HttpService $httpService, LoggerInterface $logger)
    {
        $this->orderManager = $orderManager;
        $this->httpService = $httpService;
        $this->logger = $logger;
    }

    /**
     * Tìm kiếm đơn hàng theo các tiêu chí
     *
     * @param array $searchParams Các tiêu chí tìm kiếm
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function search(array $searchParams = []): OrderSearchResponse
    {
        // DEBUG: Log search criteria
        $this->logger->debug("OrderModule::search() called with criteria", $searchParams);

        try {
            // Kiểm tra cache trước
            $cachedData = $this->orderManager->getCachedOrders($searchParams);
            if ($cachedData !== null) {
                $this->logger->info("OrderModule::search() - Using cached data");
                return $this->createEntitiesWithMemoryManagement($cachedData, 'createOrderSearchResponse');
            }

            // Chuẩn bị search criteria theo API Nhanh.vn
            $searchData = $this->prepareSearchCriteria($searchParams);

            // Gọi API Nhanh.vn
            $this->logger->info("OrderModule::search() calling Nhanh.vn API", $searchData);

            $response = $this->httpService->callApi('/order/index', $searchData);

            // Parse response
            if (!isset($response['data']) || !isset($response['data']['orders'])) {
                $this->logger->warning("OrderModule::search() - Response không có orders data");
                // Giải phóng memory
                unset($response, $searchData);

                // Return empty response for consistency
                return $this->orderManager->createEmptySearchResponse();
            }

            $this->logger->info("OrderModule::search() - Found orders from API", [
                'totalPages' => $response['data']['totalPages'] ?? 0,
                'orderCount' => count($response['data']['orders'] ?? [])
            ]);

            // Create response entity
            $searchResponse = $this->orderManager->createOrderSearchResponse($response['data']);

            $this->logger->info("OrderModule::search() - Created search response", [
                'totalOrders' => $searchResponse->getTotalRecords(),
                'totalPages' => $searchResponse->getTotalPages()
            ]);

            // Cache kết quả
            $this->orderManager->cacheOrders($searchParams, $response['data']);

            // Giải phóng memory trước khi return
            unset($response, $searchData);

            return $searchResponse;

        } catch (Exception $e) {
            $this->logger->error("OrderModule::search() error", ['error' => $e->getMessage()]);
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

        if (isset($searchParams['limit'])) {
            $searchData['icpp'] = min((int) $searchParams['limit'], 100); // Tối đa 100
        }

        if (isset($searchParams['fromDate'])) {
            $searchData['fromDate'] = $searchParams['fromDate'];
        }

        if (isset($searchParams['toDate'])) {
            $searchData['toDate'] = $searchParams['toDate'];
        }

        if (isset($searchParams['id'])) {
            $searchData['id'] = (int) $searchParams['id'];
        }

        if (isset($searchParams['customerMobile'])) {
            $searchData['customerMobile'] = $searchParams['customerMobile'];
        }

        if (isset($searchParams['customerId'])) {
            $searchData['customerId'] = (int) $searchParams['customerId'];
        }

        if (isset($searchParams['statuses'])) {
            $searchData['statuses'] = $searchParams['statuses'];
        }

        if (isset($searchParams['fromDeliveryDate'])) {
            $searchData['fromDeliveryDate'] = $searchParams['fromDeliveryDate'];
        }

        if (isset($searchParams['toDeliveryDate'])) {
            $searchData['toDeliveryDate'] = $searchParams['toDeliveryDate'];
        }

        if (isset($searchParams['carrierId'])) {
            $searchData['carrierId'] = (int) $searchParams['carrierId'];
        }

        if (isset($searchParams['carrierCode'])) {
            $searchData['carrierCode'] = $searchParams['carrierCode'];
        }

        if (isset($searchParams['type'])) {
            $searchData['type'] = (int) $searchParams['type'];
        }

        if (isset($searchParams['customerCityId'])) {
            $searchData['customerCityId'] = (int) $searchParams['customerCityId'];
        }

        if (isset($searchParams['customerDistrictId'])) {
            $searchData['customerDistrictId'] = (int) $searchParams['customerDistrictId'];
        }

        if (isset($searchParams['handoverId'])) {
            $searchData['handoverId'] = (int) $searchParams['handoverId'];
        }

        if (isset($searchParams['depotId'])) {
            $searchData['depotId'] = (int) $searchParams['depotId'];
        }

        if (isset($searchParams['updatedDateTimeFrom'])) {
            $searchData['updatedDateTimeFrom'] = $searchParams['updatedDateTimeFrom'];
        }

        if (isset($searchParams['updatedDateTimeTo'])) {
            $searchData['updatedDateTimeTo'] = $searchParams['updatedDateTimeTo'];
        }

        if (isset($searchParams['dataOptions'])) {
            $searchData['dataOptions'] = $searchParams['dataOptions'];
        }

        return $searchData;
    }

    /**
     * Tìm kiếm đơn hàng theo ID
     *
     * @param int $orderId ID đơn hàng trên Nhanh.vn
     * @return OrderSearchResponse Response chứa thông tin đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchById(int $orderId): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::searchById() called with orderId", ['orderId' => $orderId]);

        return $this->search(['id' => $orderId]);
    }

    /**
     * Tìm kiếm đơn hàng theo số điện thoại khách hàng
     *
     * @param string $mobile Số điện thoại khách hàng
     * @return OrderSearchResponse Response chứa thông tin đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchByCustomerMobile(string $mobile): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::searchByCustomerMobile() called with mobile", ['mobile' => $mobile]);

        return $this->search(['customerMobile' => $mobile]);
    }

    /**
     * Tìm kiếm đơn hàng theo ID khách hàng
     *
     * @param int $customerId ID khách hàng
     * @return OrderSearchResponse Response chứa thông tin đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchByCustomerId(int $customerId): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::searchByCustomerId() called with customerId", ['customerId' => $customerId]);

        return $this->search(['customerId' => $customerId]);
    }

    /**
     * Lấy đơn hàng theo trạng thái
     *
     * @param array $statuses Mảng trạng thái đơn hàng
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getByStatuses(array $statuses, int $page = 1, int $limit = 100): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::getByStatuses() called", [
            'statuses' => $statuses,
            'page' => $page,
            'limit' => $limit
        ]);

        return $this->search([
            'statuses' => $statuses,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Lấy đơn hàng theo loại
     *
     * @param int $type Loại đơn hàng
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getByType(int $type, int $page = 1, int $limit = 100): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::getByType() called", [
            'type' => $type,
            'page' => $page,
            'limit' => $limit
        ]);

        return $this->search([
            'type' => $type,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Lấy đơn hàng theo khoảng thời gian tạo
     *
     * @param string $fromDate Ngày bắt đầu (Y-m-d)
     * @param string $toDate Ngày kết thúc (Y-m-d)
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getByDateRange(string $fromDate, string $toDate, int $page = 1, int $limit = 100): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::getByDateRange() called", [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'page' => $page,
            'limit' => $limit
        ]);

        return $this->search([
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Lấy đơn hàng theo khoảng thời gian giao hàng
     *
     * @param string $fromDate Ngày bắt đầu (Y-m-d)
     * @param string $toDate Ngày kết thúc (Y-m-d)
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getByDeliveryDateRange(string $fromDate, string $toDate, int $page = 1, int $limit = 100): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::getByDeliveryDateRange() called", [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'page' => $page,
            'limit' => $limit
        ]);

        return $this->search([
            'fromDeliveryDate' => $fromDate,
            'toDeliveryDate' => $toDate,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Lấy đơn hàng theo khoảng thời gian cập nhật
     *
     * @param string $fromDateTime Thời gian bắt đầu (Y-m-d H:i:s)
     * @param string $toDateTime Thời gian kết thúc (Y-m-d H:i:s)
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getByUpdatedDateTimeRange(string $fromDateTime, string $toDateTime, int $page = 1, int $limit = 100): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::getByUpdatedDateTimeRange() called", [
            'fromDateTime' => $fromDateTime,
            'toDateTime' => $toDateTime,
            'page' => $page,
            'limit' => $limit
        ]);

        return $this->search([
            'updatedDateTimeFrom' => $fromDateTime,
            'updatedDateTimeTo' => $toDateTime,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Lấy tất cả đơn hàng với phân trang
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getAll(int $page = 1, int $limit = 100): OrderSearchResponse
    {
        $this->logger->debug("OrderModule::getAll() called", [
            'page' => $page,
            'limit' => $limit
        ]);

        return $this->search([
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Lấy đơn hàng giao hàng tận nhà
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getShippingOrders(int $page = 1, int $limit = 100): OrderSearchResponse
    {
        return $this->getByType(1, $page, $limit); // TYPE_SHIPPING = 1
    }

    /**
     * Lấy đơn hàng mua tại quầy
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getCounterOrders(int $page = 1, int $limit = 100): OrderSearchResponse
    {
        return $this->getByType(2, $page, $limit); // TYPE_COUNTER = 2
    }

    /**
     * Lấy đơn hàng đặt trước
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getPreOrders(int $page = 1, int $limit = 100): OrderSearchResponse
    {
        return $this->getByType(3, $page, $limit); // TYPE_PRE_ORDER = 3
    }

    /**
     * Lấy đơn hàng trả hàng
     *
     * @param int $page Trang hiện tại (mặc định: 1)
     * @param int $limit Số lượng đơn hàng trên 1 trang (mặc định: 100)
     * @return OrderSearchResponse Response chứa danh sách đơn hàng
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function getReturnOrders(int $page = 1, int $limit = 100): OrderSearchResponse
    {
        return $this->getByType(14, $page, $limit); // TYPE_RETURN = 14
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
            $isValid = $this->orderManager->validateSearchRequest($searchParams);

            $this->logger->debug("OrderModule::validateSearchRequest()", [
                'params' => $searchParams,
                'isValid' => $isValid
            ]);

            return $isValid;
        } catch (Exception $e) {
            $this->logger->error("OrderModule::validateSearchRequest() error", [
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
            return $this->orderManager->getSearchRequestErrors($searchParams);
        } catch (Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Lấy instance của OrderManager
     *
     * @return OrderManager Instance của OrderManager
     */
    public function getManager(): OrderManager
    {
        return $this->orderManager;
    }

    /**
     * Helper method để giải phóng memory cho cached data
     *
     * @param array|null $cachedData Dữ liệu cache cần xử lý
     * @param string $methodName Tên method để tạo entities
     * @return mixed Kết quả từ method
     */
    private function createEntitiesWithMemoryManagement(?array $cachedData, string $methodName)
    {
        if ($cachedData === null) {
            return null;
        }

        try {
            $result = $this->orderManager->$methodName($cachedData);
            // Giải phóng memory ngay lập tức
            unset($cachedData);
            return $result;
        } catch (Exception $e) {
            $this->logger->error("OrderModule::$methodName() - Error creating entities", ['error' => $e->getMessage()]);
            unset($cachedData);
            return null;
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
                $entity = $this->orderManager->$methodName($itemData);
                $entities[] = $entity;
                // Giải phóng memory ngay sau khi tạo entity
                unset($itemData);
            } catch (Exception $e) {
                $this->logger->error("OrderModule::$methodName() - Error creating entity", ['error' => $e->getMessage()]);
                // Skip invalid data
            }
        }

        // Giải phóng memory
        unset($responseData, $data);

        return $entities;
    }

    /**
     * Lấy trạng thái cache của đơn hàng
     *
     * @return array Mảng chứa thông tin trạng thái cache
     */
    public function getCacheStatus(): array
    {
        return $this->orderManager->getOrderCacheStatus();
    }

    /**
     * Xóa tất cả cache đơn hàng
     *
     * @return bool True nếu xóa thành công, false nếu thất bại
     */
    public function clearCache(): bool
    {
        return $this->orderManager->clearAllOrderCache();
    }

    /**
     * Kiểm tra cache đơn hàng có sẵn không
     *
     * @return bool True nếu cache có sẵn, false nếu không
     */
    public function isCacheAvailable(): bool
    {
        return $this->orderManager->isOrderCacheAvailable();
    }
}
