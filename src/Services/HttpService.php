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
 * HTTP Service để gọi API Nhanh.vn
 */
class HttpService
{
    private Client $httpClient;
    private ClientConfig $config;
    private LoggerInterface $logger;

    public function __construct(ClientConfig $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->httpClient = new Client([
            'timeout' => $this->config->getTimeout(),
            'verify' => false, // Disable SSL verification for development
        ]);
    }

    /**
     * Gọi API Nhanh.vn
     */
    public function callApi(string $endpoint, array|string|int $data = []): array
    {
        try {
            // Chuẩn bị request data theo format Nhanh.vn
            $requestData = [
                'version' => $this->config->getApiVersion(),
                'appId' => $this->config->getAppId(),
                'businessId' => $this->config->getBusinessId(),
                'accessToken' => $this->config->getAccessToken(),
            ];

            if (!empty($data)) {
                if (is_array($data)) {
                    $requestData['data'] = json_encode($data);
                } else {
                    $requestData['data'] = $data;
                }
            }

            // Debug log
            $this->logger->info("HttpService::callApi() - Endpoint: {$endpoint}");
            $this->logger->debug("HttpService::callApi() - Request data", $requestData);


            $response = $this->httpClient->post($this->config->getBaseUrl() . $endpoint, [
                'multipart' => $this->arrayToMultipart($requestData)
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            // Debug log
            $this->logger->debug("HttpService::callApi() - Response", ['body' => $responseBody]);

            // Kiểm tra response
            if ($responseData === null) {
                throw new ApiException(
                    "Không thể parse JSON response",
                    0,
                    null,
                    $response->getStatusCode(),
                    $responseBody
                );
            }

            if (!isset($responseData['code'])) {
                throw new ApiException(
                    "Response không có field 'code'",
                    0,
                    null,
                    $response->getStatusCode(),
                    $responseBody
                );
            }

            if ($responseData['code'] !== 1) {
                $messages = isset($responseData['messages']) ? implode(', ', $responseData['messages']) : 'Unknown error';
                throw new ApiException(
                    "API Error: " . $messages,
                    $responseData['code'] ?? 0,
                    null,
                    $response->getStatusCode(),
                    $responseBody,
                    $responseData['errorCode'] ?? '',
                    $responseData['errorData'] ?? []
                );
            }

            return $responseData;

        } catch (GuzzleException $e) {
            throw new NetworkException("Network error: " . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            throw new ApiException("API error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Chuyển array thành multipart format cho Guzzle
     */
    private function arrayToMultipart(array $data): array
    {
        $multipart = [];
        foreach ($data as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value
            ];
        }
        return $multipart;
    }
}
