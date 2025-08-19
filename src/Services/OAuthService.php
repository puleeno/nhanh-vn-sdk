<?php

namespace Puleeno\NhanhVn\Services;

use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Exceptions\ApiException;
use Puleeno\NhanhVn\Exceptions\ConfigurationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * OAuth Service để xử lý authentication
 */
class OAuthService
{
    private ClientConfig $config;
    private LoggerInterface $logger;

    public function __construct(ClientConfig $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

        /**
     * Exchange access code cho access token
     */
    public function exchangeAccessCode(string $accessCode): array
    {
        try {
            $url = $this->config->getBaseUrl() . '/api/oauth/access_token';

            $data = [
                'version' => $this->config->getApiVersion(),
                'appId' => $this->config->getAppId(),
                'secretKey' => $this->config->getSecretKey(),
                'accessCode' => $accessCode
            ];

            $client = new Client([
                'timeout' => $this->config->getTimeout(),
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'verify' => false, // Disable SSL verification for development
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]
            ]);

            $this->logger->info('Exchanging access code for token', [
                'url' => $url,
                'appId' => $this->config->getAppId(),
                'accessCode' => substr($accessCode, 0, 10) . '...'
            ]);

            $response = $client->post($url, [
                'form_params' => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!is_array($result)) {
                throw new ApiException('Response không hợp lệ từ Nhanh.vn API');
            }

            if ($result['code'] !== 1) {
                $message = is_array($result['message'] ?? '')
                    ? implode(', ', $result['message'])
                    : ($result['message'] ?? 'Unknown error');
                throw new ApiException($message);
            }

            $this->logger->info('Successfully exchanged access code for token');

            return $result;
        } catch (RequestException $e) {
            $this->logger->error('Request failed when exchanging access code', [
                'error' => $e->getMessage(),
                'url' => $url ?? 'unknown'
            ]);
            throw new ApiException('Không thể kết nối đến Nhanh.vn API: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error when exchanging access code', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Lấy OAuth URL
     */
    public function getOAuthUrl(string $returnLink): string
    {
        try {
            $params = [
                'version' => $this->config->getApiVersion(),
                'appId' => $this->config->getAppId(),
                'returnLink' => $returnLink
            ];

            // Thêm businessId nếu có
            if ($this->config->getBusinessId()) {
                $params['businessId'] = $this->config->getBusinessId();
            }

            $this->logger->info('Generated OAuth URL', [
                'params' => array_merge($params, ['returnLink' => '***hidden***'])
            ]);

            return 'https://nhanh.vn/oauth?' . http_build_query($params);
        } catch (\Exception $e) {
            $this->logger->error('Error generating OAuth URL', [
                'error' => $e->getMessage()
            ]);

            // Fallback nếu có lỗi
            $fallbackParams = [
                'version' => '2.0',
                'appId' => $this->config->getAppId() ?? 'unknown',
                'returnLink' => $returnLink
            ];

            return 'https://nhanh.vn/oauth?' . http_build_query($fallbackParams);
        }
    }
}
