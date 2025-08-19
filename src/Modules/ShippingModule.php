<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Entities\Shipping\Location;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchRequest;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchResponse;
use Puleeno\NhanhVn\Entities\Shipping\ShippingCarrier;
use Puleeno\NhanhVn\Entities\Shipping\ShippingCarrierResponse;
use Puleeno\NhanhVn\Entities\Shipping\ShippingFeeRequest;
use Puleeno\NhanhVn\Entities\Shipping\ShippingFeeResponse;
use Puleeno\NhanhVn\Entities\Shipping\ShippingFeeSelfConnectRequest;
use Puleeno\NhanhVn\Entities\Shipping\ShippingFeeSelfConnectResponse;
use Puleeno\NhanhVn\Managers\ShippingManager;
use Puleeno\NhanhVn\Services\HttpService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Exception;

/**
 * Shipping Module - Module chính để tương tác với shipping và location APIs
 *
 * Module này cung cấp interface công khai để truy cập các chức năng shipping
 * bao gồm: tìm kiếm địa điểm (thành phố, quận huyện, phường xã)
 *
 * @package NhanhVn\Sdk\Modules
 * @author Nhanh.vn SDK Team
 */
class ShippingModule
{
    /** @var ShippingManager Manager xử lý business logic shipping */
    protected ShippingManager $manager;

    /** @var HttpService Service xử lý HTTP requests */
    protected HttpService $httpService;

    /** @var LoggerInterface Logger để ghi log */
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param ShippingManager $manager Manager xử lý business logic shipping
     * @param HttpService $httpService Service xử lý HTTP requests
     * @param LoggerInterface $logger Logger để ghi log
     */
    public function __construct(
        ShippingManager $manager,
        HttpService $httpService,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
        $this->httpService = $httpService;
        $this->logger = $logger;
    }

