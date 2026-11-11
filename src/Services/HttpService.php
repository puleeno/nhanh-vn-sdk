<?php

namespace Puleeno\NhanhVn\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Exceptions\ApiException;
use Puleeno\NhanhVn\Exceptions\NetworkException;
use Exception;

/**
 * HTTP Service để gọi Nhanh.vn Open API v3.
 *
 * Spec v3 (common/3.api-requests.md):
 * - POST {baseUrl}{endpoint}
 * - Query: appId, businessId
 * - Header: Authorization = accessToken, Content-Type: application/json
 * - Body: JSON (filters/paginator/dataOptions, hoặc array cho một số API add)
 */
class HttpService
{
    private Client $httpClient;
    private ClientConfig $config;
    private LoggerInterface $logger;

    public function __construct(ClientConfig $config, LoggerInterface $logger, ?Client $httpClient = null)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->httpClient = $httpClient ?? new Client([
            'timeout' => $this->config->getTimeout(),
            'verify' => false, // disable SSL verification for local/dev environments
        ]);
    }

    /**
     * Gọi API Nhanh.vn.
     *
     * @param string $endpoint Endpoint path, e.g. "/v3.0/product/list" (v3) or legacy "/product/search"
     * @param array|string|int $data JSON body payload (array/object-like data). For legacy scalar, wrapped.
     */
    public function callApi(string $endpoint, array|string|int $data = []): array
    {
        try {
            $url = $this->buildUrl($endpoint);

            $headers = [
                'Content-Type' => 'application/json',
            ];
            $accessToken = $this->config->getAccessToken();
            if (!empty($accessToken)) {
                $headers['Authorization'] = $accessToken;
            }

            $body = $this->normalizeBody($data);

            $this->logger->info('HttpService::callApi() request', [
                'endpoint' => $endpoint,
                'url' => $url,
                'hasAuth' => !empty($accessToken),
            ]);
            $this->logger->debug('HttpService::callApi() payload', ['body' => $body]);

            $options = [
                'headers' => $headers,
                'query' => $this->buildQuery(),
            ];
            // Always send JSON body for v3 POST endpoints (empty object/array acceptable)
            $options['json'] = $body;

            $response = $this->httpClient->post($url, $options);

            $responseBody = (string) $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            $this->logger->debug('HttpService::callApi() response', ['body' => $responseBody]);

            if ($responseData === null || !is_array($responseData)) {
                throw new ApiException(
                    'Không thể parse JSON response',
                    0,
                    null,
                    $response->getStatusCode(),
                    $responseBody
                );
            }

            if (!array_key_exists('code', $responseData)) {
                throw new ApiException(
                    "Response không có field 'code'",
                    0,
                    null,
                    $response->getStatusCode(),
                    $responseBody
                );
            }

            if ((int) $responseData['code'] !== 1) {
                $messages = $this->extractErrorMessage($responseData);
                throw new ApiException(
                    'API Error: ' . $messages,
                    (int) ($responseData['code'] ?? 0),
                    null,
                    $response->getStatusCode(),
                    $responseBody,
                    (string) ($responseData['errorCode'] ?? ''),
                    is_array($responseData['errorData'] ?? null) ? $responseData['errorData'] : []
                );
            }

            return $responseData;
        } catch (GuzzleException $e) {
            throw new NetworkException('Network error: ' . $e->getMessage(), 0, $e);
        } catch (ApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ApiException('API error: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build full request URL. v3 endpoints are passed as full paths (e.g. /v3.0/product/list).
     */
    private function buildUrl(string $endpoint): string
    {
        $base = rtrim($this->config->getBaseUrl(), '/');
        if ($endpoint === '') {
            return $base;
        }
        if (strpos($endpoint, 'http://') === 0 || strpos($endpoint, 'https://') === 0) {
            return $endpoint;
        }
        // v3: endpoint already includes /v3.0/... ; legacy callers may pass /product/search
        if (strpos($endpoint, '/v3.0/') === 0) {
            return $base . $endpoint;
        }
        if (strpos($endpoint, '/v3.0') === 0) {
            return $base . $endpoint;
        }
        // legacy fallback -> prefix /api
        $path = '/' . ltrim($endpoint, '/');
        return $base . '/api' . $path;
    }

    /**
     * Query params required by v3: appId, businessId.
     */
    private function buildQuery(): array
    {
        $query = [
            'appId' => $this->config->getAppId(),
        ];
        $businessId = $this->config->getBusinessId();
        if (!empty($businessId)) {
            $query['businessId'] = $businessId;
        }
        return $query;
    }

    /**
     * @param array|string|int $data
     * @return array
     */
    private function normalizeBody($data): array
    {
        if (is_array($data)) {
            return $data;
        }
        if ($data === '' || $data === null) {
            return [];
        }
        // legacy scalar payload
        return ['data' => $data];
    }

    private function extractErrorMessage(array $responseData): string
    {
        $messages = $responseData['messages'] ?? ($responseData['message'] ?? 'Unknown error');
        if (is_array($messages)) {
            // can be list or associative field->error
            $flat = [];
            foreach ($messages as $k => $v) {
                if (is_array($v)) {
                    $v = implode(', ', $v);
                }
                $flat[] = is_string($k) ? ($k . ': ' . $v) : (string) $v;
            }
            return implode('; ', $flat);
        }
        return (string) $messages;
    }

    public function getApiVersionSafe(): string
    {
        return $this->config->getApiVersion() ?: '3.0';
    }

    public function isV3(): bool
    {
        return strpos($this->getApiVersionSafe(), '3') === 0;
    }

    /**
     * @deprecated kept for backward compatibility
     */
    private function arrayToMultipart(array $data): array
    {
        $multipart = [];
        foreach ($data as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => is_array($value) ? json_encode($value) : $value,
            ];
        }
        return $multipart;
    }
}
