<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Repositories;

use Puleeno\NhanhVn\Entities\Shipping\ShippingCarrier;
use Puleeno\NhanhVn\Entities\Shipping\ShippingCarrierResponse;
use Puleeno\NhanhVn\Entities\Shipping\Location;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchRequest;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchResponse;
use Puleeno\NhanhVn\Services\CacheService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Exception;

/**
 * Shipping Repository - Quản lý dữ liệu hãng vận chuyển
 *
 * Repository này chịu trách nhiệm lưu trữ và truy xuất dữ liệu hãng vận chuyển
 * từ cache và các nguồn dữ liệu khác
 */
class ShippingRepository
{
    /** @var CacheService Service quản lý cache */
    protected CacheService $cacheService;

    /** @var LoggerInterface Logger để ghi log */
    protected LoggerInterface $logger;

    /** @var string Cache key cho danh sách hãng vận chuyển */
    protected const CACHE_KEY_CARRIERS = 'shipping_carriers';

    /** @var int Thời gian cache mặc định (24 giờ) */
    protected const CACHE_TTL = 86400;

    /**
     * Constructor
     *
     * @param CacheService $cacheService Service quản lý cache
     * @param LoggerInterface $logger Logger để ghi log
     */
    public function __construct(CacheService $cacheService, LoggerInterface $logger)
    {
        $this->cacheService = $cacheService;
        $this->logger = $logger;
    }

