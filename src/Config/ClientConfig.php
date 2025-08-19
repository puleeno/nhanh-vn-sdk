<?php

namespace Puleeno\NhanhVn\Config;

use Puleeno\NhanhVn\Exceptions\ConfigurationException;

/**
 * Cấu hình cho Nhanh.vn Client
 */
class ClientConfig
{
    private const DEFAULT_API_DOMAIN = 'https://pos.open.nhanh.vn';
    private const DEFAULT_API_VERSION = '2.0';
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_RETRY_ATTEMPTS = 3;
    private const DEFAULT_RATE_LIMIT = 150; // requests per 30 seconds

    private string $appId;
    private ?string $secretKey;
    private ?string $returnLink;
    private ?string $businessId;
    private ?string $accessToken;
    private string $apiDomain;
    private string $apiVersion;
    private int $timeout;
    private int $retryAttempts;
    private int $rateLimit;
    private bool $enableLogging;
    private string $logLevel;

    public function __construct(array $config = [])
    {
        $this->validateRequiredConfig($config);

        $this->appId = $config['appId'];
        $this->secretKey = $config['secretKey'] ?? null;
        $this->returnLink = $config['returnLink'] ?? null;
        $this->businessId = $config['businessId'] ?? null;
        $this->accessToken = $config['accessToken'] ?? null;
        $this->apiDomain = $config['apiDomain'] ?? self::DEFAULT_API_DOMAIN;
        $this->apiVersion = $config['apiVersion'] ?? self::DEFAULT_API_VERSION;
        $this->timeout = $config['timeout'] ?? self::DEFAULT_TIMEOUT;
        $this->retryAttempts = $config['retry_attempts'] ?? self::DEFAULT_RETRY_ATTEMPTS;
        $this->rateLimit = $config['rate_limit'] ?? self::DEFAULT_RATE_LIMIT;
        $this->enableLogging = $config['enable_logging'] ?? false;
        $this->logLevel = $config['log_level'] ?? 'info';

        $this->validateConfig();
    }

    /**
     * Kiểm tra các tham số bắt buộc cho API calls
     */
    private function validateRequiredConfig(array $config): void
    {
        // AppId luôn bắt buộc
        if (!isset($config['appId']) || empty($config['appId'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc: appId");
        }

        // Kiểm tra xem có phải OAuth flow không
        $isOAuthFlow = isset($config['secretKey']) && !empty($config['secretKey']) &&
                      isset($config['returnLink']) && !empty($config['returnLink']);

        if ($isOAuthFlow) {
            // OAuth flow - chỉ cần appId, secretKey, returnLink
            // Không cần businessId và accessToken
            return;
        }

        // API calls - cần businessId và accessToken
        if (!isset($config['businessId']) || empty($config['businessId'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc: businessId");
        }

        if (!isset($config['accessToken']) || empty($config['accessToken'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc: accessToken");
        }
    }

    /**
     * Kiểm tra các tham số bắt buộc cho Get Access Code
     */
    public function validateAccessCodeConfig(array $config): void
    {
        // Get Access Code bắt buộc: appId, secretKey, redirectUrl
        if (!isset($config['appId']) || empty($config['appId'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc cho Get Access Code: appId");
        }

        if (!isset($config['secretKey']) || empty($config['secretKey'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc cho Get Access Code: secretKey");
        }

        if (!isset($config['redirectUrl']) || empty($config['redirectUrl'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc cho Get Access Code: redirectUrl");
        }
    }

    /**
     * Kiểm tra các tham số bắt buộc cho Get Access Token
     */
    public function validateAccessTokenConfig(array $config): void
    {
        // Get Access Token bắt buộc: secretKey, version, appId, accessCode
        if (!isset($config['secretKey']) || empty($config['secretKey'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc cho Get Access Token: secretKey");
        }

        if (!isset($config['version']) || empty($config['version'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc cho Get Access Token: version");
        }

        if (!isset($config['appId']) || empty($config['appId'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc cho Get Access Token: appId");
        }

        if (!isset($config['accessCode']) || empty($config['accessCode'])) {
            throw new ConfigurationException("Thiếu tham số bắt buộc cho Get Access Token: accessCode");
        }
    }

    /**
     * Kiểm tra tính hợp lệ của cấu hình
     */
    private function validateConfig(): void
    {
        // Chỉ validate returnLink nếu nó được cung cấp
        if ($this->returnLink !== null && !filter_var($this->returnLink, FILTER_VALIDATE_URL)) {
            throw new ConfigurationException('Return link không hợp lệ');
        }

        if ($this->timeout < 1 || $this->timeout > 300) {
            throw new ConfigurationException('Timeout phải từ 1 đến 300 giây');
        }

        if ($this->retryAttempts < 0 || $this->retryAttempts > 10) {
            throw new ConfigurationException('Retry attempts phải từ 0 đến 10');
        }

        if ($this->rateLimit < 1 || $this->rateLimit > 1000) {
            throw new ConfigurationException('Rate limit phải từ 1 đến 1000 requests/30s');
        }

        if (!in_array($this->logLevel, ['debug', 'info', 'warning', 'error'])) {
            throw new ConfigurationException('Log level không hợp lệ');
        }
    }

    // Getters
    public function getAppId(): string { return $this->appId; }
    public function getSecretKey(): ?string { return $this->secretKey; }
    public function getReturnLink(): ?string { return $this->returnLink; }
    public function getBusinessId(): ?string { return $this->businessId; }
    public function getAccessToken(): ?string { return $this->accessToken; }
    public function getApiDomain(): string { return $this->apiDomain; }
    public function getApiVersion(): string { return $this->apiVersion; }
    public function getTimeout(): int { return $this->timeout; }
    public function getRetryAttempts(): int { return $this->retryAttempts; }
    public function getRateLimit(): int { return $this->rateLimit; }
    public function isLoggingEnabled(): bool { return $this->enableLogging; }
    public function getLogLevel(): string { return $this->logLevel; }

    /**
     * Lấy base URL cho API
     */
    public function getBaseUrl(): string
    {
        return $this->apiDomain;
    }

    /**
     * Lấy OAuth URL cho Get Access Code
     */
    public function getOAuthUrl(): string
    {
        if ($this->returnLink === null) {
            throw new ConfigurationException('Redirect URL không được cung cấp. Không thể tạo OAuth URL.');
        }

        $params = [
            'version' => $this->apiVersion,
            'appId' => $this->appId,
            'returnLink' => $this->returnLink
        ];

        return 'https://nhanh.vn/oauth?' . http_build_query($params);
    }

    /**
     * Kiểm tra xem config có hợp lệ cho API calls không
     */
    public function isValid(): bool
    {
        if (empty($this->appId)) {
            return false;
        }

        // Kiểm tra OAuth flow
        if (!empty($this->secretKey) && !empty($this->returnLink)) {
            return true;
        }

        // Kiểm tra API calls
        return !empty($this->businessId) && !empty($this->accessToken);
    }
}
