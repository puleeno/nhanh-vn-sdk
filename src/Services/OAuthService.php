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
    private ?Client $httpClient;

    public function __construct(ClientConfig $config, LoggerInterface $logger, ?Client $httpClient = null)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->httpClient = $httpClient;
    }

    protected function createHttpClient(): Client
    {
        if ($this->httpClient) {
            return $this->httpClient;
        }
        return new Client([
            'timeout' => $this->config->getTimeout(),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ],
        ]);
    }

    /**
     * Exchange access code for access token (v3).
     * POST https://pos.open.nhanh.vn/v3.0/app/getaccesstoken?appId=...
     * Header: Authorization: <secretKey>
     * Body: {accessCode, verifyCode}
     */
    public function exchangeAccessCode(string $accessCode, ?string $verifyCode = null): array
    {
        try {
            $appId = $this->config->getAppId();
            $secretKey = $this->config->getSecretKey();
            if (empty($secretKey)) {
                throw new ConfigurationException('Thiếu secretKey để đổi access token');
            }

            $url = rtrim($this->config->getBaseUrl(), '/') . '/v3.0/app/getaccesstoken?appId=' . rawurlencode((string) $appId);

            $body = ['accessCode' => $accessCode];
            $verifyCode = $verifyCode ?? (method_exists($this->config, 'getVerifyCode') ? $this->config->getVerifyCode() : null);
            if (!empty($verifyCode)) {
                $body['verifyCode'] = $verifyCode;
            }

            $client = $this->createHttpClient();

            $this->logger->info('Exchanging access code for token (v3)', [
                'url' => $url,
                'appId' => $appId,
                'accessCode' => substr($accessCode, 0, 10) . '...',
                'hasVerifyCode' => !empty($verifyCode),
            ]);

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $secretKey,
                ],
                'json' => $body,
            ]);
            $result = json_decode($response->getBody()->getContents(), true);

            if (!is_array($result)) {
                throw new ApiException('Response không hợp lệ từ Nhanh.vn API');
            }

            if (($result['code'] ?? 0) !== 1) {
                $messages = $result['messages'] ?? ($result['message'] ?? 'Unknown error');
                if (is_array($messages)) {
                    $messages = implode(', ', $messages);
                }
                throw new ApiException((string) $messages);
            }

            $this->logger->info('Successfully exchanged access code for token');

            // v3 wraps payload in data; normalize for callers expecting top-level fields
            $payload = $result['data'] ?? $result;
            if (is_array($payload)) {
                $normalized = array_merge($result, $payload);
                if (!isset($normalized['accessToken']) && isset($normalized['access_token'])) {
                    $normalized['accessToken'] = $normalized['access_token'];
                }
                if (!isset($normalized['businessId']) && isset($normalized['business_id'])) {
                    $normalized['businessId'] = $normalized['business_id'];
                }
                return $normalized;
            }

            return $result;
        } catch (RequestException $e) {
            $this->logger->error('Request failed when exchanging access code', [
                'error' => $e->getMessage(),
                'url' => $url ?? 'unknown',
            ]);
            throw new ApiException('Không thể kết nối đến Nhanh.vn API: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error when exchanging access code', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Lấy OAuth URL (v3 spec: appId, service, verifyCode, returnLink)
     */
    public function getOAuthUrl(string $returnLink, ?string $verifyCode = null, ?string $service = null): string
    {
        try {
            $verifyCode = $verifyCode ?? $this->config->getVerifyCode();
            $service = $service ?? (method_exists($this->config, 'getOauthService') ? $this->config->getOauthService() : 'pos');
            $params = [
                'appId' => $this->config->getAppId(),
                'service' => $service ?: 'pos',
                'returnLink' => $returnLink,
            ];
            if (!empty($verifyCode)) {
                $params['verifyCode'] = $verifyCode;
            }
            $this->logger->info('Generated OAuth URL', ['appId' => $params['appId'], 'service' => $service]);
            return 'https://nhanh.vn/oauth?' . http_build_query($params);
        } catch (\Exception $e) {
            $this->logger->error('Error generating OAuth URL', ['error' => $e->getMessage()]);
            $fallback = ['appId' => $this->config->getAppId() ?? '', 'service' => $service ?? 'pos', 'returnLink' => $returnLink];
            if (!empty($verifyCode)) { $fallback['verifyCode'] = $verifyCode; }
            return 'https://nhanh.vn/oauth?' . http_build_query($fallback);
        }
    }
}
