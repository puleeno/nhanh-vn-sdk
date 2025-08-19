<?php

namespace Puleeno\NhanhVn\Managers;

use Puleeno\NhanhVn\Repositories\OrderRepository;
use Puleeno\NhanhVn\Entities\Order\Order;
use Puleeno\NhanhVn\Entities\Order\OrderAddRequest;
use Puleeno\NhanhVn\Entities\Order\OrderSearchRequest;
use Puleeno\NhanhVn\Entities\Order\OrderSearchResponse;
use Puleeno\NhanhVn\Entities\Order\OrderUpdateRequest;
use Puleeno\NhanhVn\Entities\Order\OrderUpdateResponse;
use Puleeno\NhanhVn\Services\CacheService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;

/**
 * Order Manager - Quản lý business logic cho đơn hàng
 *
 * Manager này chịu trách nhiệm xử lý các logic nghiệp vụ liên quan đến đơn hàng
 * bao gồm: validation, cache management, business rules, thêm mới, cập nhật
 */
class OrderManager
{
    /** @var OrderRepository Repository quản lý Order entities */
    protected OrderRepository $orderRepository;

    /** @var CacheService Service quản lý cache */
    protected CacheService $cacheService;

    /** @var LoggerInterface Logger để ghi log */
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param OrderRepository $orderRepository Repository quản lý Order entities
     * @param CacheService $cacheService Service quản lý cache
     * @param LoggerInterface $logger Logger để ghi log
     */
    public function __construct(OrderRepository $orderRepository, CacheService $cacheService, LoggerInterface $logger)
    {
        $this->orderRepository = $orderRepository;
        $this->cacheService = $cacheService;
        $this->logger = $logger;
    }

    /**
     * Validate dữ liệu tìm kiếm đơn hàng
     *
     * @param array $searchData Dữ liệu cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateSearchRequest(array $searchData): bool
    {
        $this->logger->debug("OrderManager::validateSearchRequest() called", $searchData);

        try {
            $isValid = $this->orderRepository->validateSearchData($searchData);

            $this->logger->info("OrderManager::validateSearchRequest() result", [
                'params' => $searchData,
                'isValid' => $isValid
            ]);

            return $isValid;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::validateSearchRequest() error", [
                'error' => $e->getMessage(),
                'params' => $searchData
            ]);
            return false;
        }
    }

    /**
     * Validate dữ liệu cập nhật đơn hàng
     *
     * @param array $updateData Dữ liệu cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateUpdateData(array $updateData): bool
    {
        $this->logger->debug("OrderManager::validateUpdateData() called", $updateData);

        try {
            $isValid = $this->orderRepository->validateUpdateData($updateData);

            $this->logger->info("OrderManager::validateUpdateData() result", [
                'params' => $updateData,
                'isValid' => $isValid
            ]);

            return $isValid;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::validateUpdateData() error", [
                'error' => $e->getMessage(),
                'params' => $updateData
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách lỗi validation
     *
     * @param array $searchData Dữ liệu cần validate
     * @return array Mảng chứa các lỗi validation
     */
    public function getSearchRequestErrors(array $searchData): array
    {
        try {
            return $this->orderRepository->getSearchValidationErrors($searchData);
        } catch (\Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách lỗi validation cho cập nhật đơn hàng
     *
     * @param array $updateData Dữ liệu cần validate
     * @return array Mảng chứa các lỗi validation
     */
    public function getUpdateValidationErrors(array $updateData): array
    {
        try {
            return $this->orderRepository->getUpdateValidationErrors($updateData);
        } catch (\Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Tạo OrderSearchResponse từ dữ liệu API
     *
     * @param array $responseData Dữ liệu response từ API
     * @return OrderSearchResponse Response đã được tạo
     */
    public function createOrderSearchResponse(array $responseData): OrderSearchResponse
    {
        $this->logger->debug("OrderManager::createOrderSearchResponse() called", $responseData);

        try {
            $response = $this->orderRepository->createOrderSearchResponse($responseData);

            $this->logger->info("OrderManager::createOrderSearchResponse() - Created response", [
                'totalPages' => $response->getTotalPages(),
                'totalRecords' => $response->getTotalRecords(),
                'orderCount' => $response->getCurrentPageOrderCount()
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::createOrderSearchResponse() error", [
                'error' => $e->getMessage(),
                'responseData' => $responseData
            ]);
            throw $e;
        }
    }

    /**
     * Tạo OrderSearchResponse trống
     *
     * @return OrderSearchResponse Response trống
     */
    public function createEmptySearchResponse(): OrderSearchResponse
    {
        $this->logger->debug("OrderManager::createEmptySearchResponse() called");

        return $this->orderRepository->createEmptySearchResponse();
    }

    /**
     * Tạo Order entity từ dữ liệu
     *
     * @param array $orderData Dữ liệu đơn hàng
     * @return Order Order entity đã được tạo
     */
    public function createOrder(array $orderData): Order
    {
        $this->logger->debug("OrderManager::createOrder() called", ['orderId' => $orderData['id'] ?? 'unknown']);

        try {
            $order = $this->orderRepository->createOrder($orderData);

            $this->logger->info("OrderManager::createOrder() - Created order entity", [
                'orderId' => $order->getId(),
                'customerName' => $order->getCustomerName()
            ]);

            return $order;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::createOrder() error", [
                'error' => $e->getMessage(),
                'orderData' => $orderData
            ]);
            throw $e;
        }
    }

    /**
     * Tạo nhiều Order entities từ dữ liệu
     *
     * @param array $ordersData Mảng dữ liệu đơn hàng
     * @return array Mảng Order entities đã được tạo
     */
    public function createOrders(array $ordersData): array
    {
        $this->logger->debug("OrderManager::createOrders() called", [
            'orderCount' => count($ordersData)
        ]);

        try {
            $orders = $this->orderRepository->createOrders($ordersData);

            $this->logger->info("OrderManager::createOrders() - Created orders", [
                'requestedCount' => count($ordersData),
                'createdCount' => count($orders)
            ]);

            return $orders;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::createOrders() error", [
                'error' => $e->getMessage(),
                'orderCount' => count($ordersData)
            ]);
            throw $e;
        }
    }