    /**
     * Tìm kiếm địa điểm theo criteria
     *
     * @param array $criteria Criteria tìm kiếm (type, parentId)
     * @return LocationSearchResponse Response chứa danh sách địa điểm
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function search(array $criteria): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::search() called", [
            'criteria' => $criteria
        ]);

        try {
            $response = $this->manager->searchLocations($criteria);

            $this->logger->debug("ShippingModule::search() completed", [
                'totalLocations' => $response->getCount(),
                'isSuccess' => $response->isSuccess()
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::search() error", [
                'error' => $e->getMessage(),
                'criteria' => $criteria
            ]);
            throw $e;
        }
    }

    /**
     * Tìm kiếm địa điểm từ LocationSearchRequest entity
     *
     * @param LocationSearchRequest $request Request entity chứa criteria tìm kiếm
     * @return LocationSearchResponse Response chứa danh sách địa điểm
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchFromRequest(LocationSearchRequest $request): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::searchFromRequest() called", [
            'type' => $request->getType(),
            'parentId' => $request->getParentId()
        ]);

        try {
            $criteria = $request->toArray();
            $response = $this->search($criteria);

            $this->logger->debug("ShippingModule::searchFromRequest() completed", [
                'totalLocations' => $response->getCount(),
                'isSuccess' => $response->isSuccess()
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::searchFromRequest() error", [
                'error' => $e->getMessage(),
                'type' => $request->getType(),
                'parentId' => $request->getParentId()
            ]);
            throw $e;
        }
    }

    /**
     * Tìm kiếm thành phố
     *
     * @return LocationSearchResponse Response chứa danh sách thành phố
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchCities(): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::searchCities() called");

        try {
            $response = $this->manager->searchCities();

            $this->logger->debug("ShippingModule::searchCities() completed", [
                'totalCities' => $response->getCount(),
                'isSuccess' => $response->isSuccess()
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::searchCities() error", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Tìm kiếm quận huyện theo thành phố
     *
     * @param int $cityId ID của thành phố
     * @return LocationSearchResponse Response chứa danh sách quận huyện
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchDistricts(int $cityId): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::searchDistricts() called", [
            'cityId' => $cityId
        ]);

        try {
            $response = $this->manager->searchDistricts($cityId);

            $this->logger->debug("ShippingModule::searchDistricts() completed", [
                'totalDistricts' => $response->getCount(),
                'isSuccess' => $response->isSuccess()
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::searchDistricts() error", [
                'error' => $e->getMessage(),
                'cityId' => $cityId
            ]);
            throw $e;
        }
    }

    /**
     * Tìm kiếm phường xã theo quận huyện
     *
     * @param int $districtId ID của quận huyện
     * @return LocationSearchResponse Response chứa danh sách phường xã
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchWards(int $districtId): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::searchWards() called", [
            'districtId' => $districtId
        ]);

        try {
            $response = $this->manager->searchWards($districtId);

            $this->logger->debug("ShippingModule::searchWards() completed", [
                'totalWards' => $response->getCount(),
                'isSuccess' => $response->isSuccess()
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::searchWards() error", [
                'error' => $e->getMessage(),
                'districtId' => $districtId
            ]);
            throw $e;
        }
    }

    /**
     * Tìm kiếm địa điểm theo tên (tìm kiếm gần đúng)
     *
     * @param string $name Tên địa điểm cần tìm
     * @param string $type Loại địa điểm (CITY, DISTRICT, WARD)
     * @param int|null $parentId ID của địa điểm cha (nếu có)
     * @return LocationSearchResponse Response chứa danh sách địa điểm tìm được
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function searchByName(string $name, string $type = 'CITY', ?int $parentId = null): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::searchByName() called", [
            'name' => $name,
            'type' => $type,
            'parentId' => $parentId
        ]);

        try {
            $criteria = ['type' => $type];
            if ($parentId !== null) {
                $criteria['parentId'] = $parentId;
            }

            $response = $this->search($criteria);

            if ($response->isSuccess() && !$response->isEmpty()) {
                // Lọc theo tên
                $filteredLocations = $response->filterByName($name);
                $filteredResponse = LocationSearchResponse::createSuccess($filteredLocations);

                $this->logger->debug("ShippingModule::searchByName() completed with filtering", [
                    'originalCount' => $response->getCount(),
                    'filteredCount' => count($filteredLocations),
                    'searchName' => $name
                ]);

                return $filteredResponse;
            }

            $this->logger->debug("ShippingModule::searchByName() completed", [
                'totalLocations' => $response->getCount(),
                'isSuccess' => $response->isSuccess()
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::searchByName() error", [
                'error' => $e->getMessage(),
                'name' => $name,
                'type' => $type,
                'parentId' => $parentId
            ]);
            throw $e;
        }
    }

    /**
     * Tìm kiếm địa điểm theo ID
     *
     * @param int $id ID của địa điểm
     * @param string $type Loại địa điểm (CITY, DISTRICT, WARD)
     * @return Location|null Địa điểm tìm được hoặc null nếu không tìm thấy
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function findById(int $id, string $type = 'CITY'): ?Location
    {
        $this->logger->debug("ShippingModule::findById() called", [
            'id' => $id,
            'type' => $type
        ]);

        try {
            $criteria = ['type' => $type];

            // Nếu là DISTRICT hoặc WARD, cần tìm tất cả rồi filter theo ID
            if ($type === 'DISTRICT' || $type === 'WARD') {
                // Tìm tất cả địa điểm cùng loại
                $response = $this->search($criteria);

                if ($response->isSuccess()) {
                    $location = $response->getLocationById($id);

                    $this->logger->debug("ShippingModule::findById() completed", [
                        'found' => $location !== null,
                        'type' => $type
                    ]);

                    return $location;
                }
            } else {
                // Với CITY, có thể tìm trực tiếp
                $response = $this->search($criteria);

                if ($response->isSuccess()) {
                    $location = $response->getLocationById($id);

                    $this->logger->debug("ShippingModule::findById() completed", [
                        'found' => $location !== null,
                        'type' => $type
                    ]);

                    return $location;
                }
            }

            $this->logger->debug("ShippingModule::findById() - Location not found", [
                'id' => $id,
                'type' => $type
            ]);

            return null;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::findById() error", [
                'error' => $e->getMessage(),
                'id' => $id,
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
    public function validateSearchData(array $data): bool
    {
        $this->logger->debug("ShippingModule::validateSearchData() called", [
            'data' => $data
        ]);

        try {
            $isValid = $this->manager->validateLocationSearchData($data);

            $this->logger->debug("ShippingModule::validateSearchData() result", [
                'isValid' => $isValid
            ]);

            return $isValid;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::validateSearchData() error", [
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
    public function getValidationErrors(array $data): array
    {
        $this->logger->debug("ShippingModule::getValidationErrors() called", [
            'data' => $data
        ]);

        try {
            $errors = $this->manager->getLocationSearchValidationErrors($data);

            $this->logger->debug("ShippingModule::getValidationErrors() result", [
                'errorCount' => count($errors)
            ]);

            return $errors;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::getValidationErrors() error", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Tạo LocationSearchRequest từ dữ liệu array
     *
     * @param array $data Dữ liệu để tạo request
     * @return LocationSearchRequest Request được tạo
     * @throws Exception Khi có lỗi xảy ra trong quá trình tạo request
     */
    public function createSearchRequest(array $data): LocationSearchRequest
    {
        $this->logger->debug("ShippingModule::createSearchRequest() called", [
            'data' => $data
        ]);

        try {
            $request = $this->manager->createLocationSearchRequest($data);

            $this->logger->debug("ShippingModule::createSearchRequest() completed", [
                'type' => $request->getType(),
                'parentId' => $request->getParentId()
            ]);

            return $request;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::createSearchRequest() error", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Tạo response rỗng cho tìm kiếm địa điểm
     *
     * @return LocationSearchResponse Response rỗng
     */
    public function createEmptyResponse(): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::createEmptyResponse() called");

        return $this->manager->createEmptyLocationSearchResponse();
    }

    /**
     * Tạo response lỗi cho tìm kiếm địa điểm
     *
     * @param array $messages Danh sách thông báo lỗi
     * @return LocationSearchResponse Response lỗi
     */
    public function createErrorResponse(array $messages): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::createErrorResponse() called", [
            'messages' => $messages
        ]);

        return $this->manager->createErrorLocationSearchResponse($messages);
    }

    /**
     * Lấy danh sách hãng vận chuyển từ Nhanh.vn API
     *
     * @param bool $forceRefresh Bắt buộc refresh từ API, bỏ qua cache
     * @return ShippingCarrierResponse Response chứa danh sách hãng vận chuyển
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy dữ liệu
     */
    public function getCarriers(bool $forceRefresh = false): ShippingCarrierResponse
    {
        $this->logger->debug("ShippingModule::getCarriers() called", [
            'forceRefresh' => $forceRefresh
        ]);

        try {
            // Gọi API để lấy danh sách hãng vận chuyển
            $response = $this->httpService->callApi('/shipping/carrier', [
                'force_refresh' => $forceRefresh ? 1 : 0
            ]);

            $this->logger->info("ShippingModule::getCarriers() API call completed", [
                'responseSize' => is_array($response) ? count($response) : 'unknown'
            ]);

            // Tạo response entity từ dữ liệu API
            return new ShippingCarrierResponse($response);

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::getCarriers() error", [
                'error' => $e->getMessage(),
                'forceRefresh' => $forceRefresh
            ]);
            throw $e;
        }
    }

    /**
     * Lấy danh sách hãng vận chuyển với quản lý memory
     *
     * @param bool $forceRefresh Bắt buộc refresh từ API, bỏ qua cache
     * @return ShippingCarrierResponse Response chứa danh sách hãng vận chuyển
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy dữ liệu
     */
    public function getCarriersWithMemoryManagement(bool $forceRefresh = false): ShippingCarrierResponse
    {
        $this->logger->debug("ShippingModule::getCarriersWithMemoryManagement() called", [
            'forceRefresh' => $forceRefresh
        ]);

        try {
            $response = $this->getCarriers($forceRefresh);

            // Cleanup memory
            $this->createEntitiesWithMemoryManagement($response);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::getCarriersWithMemoryManagement() error", [
                'error' => $e->getMessage(),
                'forceRefresh' => $forceRefresh
            ]);
            throw $e;
        }
    }

    /**
     * Tính phí vận chuyển qua cổng Nhanh.vn
     *
     * @param ShippingFeeRequest $request Request tính phí vận chuyển
     * @return ShippingFeeResponse Response chứa danh sách phí vận chuyển
     * @throws Exception Khi có lỗi xảy ra trong quá trình tính phí
     */
    public function calculateFee(ShippingFeeRequest $request): ShippingFeeResponse
    {
        $this->logger->debug("ShippingModule::calculateFee() called", [
            'fromCity' => $request->getFromCityName(),
            'toCity' => $request->getToCityName(),
            'weight' => $request->getShippingWeight(),
            'codMoney' => $request->getCodMoney()
        ]);

        try {
            // Validate request
            if (!$request->isValid()) {
                throw new Exception('Request validation failed: ' . implode(', ', array_flatten($request->getErrors())));
            }

            // Chuẩn bị data cho API call
            $data = [
                'fromCityName' => $request->getFromCityName(),
                'fromDistrictName' => $request->getFromDistrictName(),
                'toCityName' => $request->getToCityName(),
                'toDistrictName' => $request->getToDistrictName(),
                'codMoney' => $request->getCodMoney()
            ];

            // Thêm weight hoặc productIds
            if ($request->hasShippingWeight()) {
                $data['shippingWeight'] = $request->getShippingWeight();
            } elseif ($request->hasProductIds()) {
                $data['productIds'] = $request->getProductIds();
            }

            // Thêm carrier IDs nếu có
            if ($request->getCarrierIds()) {
                $data['carrierIds'] = $request->getCarrierIds();
            }

            // Thêm dimensions nếu có
            if ($request->hasDimensions()) {
                $data['length'] = $request->getLength();
                $data['width'] = $request->getWidth();
                $data['height'] = $request->getHeight();
            }

            // Gọi API
            $response = $this->httpService->callApi('/shipping/fee', $data);

            $this->logger->info("ShippingModule::calculateFee() API call completed", [
                'responseSize' => is_array($response) ? count($response) : 'unknown'
            ]);

            // Tạo response entity từ dữ liệu API
            return new ShippingFeeResponse($response);

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::calculateFee() error", [
                'error' => $e->getMessage(),
                'request' => $request->toArray()
            ]);
            throw $e;
        }
    }

    /**
     * Tính phí vận chuyển từ array data (convenience method)
     *
     * @param array $data Dữ liệu tính phí vận chuyển
     * @return ShippingFeeResponse Response chứa danh sách phí vận chuyển
     * @throws Exception Khi có lỗi xảy ra trong quá trình tính phí
     */
    public function calculateFeeFromArray(array $data): ShippingFeeResponse
    {
        $request = new ShippingFeeRequest($data);
        return $this->calculateFee($request);
    }

    /**
     * Tính phí vận chuyển tự kết nối
     *
     * @param ShippingFeeSelfConnectRequest $request Request tính phí vận chuyển tự kết nối
     * @return ShippingFeeSelfConnectResponse Response chứa danh sách phí vận chuyển
     * @throws Exception Khi có lỗi xảy ra trong quá trình tính phí
     */
    public function calculateFeeSelfConnect(ShippingFeeSelfConnectRequest $request): ShippingFeeSelfConnectResponse
    {
        $this->logger->debug("ShippingModule::calculateFeeSelfConnect() called", [
            'carrierId' => $request->getCarrierId(),
            'fromCity' => $request->getFromCityName(),
            'toCity' => $request->getToCityName(),
            'weight' => $request->getShippingWeight(),
            'codMoney' => $request->getCodMoney()
        ]);

        try {
            // Validate request
            if (!$request->isValid()) {
                throw new Exception('Request validation failed: ' . implode(', ', array_flatten($request->getErrors())));
            }

            // Chuẩn bị data cho API call
            $data = [
                'carrierId' => $request->getCarrierId(),
                'fromCityName' => $request->getFromCityName(),
                'fromDistrictName' => $request->getFromDistrictName(),
                'toCityName' => $request->getToCityName(),
                'toDistrictName' => $request->getToDistrictName(),
                'codMoney' => $request->getCodMoney()
            ];

            // Thêm optional fields
            if ($request->getFromMobile()) {
                $data['fromMobile'] = $request->getFromMobile();
            }

            if ($request->getFromWardName()) {
                $data['fromWardName'] = $request->getFromWardName();
            }

            if ($request->getFromAddress()) {
                $data['fromAddress'] = $request->getFromAddress();
            }

            if ($request->getToWardName()) {
                $data['toWardName'] = $request->getToWardName();
            }

            if ($request->getToAddress()) {
                $data['toAddress'] = $request->getToAddress();
            }

            // Thêm weight hoặc productIds
            if ($request->hasShippingWeight()) {
                $data['shippingWeight'] = $request->getShippingWeight();
            } elseif ($request->hasProductIds()) {
                $data['productIds'] = $request->getProductIds();
            }

            // Gọi API
            $response = $this->httpService->callApi('/shipping/feeselfconnect', $data);

            $this->logger->info("ShippingModule::calculateFeeSelfConnect() API call completed", [
                'responseSize' => is_array($response) ? count($response) : 'unknown'
            ]);

            // Tạo response entity từ dữ liệu API
            return new ShippingFeeSelfConnectResponse($response);

        } catch (Exception $e) {
            $this->logger->error("ShippingModule::calculateFeeSelfConnect() error", [
                'error' => $e->getMessage(),
                'request' => $request->toArray()
            ]);
            throw $e;
        }
    }

    /**
     * Tính phí vận chuyển tự kết nối từ array data (convenience method)
     *
     * @param array $data Dữ liệu tính phí vận chuyển tự kết nối
     * @return ShippingFeeSelfConnectResponse Response chứa danh sách phí vận chuyển
     * @throws Exception Khi có lỗi xảy ra trong quá trình tính phí
     */
    public function calculateFeeSelfConnectFromArray(array $data): ShippingFeeSelfConnectResponse
    {
        $request = new ShippingFeeSelfConnectRequest($data);
        return $this->calculateFeeSelfConnect($request);
    }

    /**
     * Helper method để tạo entities với quản lý memory
     *
     * @param ShippingCarrierResponse $response Response cần xử lý
     */
    private function createEntitiesWithMemoryManagement(ShippingCarrierResponse $response): void
    {
        if ($response->hasCarriers()) {
            $carriers = $response->getCarriers();
            foreach ($carriers as $carrier) {
                if ($carrier instanceof ShippingCarrier) {
                    // Cleanup memory sau khi xử lý
                    unset($carrier);
                }
            }
            unset($carriers);
        }
    }
}