    /**
     * Lấy danh sách hãng vận chuyển từ cache
     *
     * @return ShippingCarrierResponse|null Response chứa danh sách hãng vận chuyển hoặc null nếu không có
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy dữ liệu từ cache
     */
    public function getCarriersFromCache(): ?ShippingCarrierResponse
    {
        $this->logger->debug("ShippingRepository::getCarriersFromCache() called");

        try {
            $cachedData = $this->cacheService->get(self::CACHE_KEY_CARRIERS);
            
            if (empty($cachedData)) {
                $this->logger->debug("ShippingRepository::getCarriersFromCache() - No cached data found");
                return null;
            }

            $this->logger->info("ShippingRepository::getCarriersFromCache() - Found cached data", [
                'cacheKey' => self::CACHE_KEY_CARRIERS,
                'dataSize' => is_array($cachedData) ? count($cachedData) : 'unknown'
            ]);

            return new ShippingCarrierResponse($cachedData);

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::getCarriersFromCache() error", [
                'error' => $e->getMessage(),
                'cacheKey' => self::CACHE_KEY_CARRIERS
            ]);
            throw $e;
        }
    }

    /**
     * Lưu danh sách hãng vận chuyển vào cache
     *
     * @param ShippingCarrierResponse $carriersResponse Response chứa danh sách hãng vận chuyển
     * @param int|null $ttl Thời gian cache (giây), mặc định 24 giờ
     * @return bool True nếu lưu thành công, false nếu thất bại
     * @throws Exception Khi có lỗi xảy ra trong quá trình lưu cache
     */
    public function saveCarriersToCache(ShippingCarrierResponse $carriersResponse, ?int $ttl = null): bool
    {
        $this->logger->debug("ShippingRepository::saveCarriersToCache() called", [
            'totalCarriers' => $carriersResponse->getTotalCarriers(),
            'ttl' => $ttl ?? self::CACHE_TTL
        ]);

        try {
            $cacheData = [
                'carriers' => $carriersResponse->getCarriers(),
                'totalCarriers' => $carriersResponse->getTotalCarriers(),
                'cached' => true,
                'cacheExpiry' => date('Y-m-d H:i:s', time() + ($ttl ?? self::CACHE_TTL))
            ];

            $success = $this->cacheService->set(
                self::CACHE_KEY_CARRIERS,
                $cacheData,
                $ttl ?? self::CACHE_TTL
            );

            if ($success) {
                $this->logger->info("ShippingRepository::saveCarriersToCache() - Successfully cached carriers", [
                    'cacheKey' => self::CACHE_KEY_CARRIERS,
                    'totalCarriers' => $carriersResponse->getTotalCarriers(),
                    'ttl' => $ttl ?? self::CACHE_TTL
                ]);
            } else {
                $this->logger->warning("ShippingRepository::saveCarriersToCache() - Failed to cache carriers", [
                    'cacheKey' => self::CACHE_KEY_CARRIERS
                ]);
            }

            return $success;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::saveCarriersToCache() error", [
                'error' => $e->getMessage(),
                'cacheKey' => self::CACHE_KEY_CARRIERS
            ]);
            throw $e;
        }
    }

    /**
     * Xóa cache hãng vận chuyển
     *
     * @return bool True nếu xóa thành công, false nếu thất bại
     * @throws Exception Khi có lỗi xảy ra trong quá trình xóa cache
     */
    public function clearCarriersCache(): bool
    {
        $this->logger->debug("ShippingRepository::clearCarriersCache() called");

        try {
            $success = $this->cacheService->delete(self::CACHE_KEY_CARRIERS);

            if ($success) {
                $this->logger->info("ShippingRepository::clearCarriersCache() - Successfully cleared cache", [
                    'cacheKey' => self::CACHE_KEY_CARRIERS
                ]);
            } else {
                $this->logger->warning("ShippingRepository::clearCarriersCache() - Failed to clear cache", [
                    'cacheKey' => self::CACHE_KEY_CARRIERS
                ]);
            }

            return $success;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::clearCarriersCache() error", [
                'error' => $e->getMessage(),
                'cacheKey' => self::CACHE_KEY_CARRIERS
            ]);
            throw $e;
        }
    }

    /**
     * Kiểm tra cache có hết hạn chưa
     *
     * @return bool True nếu cache đã hết hạn, false nếu còn hạn
     * @throws Exception Khi có lỗi xảy ra trong quá trình kiểm tra cache
     */
    public function isCarriersCacheExpired(): bool
    {
        $this->logger->debug("ShippingRepository::isCarriersCacheExpired() called");

        try {
            $cachedData = $this->cacheService->get(self::CACHE_KEY_CARRIERS);
            
            if (empty($cachedData)) {
                $this->logger->debug("ShippingRepository::isCarriersCacheExpired() - No cached data, considered expired");
                return true;
            }

            if (!isset($cachedData['cacheExpiry'])) {
                $this->logger->debug("ShippingRepository::isCarriersCacheExpired() - No expiry info, considered expired");
                return true;
            }

            $expiryTime = strtotime($cachedData['cacheExpiry']);
            $isExpired = $expiryTime === false || $expiryTime < time();

            $this->logger->debug("ShippingRepository::isCarriersCacheExpired() result", [
                'expiryTime' => $cachedData['cacheExpiry'],
                'isExpired' => $isExpired
            ]);

            return $isExpired;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::isCarriersCacheExpired() error", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Lấy thời gian còn lại của cache (giây)
     *
     * @return int Số giây còn lại, 0 nếu đã hết hạn hoặc không có cache
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy thời gian cache
     */
    public function getCarriersCacheTimeLeft(): int
    {
        $this->logger->debug("ShippingRepository::getCarriersCacheTimeLeft() called");

        try {
            $cachedData = $this->cacheService->get(self::CACHE_KEY_CARRIERS);
            
            if (empty($cachedData) || !isset($cachedData['cacheExpiry'])) {
                $this->logger->debug("ShippingRepository::getCarriersCacheTimeLeft() - No cache or expiry info");
                return 0;
            }

            $expiryTime = strtotime($cachedData['cacheExpiry']);
            if ($expiryTime === false) {
                $this->logger->debug("ShippingRepository::getCarriersCacheTimeLeft() - Invalid expiry time");
                return 0;
            }

            $timeLeft = max(0, $expiryTime - time());

            $this->logger->debug("ShippingRepository::getCarriersCacheTimeLeft() result", [
                'expiryTime' => $cachedData['cacheExpiry'],
                'timeLeft' => $timeLeft
            ]);

            return $timeLeft;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::getCarriersCacheTimeLeft() error", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Tạo response rỗng cho hãng vận chuyển
     *
     * @return ShippingCarrierResponse Response rỗng
     */
    public function createEmptyCarriersResponse(): ShippingCarrierResponse
    {
        $this->logger->debug("ShippingRepository::createEmptyCarriersResponse() called");

        return new ShippingCarrierResponse([
            'carriers' => [],
            'totalCarriers' => 0,
            'cached' => false,
            'cacheExpiry' => null
        ]);
    }

    /**
     * Tạo response từ dữ liệu API
     *
     * @param array $apiData Dữ liệu từ API
     * @return ShippingCarrierResponse Response được tạo từ dữ liệu API
     */
    public function createCarriersResponseFromApiData(array $apiData): ShippingCarrierResponse
    {
        $this->logger->debug("ShippingRepository::createCarriersResponseFromApiData() called", [
            'dataSize' => count($apiData)
        ]);

        $responseData = [
            'carriers' => $apiData,
            'totalCarriers' => count($apiData),
            'cached' => false,
            'cacheExpiry' => null
        ];

        return new ShippingCarrierResponse($responseData);
    }

    /**
     * Lấy thông tin cache
     *
     * @return array Thông tin về cache hiện tại
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy thông tin cache
     */
    public function getCacheInfo(): array
    {
        $this->logger->debug("ShippingRepository::getCacheInfo() called");

        try {
            $cachedData = $this->cacheService->get(self::CACHE_KEY_CARRIERS);
            
            if (empty($cachedData)) {
                return [
                    'hasCache' => false,
                    'totalCarriers' => 0,
                    'cached' => false,
                    'cacheExpiry' => null,
                    'timeLeft' => 0
                ];
            }

            $timeLeft = $this->getCarriersCacheTimeLeft();

            return [
                'hasCache' => true,
                'totalCarriers' => $cachedData['totalCarriers'] ?? 0,
                'cached' => $cachedData['cached'] ?? false,
                'cacheKey' => self::CACHE_KEY_CARRIERS,
                'cacheExpiry' => $cachedData['cacheExpiry'] ?? null,
                'timeLeft' => $timeLeft
            ];

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::getCacheInfo() error", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ==================== LOCATION METHODS ====================

    /**
     * Tạo LocationSearchRequest từ dữ liệu array
     *
     * @param array $data Dữ liệu để tạo request
     * @return LocationSearchRequest Request được tạo
     * @throws Exception Khi có lỗi xảy ra trong quá trình tạo request
     */
    public function createLocationSearchRequest(array $data): LocationSearchRequest
    {
        $this->logger->debug("ShippingRepository::createLocationSearchRequest() called", [
            'data' => $data
        ]);

        try {
            $request = new LocationSearchRequest($data);
            
            if (!$request->isValid()) {
                $errors = $request->getValidationErrors();
                $this->logger->warning("ShippingRepository::createLocationSearchRequest() - Validation failed", [
                    'errors' => $errors
                ]);
                throw new Exception("Dữ liệu request không hợp lệ: " . implode(', ', $errors));
            }

            $this->logger->debug("ShippingRepository::createLocationSearchRequest() - Request created successfully", [
                'type' => $request->getType(),
                'parentId' => $request->getParentId()
            ]);

            return $request;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::createLocationSearchRequest() error", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Tạo LocationSearchResponse từ dữ liệu API
     *
     * @param array $apiData Dữ liệu từ API
     * @param string $type Loại địa điểm (CITY, DISTRICT, WARD)
     * @return LocationSearchResponse Response được tạo từ dữ liệu API
     * @throws Exception Khi có lỗi xảy ra trong quá trình tạo response
     */
    public function createLocationSearchResponse(array $apiData, string $type = 'CITY'): LocationSearchResponse
    {
        $this->logger->debug("ShippingRepository::createLocationSearchResponse() called", [
            'dataSize' => count($apiData),
            'type' => $type
        ]);

        try {
            if (empty($apiData)) {
                $this->logger->debug("ShippingRepository::createLocationSearchResponse() - Empty data, creating empty response");
                return LocationSearchResponse::createEmpty();
            }

            $locations = [];
            foreach ($apiData as $locationData) {
                $location = Location::fromApiResponse($locationData, $type);
                $locations[] = $location;
            }

            $this->logger->debug("ShippingRepository::createLocationSearchResponse() - Response created successfully", [
                'totalLocations' => count($locations),
                'type' => $type
            ]);

            return LocationSearchResponse::createSuccess($locations);

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::createLocationSearchResponse() error", [
                'error' => $e->getMessage(),
                'dataSize' => count($apiData),
                'type' => $type
            ]);
            throw $e;
        }
    }

    /**
     * Validate dữ liệu tìm kiếm địa điểm
     *
     * @param array $data Dữ liệu cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     * @throws Exception Khi có lỗi xảy ra trong quá trình validate
     */
    public function validateLocationSearchData(array $data): bool
    {
        $this->logger->debug("ShippingRepository::createLocationSearchRequest() called", [
            'data' => $data
        ]);

        try {
            $request = new LocationSearchRequest($data);
            $isValid = $request->isValid();

            $this->logger->debug("ShippingRepository::validateLocationSearchData() result", [
                'isValid' => $isValid
            ]);

            return $isValid;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::validateLocationSearchData() error", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Lấy danh sách lỗi validation cho tìm kiếm địa điểm
     *
     * @param array $data Dữ liệu cần validate
     * @return array Danh sách lỗi validation
     * @throws Exception Khi có lỗi xảy ra trong quá trình validate
     */
    public function getLocationSearchValidationErrors(array $data): array
    {
        $this->logger->debug("ShippingRepository::getLocationSearchValidationErrors() called", [
            'data' => $data
        ]);

        try {
            $request = new LocationSearchRequest($data);
            $errors = $request->getValidationErrors();

            $this->logger->debug("ShippingRepository::getLocationSearchValidationErrors() result", [
                'errorCount' => count($errors)
            ]);

            return $errors;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::getLocationSearchValidationErrors() error", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Chuẩn bị dữ liệu để gửi API tìm kiếm địa điểm
     *
     * @param array $data Dữ liệu cần chuẩn bị
     * @return array Dữ liệu đã được chuẩn bị
     * @throws Exception Khi có lỗi xảy ra trong quá trình chuẩn bị dữ liệu
     */
    public function prepareLocationSearchData(array $data): array
    {
        $this->logger->debug("ShippingRepository::prepareLocationSearchData() called", [
            'data' => $data
        ]);

        try {
            $request = new LocationSearchRequest($data);
            
            if (!$request->isValid()) {
                $errors = $request->getValidationErrors();
                $this->logger->warning("ShippingRepository::prepareLocationSearchData() - Validation failed", [
                    'errors' => $errors
                ]);
                throw new Exception("Dữ liệu tìm kiếm không hợp lệ: " . implode(', ', $errors));
            }

            $preparedData = $request->toArray();

            $this->logger->debug("ShippingRepository::prepareLocationSearchData() - Data prepared successfully", [
                'preparedData' => $preparedData
            ]);

            return $preparedData;

        } catch (Exception $e) {
            $this->logger->error("ShippingRepository::prepareLocationSearchData() error", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }
}
