<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Entities\Shipping\Location;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchRequest;
use Puleeno\NhanhVn\Entities\Shipping\LocationSearchResponse;
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
 * Shipping Module (v3).
 *
 * - POST /v3.0/shipping/location  body: {filters:{locationVersion,type,parentId}}
 * - POST /v3.0/shipping/carrier   body: {}
 * - POST /v3.0/shipping/fee       body: {filters:{...}} (type 1: Nhanh, type 2: self-connect)
 */
class ShippingModule
{
    protected ShippingManager $manager;
    protected HttpService $httpService;
    protected LoggerInterface $logger;

    public function __construct(ShippingManager $manager, HttpService $httpService, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->httpService = $httpService;
        $this->logger = $logger;
    }

    /**
     * Tìm kiếm địa điểm (v3: /v3.0/shipping/location).
     * @param array $criteria ['type'=>CITY|DISTRICT|WARD,'parentId'=>int,'locationVersion'=>'v1'|'v2'] or v3 {filters:...}
     */
    public function search(array $criteria): LocationSearchResponse
    {
        $this->logger->debug("ShippingModule::search() called", ['criteria' => $criteria]);
        try {
            $endpoint = $this->httpService->isV3() ? '/v3.0/shipping/location' : '/shipping/location';
            $payload = $this->buildLocationPayload($criteria);
            $response = $this->httpService->callApi($endpoint, $payload);
            // Normalize to legacy LocationSearchResponse expectations
            $data = $response['data'] ?? [];
            $normalized = [
                'code' => $response['code'] ?? 1,
                'data' => is_array($data) ? $data : [],
            ];
            return $this->manager->createLocationSearchResponse($normalized);
        } catch (Exception $e) {
            $this->logger->error("ShippingModule::search() error", ['error' => $e->getMessage(), 'criteria' => $criteria]);
            throw $e;
        }
    }

    private function buildLocationPayload(array $criteria): array
    {
        if (isset($criteria['filters']) && is_array($criteria['filters'])) {
            return $criteria;
        }
        $filters = [];
        $filters['locationVersion'] = $criteria['locationVersion'] ?? 'v1';
        $type = strtoupper((string) ($criteria['type'] ?? 'CITY'));
        $filters['type'] = $type;
        $parentId = $criteria['parentId'] ?? null;
        if ($parentId !== null && $parentId !== '') {
            $filters['parentId'] = (int) $parentId;
        }
        return ['filters' => $filters];
    }

    public function searchFromRequest(LocationSearchRequest $request): LocationSearchResponse
    {
        return $this->search($request->toArray());
    }

    public function searchCities(string $locationVersion = 'v1'): LocationSearchResponse
    {
        return $this->search(['type' => 'CITY', 'locationVersion' => $locationVersion]);
    }

    public function searchDistricts(int $cityId, string $locationVersion = 'v1'): LocationSearchResponse
    {
        return $this->search(['type' => 'DISTRICT', 'parentId' => $cityId, 'locationVersion' => $locationVersion]);
    }

    public function searchWards(int $parentId, string $locationVersion = 'v1'): LocationSearchResponse
    {
        // v1: parentId = districtId, v2: parentId = cityId
        return $this->search(['type' => 'WARD', 'parentId' => $parentId, 'locationVersion' => $locationVersion]);
    }

    public function searchByName(string $name, string $type = 'CITY', ?int $parentId = null): LocationSearchResponse
    {
        $criteria = ['type' => $type];
        if ($parentId !== null) $criteria['parentId'] = $parentId;
        $response = $this->search($criteria);
        if ($response->isSuccess() && !$response->isEmpty() && method_exists($response, 'filterByName')) {
            $filtered = $response->filterByName($name);
            return LocationSearchResponse::createSuccess($filtered);
        }
        return $response;
    }

    public function findById(int $id, string $type = 'CITY'): ?Location
    {
        $response = $this->search(['type' => $type]);
        if ($response->isSuccess() && method_exists($response, 'getLocationById')) {
            return $response->getLocationById($id);
        }
        return null;
    }

    public function validateSearchData(array $data): bool
    {
        return $this->manager->validateLocationSearchData($data);
    }

    public function getValidationErrors(array $data): array
    {
        return $this->manager->getLocationSearchValidationErrors($data);
    }

    public function createSearchRequest(array $data): LocationSearchRequest
    {
        return $this->manager->createLocationSearchRequest($data);
    }

    public function createEmptyResponse(): LocationSearchResponse
    {
        return $this->manager->createEmptyLocationSearchResponse();
    }

    public function createErrorResponse(array $messages): LocationSearchResponse
    {
        return $this->manager->createErrorLocationSearchResponse($messages);
    }

