<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Managers;

use Puleeno\NhanhVn\Entities\Shipping\Location;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchRequest;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchResponse;
use Puleeno\NhanhVn\Repositories\ShippingRepository;
use Puleeno\NhanhVn\Services\HttpService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Exception;

/**
 * Shipping Manager - Quản lý business logic cho shipping và location APIs
 *
 * Manager này chịu trách nhiệm điều phối giữa Repository và Service layers,
 * xử lý business logic và validation cho các API liên quan đến shipping
 *
 * @package NhanhVn\Sdk\Managers
 * @author Nhanh.vn SDK Team
 */
class ShippingManager
{
    /** @var ShippingRepository Repository xử lý dữ liệu shipping */
    protected ShippingRepository $repository;

    /** @var HttpService Service xử lý HTTP requests */
    protected HttpService $httpService;

    /** @var LoggerInterface Logger để ghi log */
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param ShippingRepository $repository Repository xử lý dữ liệu shipping
     * @param HttpService $httpService Service xử lý HTTP requests
     * @param LoggerInterface $logger Logger để ghi log
     */
    public function __construct(
        ShippingRepository $repository,
        HttpService $httpService,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->httpService = $httpService;
        $this->logger = $logger;
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
        $this->logger->debug("ShippingManager::validateLocationSearchData() called", [
            'data' => $data
        ]);

        try {
            $isValid = $this->repository->validateLocationSearchData($data);

            $this->logger->debug("ShippingManager::validateLocationSearchData() result", [
                'isValid' => $isValid
            ]);

            return $isValid;

        } catch (Exception $e) {
            $this->logger->error("ShippingManager::validateLocationSearchData() error", [
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
        $this->logger->debug("ShippingManager::getLocationSearchValidationErrors() called", [
            'data' => $data
        ]);

        try {
            $errors = $this->repository->getLocationSearchValidationErrors($data);

            $this->logger->debug("ShippingManager::getLocationSearchValidationErrors() result", [
                'errorCount' => count($errors)
                ]);

            return $errors;

        } catch (Exception $e) {
            $this->logger->error("ShippingManager::getLocationSearchValidationErrors() error", [
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
    public function createLocationSearchRequest(array $data): LocationSearchRequest
    {
        $this->logger->debug("ShippingManager::createLocationSearchRequest() called", [
            'data' => $data
        ]);

        try {
            $request = $this->repository->createLocationSearchRequest($data);

            $this->logger->debug("ShippingManager::createLocationSearchRequest() - Request created successfully", [
                'type' => $request->getType(),
                'parentId' => $request->getParentId()
            ]);

            return $request;

        } catch (Exception $e) {
            $this->logger->error("ShippingManager::createLocationSearchRequest() error", [
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
        $this->logger->debug("ShippingManager::createLocationSearchResponse() called", [
            'dataSize' => count($apiData),
            'type' => $type
        ]);

        try {
            $response = $this->repository->createLocationSearchResponse($apiData, $type);

            $this->logger->debug("ShippingManager::createLocationSearchResponse() - Response created successfully", [
                'totalLocations' => $response->getCount(),
                'type' => $type
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logger->error("ShippingManager::createLocationSearchResponse() error", [
                'error' => $e->getMessage(),
                'dataSize' => count($apiData),
                'type' => $type
            ]);
            throw $e;
        }
    }

    /**
     * Tạo response rỗng cho tìm kiếm địa điểm
     *
     * @return LocationSearchResponse Response rỗng
     */
    public function createEmptyLocationSearchResponse(): LocationSearchResponse
    {
        $this->logger->debug("ShippingManager::createEmptyLocationSearchResponse() called");

        return LocationSearchResponse::createEmpty();
    }

    /**
     * Tạo response lỗi cho tìm kiếm địa điểm
     *
     * @param array $messages Danh sách thông báo lỗi
     * @return LocationSearchResponse Response lỗi
     */
    public function createErrorLocationSearchResponse(array $messages): LocationSearchResponse
    {
        $this->logger->debug("ShippingManager::createErrorLocationSearchResponse() called", [
            'messages' => $messages
        ]);

        return LocationSearchResponse::createError($messages);
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
        $this->logger->debug("ShippingManager::prepareLocationSearchData() called", [
            'data' => $data
        ]);

        try {
            $preparedData = $this->repository->prepareLocationSearchData($data);

            $this->logger->debug("ShippingManager::prepareLocationSearchData() - Data prepared successfully", [
                'preparedData' => $preparedData
            ]);

            return $preparedData;

        } catch (Exception $e) {
            $this->logger->error("ShippingManager::prepareLocationSearchData() error", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Gọi API tìm kiếm địa điểm
     *
     * @param array $data Dữ liệu tìm kiếm
     * @return LocationSearchResponse Response từ API
     * @throws Exception Khi có lỗi xảy ra trong quá trình gọi API
     */
    public function searchLocations(array $data): LocationSearchResponse
    {
        $this->logger->debug("ShippingManager::searchLocations() called", [
            'data' => $data
        ]);

        try {
            // Validate dữ liệu đầu vào
            if (!$this->validateLocationSearchData($data)) {
                $errors = $this->getLocationSearchValidationErrors($data);
                $this->logger->warning("ShippingManager::searchLocations() - Validation failed", [
                    'errors' => $errors
                ]);
                return $this->createErrorLocationSearchResponse($errors);
            }

            // Chuẩn bị dữ liệu để gửi API
            $preparedData = $this->prepareLocationSearchData($data);

            // TODO: Endpoint /api/shipping/location chưa tồn tại trong API thật của Nhanh.vn
            // Tạm thời sử dụng mock data để demo functionality
            $this->logger->info("ShippingManager::searchLocations() - Using mock data (API endpoint not available)");

            // Mock response data
            $mockData = $this->getMockLocationData($data['type'] ?? 'CITY', $data['parentId'] ?? null);
            $apiResponse = [
                'code' => 1,
                'messages' => [],
                'data' => $mockData
            ];

            // Parse response data
            $responseData = $apiResponse['data'] ?? [];
            $type = $data['type'] ?? 'CITY';

            $locationResponse = $this->createLocationSearchResponse($responseData, $type);

            $this->logger->debug("ShippingManager::searchLocations() - API call successful", [
                'totalLocations' => $locationResponse->getCount(),
                'type' => $type
            ]);

            return $locationResponse;

        } catch (Exception $e) {
            $this->logger->error("ShippingManager::searchLocations() error", [
                'error' => $e->getMessage(),
                'data' => $data
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
        $this->logger->debug("ShippingManager::searchCities() called");

        return $this->searchLocations(['type' => 'CITY']);
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
        $this->logger->debug("ShippingManager::searchDistricts() called", [
            'cityId' => $cityId
        ]);

        return $this->searchLocations([
            'type' => 'DISTRICT',
            'parentId' => $cityId
        ]);
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
        $this->logger->debug("ShippingManager::searchWards() called", [
            'districtId' => $districtId
        ]);

        return $this->searchLocations([
            'type' => 'WARD',
            'parentId' => $districtId
        ]);
    }

    /**
     * Tạo mock data cho location để demo functionality
     * TODO: Thay thế bằng API call thật khi endpoint có sẵn
     *
     * @param string $type Loại địa điểm (CITY, DISTRICT, WARD)
     * @param int|null $parentId ID của địa điểm cha
     * @return array Mock data
     */
    private function getMockLocationData(string $type, ?int $parentId = null): array
    {
        $this->logger->debug("ShippingManager::getMockLocationData() called", [
            'type' => $type,
            'parentId' => $parentId
        ]);

        switch ($type) {
            case 'CITY':
                return [
                    [
                        'id' => 1,
                        'name' => 'Hà Nội'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Hồ Chí Minh'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Đà Nẵng'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Hải Phòng'
                    ],
                    [
                        'id' => 5,
                        'name' => 'Cần Thơ'
                    ]
                ];

            case 'DISTRICT':
                if ($parentId === 1) { // Hà Nội
                    return [
                        [
                            'id' => 101,
                            'parentId' => 1,
                            'name' => 'Quận Hoàn Kiếm'
                        ],
                        [
                            'id' => 102,
                            'parentId' => 1,
                            'name' => 'Quận Hai Bà Trưng'
                        ],
                        [
                            'id' => 103,
                            'parentId' => 1,
                            'name' => 'Quận Ba Đình'
                        ],
                        [
                            'id' => 104,
                            'parentId' => 1,
                            'name' => 'Quận Đống Đa'
                        ]
                    ];
                } elseif ($parentId === 2) { // Hồ Chí Minh
                    return [
                        [
                            'id' => 201,
                            'parentId' => 2,
                            'name' => 'Quận 1'
                        ],
                        [
                            'id' => 202,
                            'parentId' => 2,
                            'name' => 'Quận 3'
                        ],
                        [
                            'id' => 203,
                            'parentId' => 2,
                            'name' => 'Quận 5'
                        ],
                        [
                            'id' => 204,
                            'parentId' => 2,
                            'name' => 'Quận 7'
                        ]
                    ];
                }
                return [];

            case 'WARD':
                if ($parentId === 101) { // Quận Hoàn Kiếm
                    return [
                        [
                            'id' => 1001,
                            'parentId' => 101,
                            'name' => 'Phường Hàng Bạc'
                        ],
                        [
                            'id' => 1002,
                            'parentId' => 101,
                            'name' => 'Phường Hàng Bồ'
                        ],
                        [
                            'id' => 1003,
                            'parentId' => 101,
                            'name' => 'Phường Hàng Gai'
                        ]
                    ];
                } elseif ($parentId === 201) { // Quận 1
                    return [
                        [
                            'id' => 2001,
                            'parentId' => 201,
                            'name' => 'Phường Bến Nghé'
                        ],
                        [
                            'id' => 2002,
                            'parentId' => 201,
                            'name' => 'Phường Bến Thành'
                        ],
                        [
                            'id' => 2003,
                            'parentId' => 201,
                            'name' => 'Phường Cầu Kho'
                        ]
                    ];
                }
                return [];

            default:
                return [];
        }
    }
}
