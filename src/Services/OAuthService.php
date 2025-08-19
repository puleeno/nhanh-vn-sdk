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

    public function __construct(ClientConfig $config)
    {
        $this->config = $config;
    }

        /**
     * Exchange access code cho access token
     */
    public function exchangeAccessCode(string $accessCode): array
    {
        $url = $this->config->getBaseUrl() . '/oauth/access_token';

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

        try {
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

            return $result;
        } catch (RequestException $e) {
            throw new ApiException('Không thể kết nối đến Nhanh.vn API: ' . $e->getMessage());
        }
    }

    /**
     * Lấy OAuth URL
     */
    public function getOAuthUrl(string $returnLink): string
    {
        $params = [
            'version' => $this->config->getApiVersion(),
            'appId' => $this->config->getAppId(),
            'returnLink' => $returnLink
        ];

        return 'https://nhanh.vn/oauth?' . http_build_query($params);
    }
}