    /**
     * Lấy danh sách hãng vận chuyển (v3: /v3.0/shipping/carrier).
     */
    public function getCarriers(bool $forceRefresh = false): ShippingCarrierResponse
    {
        $this->logger->debug("ShippingModule::getCarriers() called", ['forceRefresh' => $forceRefresh]);
        try {
            $endpoint = $this->httpService->isV3() ? '/v3.0/shipping/carrier' : '/shipping/carrier';
            $response = $this->httpService->callApi($endpoint, []);
            return new ShippingCarrierResponse($response);
        } catch (Exception $e) {
            $this->logger->error("ShippingModule::getCarriers() error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getCarriersWithMemoryManagement(bool $forceRefresh = false): ShippingCarrierResponse
    {
        return $this->getCarriers($forceRefresh);
    }

    /**
     * Tính phí vận chuyển (v3: /v3.0/shipping/fee, body {filters:{...}}).
     */
    public function calculateFee(ShippingFeeRequest $request): ShippingFeeResponse
    {
        $this->logger->debug("ShippingModule::calculateFee() called");
        try {
            if (method_exists($request, 'isValid') && !$request->isValid()) {
                $errors = method_exists($request, 'getErrors') ? $request->getErrors() : [];
                throw new Exception('Request validation failed: ' . json_encode($errors));
            }
            $filters = $this->buildFeeFiltersFromRequest($request, 1);
            $endpoint = $this->httpService->isV3() ? '/v3.0/shipping/fee' : '/shipping/fee';
            $response = $this->httpService->callApi($endpoint, ['filters' => $filters]);
            return new ShippingFeeResponse($response);
        } catch (Exception $e) {
            $this->logger->error("ShippingModule::calculateFee() error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function calculateFeeFromArray(array $data): ShippingFeeResponse
    {
        // v3-native payload passthrough
        if (isset($data['filters']) && $this->httpService->isV3()) {
            $response = $this->httpService->callApi('/v3.0/shipping/fee', $data);
            return new ShippingFeeResponse($response);
        }
        $request = new ShippingFeeRequest($data);
        return $this->calculateFee($request);
    }

    /**
     * Tính phí vận chuyển tài khoản riêng (v3: /v3.0/shipping/fee with type=2).
     */
    public function calculateFeeSelfConnect(ShippingFeeSelfConnectRequest $request): ShippingFeeSelfConnectResponse
    {
        $this->logger->debug("ShippingModule::calculateFeeSelfConnect() called");
        try {
            if (method_exists($request, 'isValid') && !$request->isValid()) {
                $errors = method_exists($request, 'getErrors') ? $request->getErrors() : [];
                throw new Exception('Request validation failed: ' . json_encode($errors));
            }
            $filters = $this->buildSelfConnectFeeFilters($request);
            $endpoint = $this->httpService->isV3() ? '/v3.0/shipping/fee' : '/shipping/feeselfconnect';
            $payload = $this->httpService->isV3() ? ['filters' => $filters] : $filters;
            $response = $this->httpService->callApi($endpoint, $payload);
            return new ShippingFeeSelfConnectResponse($response);
        } catch (Exception $e) {
            $this->logger->error("ShippingModule::calculateFeeSelfConnect() error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function calculateFeeSelfConnectFromArray(array $data): ShippingFeeSelfConnectResponse
    {
        if (isset($data['filters']) && $this->httpService->isV3()) {
            $filters = $data['filters'];
            if (!isset($filters['type'])) $filters['type'] = 2;
            $response = $this->httpService->callApi('/v3.0/shipping/fee', ['filters' => $filters]);
            return new ShippingFeeSelfConnectResponse($response);
        }
        $request = new ShippingFeeSelfConnectRequest($data);
        return $this->calculateFeeSelfConnect($request);
    }

    private function buildFeeFiltersFromRequest(ShippingFeeRequest $request, int $type): array
    {
        $arr = method_exists($request, 'toArray') ? $request->toArray() : [];
        // best-effort mapping legacy fields to v3 filters
        $filters = ['type' => $type];
        foreach (['depotId','shippingWeight','price','totalCod','carrier','shippingFrom','shippingTo'] as $k) {
            if (isset($arr[$k])) $filters[$k] = $arr[$k];
        }
        // legacy name mappings
        if (!isset($filters['shippingWeight']) && isset($arr['weight'])) $filters['shippingWeight'] = (int) $arr['weight'];
        if (!isset($filters['totalCod']) && isset($arr['codMoney'])) $filters['totalCod'] = (int) $arr['codMoney'];
        return $filters;
    }

    private function buildSelfConnectFeeFilters(ShippingFeeSelfConnectRequest $request): array
    {
        $arr = method_exists($request, 'toArray') ? $request->toArray() : [];
        $filters = ['type' => 2];
        foreach (['depotId','shippingWeight','price','totalCod','shippingFrom','shippingTo'] as $k) {
            if (isset($arr[$k])) $filters[$k] = $arr[$k];
        }
        if (!isset($filters['shippingWeight']) && isset($arr['weight'])) $filters['shippingWeight'] = (int) $arr['weight'];
        if (!isset($filters['totalCod']) && isset($arr['codMoney'])) $filters['totalCod'] = (int) $arr['codMoney'];
        // carrier info for self-connect
        $carrier = $arr['carrier'] ?? [];
        if (!is_array($carrier)) $carrier = [];
        if (isset($arr['carrierId']) && !isset($carrier['id'])) $carrier['id'] = (int) $arr['carrierId'];
        if (!empty($carrier)) $filters['carrier'] = $carrier;
        return $filters;
    }
}