    /**
     * Lấy cache key cho đơn hàng
     *
     * @param array $searchParams Tham số tìm kiếm
     * @return string Cache key
     */
    public function getOrderCacheKey(array $searchParams): string
    {
        $key = 'orders:search:' . md5(serialize($searchParams));
        return $key;
    }

    /**
     * Lấy đơn hàng từ cache
     *
     * @param array $searchParams Tham số tìm kiếm
     * @return array|null Dữ liệu đơn hàng từ cache hoặc null
     */
    public function getCachedOrders(array $searchParams): ?array
    {
        $cacheKey = $this->getOrderCacheKey($searchParams);

        $this->logger->debug("OrderManager::getCachedOrders() - Checking cache", ['key' => $cacheKey]);

        $cachedData = $this->cacheService->get($cacheKey);

        if ($cachedData !== null) {
            $this->logger->info("OrderManager::getCachedOrders() - Cache hit", ['key' => $cacheKey]);
        } else {
            $this->logger->info("OrderManager::getCachedOrders() - Cache miss", ['key' => $cacheKey]);
        }

        return $cachedData;
    }

    /**
     * Lưu đơn hàng vào cache
     *
     * @param array $searchParams Tham số tìm kiếm
     * @param array $ordersData Dữ liệu đơn hàng
     * @param int $ttl Thời gian sống cache (giây)
     * @return bool True nếu lưu thành công
     */
    public function cacheOrders(array $searchParams, array $ordersData, int $ttl = 3600): bool
    {
        $cacheKey = $this->getOrderCacheKey($searchParams);

        $this->logger->debug("OrderManager::cacheOrders() - Caching orders", [
            'key' => $cacheKey,
            'ttl' => $ttl,
            'orderCount' => count($ordersData)
        ]);

        try {
            $result = $this->cacheService->set($cacheKey, $ordersData, $ttl);

            if ($result) {
                $this->logger->info("OrderManager::cacheOrders() - Cached successfully", ['key' => $cacheKey]);
            } else {
                $this->logger->warning("OrderManager::cacheOrders() - Failed to cache", ['key' => $cacheKey]);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::cacheOrders() error", [
                'error' => $e->getMessage(),
                'key' => $cacheKey
            ]);
            return false;
        }
    }

    /**
     * Xóa cache đơn hàng
     *
     * @param array $searchParams Tham số tìm kiếm
     * @return bool True nếu xóa thành công
     */
    public function clearOrderCache(array $searchParams): bool
    {
        $cacheKey = $this->getOrderCacheKey($searchParams);

        $this->logger->debug("OrderManager::clearOrderCache() - Clearing cache", ['key' => $cacheKey]);

        try {
            $result = $this->cacheService->delete($cacheKey);

            if ($result) {
                $this->logger->info("OrderManager::clearOrderCache() - Cleared successfully", ['key' => $cacheKey]);
            } else {
                $this->logger->warning("OrderManager::clearOrderCache() - Failed to clear", ['key' => $cacheKey]);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::clearOrderCache() error", [
                'error' => $e->getMessage(),
                'key' => $cacheKey
            ]);
            return false;
        }
    }

    /**
     * Xóa tất cả cache đơn hàng
     *
     * @return bool True nếu xóa thành công
     */
    public function clearAllOrderCache(): bool
    {
        $this->logger->debug("OrderManager::clearAllOrderCache() called");

        try {
            $keys = $this->cacheService->getKeys();
            $orderKeys = array_filter($keys, function ($key) {
                return strpos($key, 'orders:') === 0;
            });

            $deletedCount = 0;
            foreach ($orderKeys as $key) {
                if ($this->cacheService->delete($key)) {
                    $deletedCount++;
                }
            }

            $this->logger->info("OrderManager::clearAllOrderCache() - Cleared {$deletedCount} cache keys");

            return true;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::clearAllOrderCache() error", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Lấy trạng thái cache đơn hàng
     *
     * @return array Mảng chứa thông tin trạng thái cache
     */
    public function getOrderCacheStatus(): array
    {
        $this->logger->debug("OrderManager::getOrderCacheStatus() called");

        try {
            $keys = $this->cacheService->getKeys();
            $orderKeys = array_filter($keys, function ($key) {
                return strpos($key, 'orders:') === 0;
            });

            $stats = [];
            foreach ($orderKeys as $key) {
                $ttl = $this->cacheService->getTtl($key);
                $stats[$key] = [
                    'ttl' => $ttl,
                    'expires_in' => $ttl > 0 ? gmdate('H:i:s', $ttl) : 'Never'
                ];
            }

            $this->logger->info("OrderManager::getOrderCacheStatus() - Retrieved status", [
                'totalKeys' => count($orderKeys)
            ]);

            return [
                'total_keys' => count($orderKeys),
                'keys' => $stats
            ];
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::getOrderCacheStatus() error", [
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Kiểm tra cache đơn hàng có sẵn không
     *
     * @return bool True nếu cache có sẵn
     */
    public function isOrderCacheAvailable(): bool
    {
        $keys = $this->cacheService->getKeys();
        $orderKeys = array_filter($keys, function ($key) {
            return strpos($key, 'orders:') === 0;
        });

        return !empty($orderKeys);
    }

    /**
     * Lấy instance của OrderRepository
     *
     * @return OrderRepository Instance của OrderRepository
     */
    public function getOrderRepository(): OrderRepository
    {
        return $this->orderRepository;
    }

    /**
     * Tạo OrderUpdateRequest từ dữ liệu
     *
     * @param array $updateData Dữ liệu cập nhật đơn hàng
     * @return OrderUpdateRequest Request đã được tạo
     */
    public function createOrderUpdateRequest(array $updateData): OrderUpdateRequest
    {
        $this->logger->debug("OrderManager::createOrderUpdateRequest() called", $updateData);

        try {
            $request = $this->orderRepository->createOrderUpdateRequest($updateData);

            $this->logger->info("OrderManager::createOrderUpdateRequest() - Created request", [
                'orderId' => $request->getOrderId(),
                'id' => $request->getId(),
                'updateType' => $request->getUpdateType()
            ]);

            return $request;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::createOrderUpdateRequest() error", [
                'error' => $e->getMessage(),
                'updateData' => $updateData
            ]);
            throw $e;
        }
    }

    /**
     * Tạo OrderUpdateResponse từ dữ liệu API
     *
     * @param array $responseData Dữ liệu response từ API
     * @return OrderUpdateResponse Response đã được tạo
     */
    public function createOrderUpdateResponse(array $responseData): OrderUpdateResponse
    {
        $this->logger->debug("OrderManager::createOrderUpdateResponse() called", $responseData);

        try {
            $response = $this->orderRepository->createOrderUpdateResponse($responseData);

            $this->logger->info("OrderManager::createOrderUpdateResponse() - Created response", [
                'success' => $response->isSuccess(),
                'code' => $response->getCode(),
                'orderId' => $response->getOrderId()
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::createOrderUpdateResponse() error", [
                'error' => $e->getMessage(),
                'responseData' => $responseData
            ]);
            throw $e;
        }
    }

    /**
     * Validate dữ liệu thêm đơn hàng
     *
     * @param OrderAddRequest $request Request thêm đơn hàng
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateAddRequest(OrderAddRequest $request): bool
    {
        $this->logger->debug("OrderManager::validateAddRequest() called", [
            'orderId' => $request->getId(),
            'customerName' => $request->getCustomerName()
        ]);

        try {
            $isValid = $request->validateForAdd();

            $this->logger->info("OrderManager::validateAddRequest() result", [
                'orderId' => $request->getId(),
                'isValid' => $isValid,
                'errors' => $request->getErrors()
            ]);

            return $isValid;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::validateAddRequest() error", [
                'error' => $e->getMessage(),
                'orderId' => $request->getId()
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách lỗi validation cho request thêm đơn hàng
     *
     * @param OrderAddRequest $request Request thêm đơn hàng
     * @return array Mảng chứa các lỗi validation
     */
    public function getAddRequestErrors(OrderAddRequest $request): array
    {
        try {
            return $request->getErrors();
        } catch (\Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Kiểm tra đơn hàng có thể thêm được không
     *
     * @param OrderAddRequest $request Request thêm đơn hàng
     * @return array Mảng chứa kết quả kiểm tra
     */
    public function canAddOrder(OrderAddRequest $request): array
    {
        $this->logger->debug("OrderManager::canAddOrder() called", [
            'orderId' => $request->getId()
        ]);

        $result = [
            'can_add' => true,
            'warnings' => [],
            'errors' => []
        ];

        try {
            // Kiểm tra validation
            if (!$this->validateAddRequest($request)) {
                $result['can_add'] = false;
                $result['errors'] = $this->getAddRequestErrors($request);
                return $result;
            }

            // Kiểm tra business rules
            if ($request->hasShipping()) {
                if ($request->usesNhanhCarrierPricing()) {
                    if (!$request->getCarrierId() || !$request->getCarrierServiceId()) {
                        $result['warnings'][] = 'Khi sử dụng bảng giá vận chuyển của Nhanh.vn, carrierId và carrierServiceId là bắt buộc';
                    }
                } elseif ($request->usesSelfConnectCarrierPricing()) {
                    if (!$request->getCarrierAccountId() || !$request->getCarrierServiceCode()) {
                        $result['warnings'][] = 'Khi sử dụng bảng giá vận chuyển riêng, carrierAccountId và carrierServiceCode là bắt buộc';
                    }
                }
            }

            // Kiểm tra sản phẩm
            if (!$request->hasProducts()) {
                $result['warnings'][] = 'Đơn hàng không có sản phẩm nào';
            }

            $this->logger->info("OrderManager::canAddOrder() result", [
                'orderId' => $request->getId(),
                'can_add' => $result['can_add'],
                'warnings' => $result['warnings'],
                'errors' => $result['errors']
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logger->error("OrderManager::canAddOrder() error", [
                'error' => $e->getMessage(),
                'orderId' => $request->getId()
            ]);

            $result['can_add'] = false;
            $result['errors'][] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Lấy instance của CacheService
     *
     * @return CacheService Instance của CacheService
     */
    public function getCacheService(): CacheService
    {
        return $this->cacheService;
    }

    /**
     * Chuẩn bị search criteria cho API call
     *
     * @param array $searchParams Tham số tìm kiếm
     * @return array Dữ liệu đã format cho API
     * @throws Exception Khi có lỗi xảy ra
     */
    public function prepareSearchCriteria(array $searchParams): array
    {
        $this->logger->debug("OrderManager::prepareSearchCriteria() called", [
            'searchParams' => $searchParams
        ]);

        try {
            return $this->orderRepository->prepareSearchCriteria($searchParams);
        } catch (Exception $e) {
            $this->logger->error("OrderManager::prepareSearchCriteria() error", [
                'error' => $e->getMessage(),
                'searchParams' => $searchParams
            ]);
            throw $e;
        }
    }
}
