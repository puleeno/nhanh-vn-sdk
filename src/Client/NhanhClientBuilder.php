<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Client;

use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Services\Logger\MonologAdapter;
use Puleeno\NhanhVn\Services\Logger\NullLogger;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Exception;

/**
 * Nhanh Client Builder - Xây dựng NhanhVnClient với syntax gọn gàng và trực quan
 *
 * Sử dụng Builder Pattern để tạo client một cách dễ dàng và linh hoạt
 *
 * @example
 * // Cách sử dụng cơ bản
 * $client = NhanhClientBuilder::create()
 *     ->withAppId('your_app_id')
 *     ->withBusinessId('your_business_id')
 *     ->withAccessToken('your_access_token')
 *     ->build();
 *
 * // Với logger
 * $client = NhanhClientBuilder::create()
 *     ->withAppId('your_app_id')
 *     ->withBusinessId('your_business_id')
 *     ->withAccessToken('your_access_token')
 *     ->withLogger()
 *     ->withLogLevel('DEBUG')
 *     ->withLogFile('logs/nhanh.log')
 *     ->build();
 *
 * // Từ file config
 * $client = NhanhClientBuilder::fromConfigFile('config/nhanh.json')->build();
 *
 * // Từ environment variables
 * $client = NhanhClientBuilder::fromEnvironment()->build();
 *
 * // Từ OAuth flow
 * $client = NhanhClientBuilder::fromOAuth()
 *     ->withAppId('your_app_id')
 *     ->withSecretKey('your_secret_key')
 *     ->withRedirectUrl('https://your-app.com/callback')
 *     ->build();
 *
 * @package Puleeno\NhanhVn\Client
 * @author Puleeno Nguyen
 * @version 0.4.0
 */
class NhanhClientBuilder
{
    private string $appId = '';
    private string $businessId = '';
    private string $accessToken = '';
    private ?string $secretKey = null;
    private ?string $redirectUrl = null;
    private string $apiVersion = '2.0';
    private string $apiDomain = 'https://pos.open.nhanh.vn';
    private int $timeout = 30;
    private int $retryAttempts = 3;
    private int $rateLimit = 150;
    private bool $enableLogging = false;
    private string $logLevel = 'INFO';
    private ?string $logFile = null;
    private bool $logToConsole = false;
    private bool $logToFile = true;
    private int $logRotationDays = 30;
    private string $environment = 'production';
    private bool $useOAuth = false;
    private bool $validateSSL = true;

    /**
     * Constructor private - sử dụng static methods để tạo instance
     */
    private function __construct()
    {
        // Private constructor để enforce factory pattern
    }

    /**
     * Tạo builder instance mới
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Tạo builder từ file config JSON
     */
    public static function fromConfigFile(string $configPath): self
    {
        if (!file_exists($configPath)) {
            throw new Exception("Config file không tồn tại: {$configPath}");
        }

        $configData = json_decode(file_get_contents($configPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Config file JSON không hợp lệ: " . json_last_error_msg());
        }

        $builder = new self();
        return $builder->withConfigArray($configData);
    }

    /**
     * Tạo builder từ environment variables
     */
        public static function fromEnvironment(): self
    {
        $builder = new self();

        $appId = $_ENV['NHANH_APP_ID'] ?? $_SERVER['NHANH_APP_ID'] ?? '';
        $businessId = $_ENV['NHANH_BUSINESS_ID'] ?? $_SERVER['NHANH_BUSINESS_ID'] ?? '';
        $accessToken = $_ENV['NHANH_ACCESS_TOKEN'] ?? $_SERVER['NHANH_ACCESS_TOKEN'] ?? '';
        $secretKey = $_ENV['NHANH_SECRET_KEY'] ?? $_SERVER['NHANH_SECRET_KEY'] ?? null;
        $apiVersion = $_ENV['NHANH_API_VERSION'] ?? $_SERVER['NHANH_API_VERSION'] ?? '2.0';
        $environment = $_ENV['NHANH_ENVIRONMENT'] ?? $_SERVER['NHANH_ENVIRONMENT'] ?? 'production';
        $timeout = (int)($_ENV['NHANH_TIMEOUT'] ?? $_SERVER['NHANH_TIMEOUT'] ?? 30);
        $logLevel = $_ENV['NHANH_LOG_LEVEL'] ?? $_SERVER['NHANH_LOG_LEVEL'] ?? 'INFO';

        $builder = $builder
            ->withAppId($appId)
            ->withBusinessId($businessId)
            ->withAccessToken($accessToken)
            ->withApiVersion($apiVersion)
            ->withEnvironment($environment)
            ->withTimeout($timeout)
            ->withLogLevel($logLevel);

        // Chỉ gọi withSecretKey nếu có giá trị
        if ($secretKey !== null) {
            $builder = $builder->withSecretKey($secretKey);
        }

        return $builder;
    }

    /**
     * Tạo builder cho OAuth flow
     */
    public static function fromOAuth(): self
    {
        $builder = new self();
        $builder->useOAuth = true;
        return $builder;
    }

    // ==================== CONFIGURATION METHODS ====================

    /**
     * Thiết lập App ID
     */
    public function withAppId(string $appId): self
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * Thiết lập Business ID
     */
    public function withBusinessId(string $businessId): self
    {
        $this->businessId = $businessId;
        return $this;
    }

    /**
     * Thiết lập Access Token
     */
    public function withAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Thiết lập Secret Key (cho OAuth)
     */
    public function withSecretKey(?string $secretKey): self
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * Thiết lập Redirect URL (cho OAuth)
     */
    public function withRedirectUrl(?string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Thiết lập API Version
     */
    public function withApiVersion(string $apiVersion): self
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    /**
     * Thiết lập API Domain
     */
    public function withApiDomain(string $apiDomain): self
    {
        $this->apiDomain = $apiDomain;
        return $this;
    }

    /**
     * Thiết lập Timeout
     */
    public function withTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Thiết lập Retry Attempts
     */
    public function withRetryAttempts(int $retryAttempts): self
    {
        $this->retryAttempts = $retryAttempts;
        return $this;
    }

    /**
     * Thiết lập Rate Limit
     */
    public function withRateLimit(int $rateLimit): self
    {
        $this->rateLimit = $rateLimit;
        return $this;
    }

    /**
     * Thiết lập Environment
     */
    public function withEnvironment(string $environment): self
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * Thiết lập SSL validation
     */
    public function withSSLValidation(bool $validateSSL): self
    {
        $this->validateSSL = $validateSSL;
        return $this;
    }

    // ==================== LOGGING METHODS ====================

    /**
     * Bật logging
     */
    public function withLogger(): self
    {
        $this->enableLogging = true;
        return $this;
    }

    /**
     * Thiết lập log level
     */
    public function withLogLevel(string $logLevel): self
    {
        $this->logLevel = strtoupper($logLevel);
        return $this;
    }

    /**
     * Thiết lập log file
     */
    public function withLogFile(string $logFile): self
    {
        $this->logFile = $logFile;
        return $this;
    }

    /**
     * Bật log ra console
     */
    public function withConsoleLogging(): self
    {
        $this->logToConsole = true;
        return $this;
    }

    /**
     * Bật log ra file
     */
    public function withFileLogging(): self
    {
        $this->logToFile = true;
        return $this;
    }

    /**
     * Thiết lập số ngày rotation cho log file
     */
    public function withLogRotation(int $days): self
    {
        $this->logRotationDays = $days;
        return $this;
    }

    // ==================== CONVENIENCE METHODS ====================

    /**
     * Thiết lập config từ array
     */
    public function withConfigArray(array $config): self
    {
        if (isset($config['appId'])) $this->appId = $config['appId'];
        if (isset($config['businessId'])) $this->businessId = $config['businessId'];
        if (isset($config['accessToken'])) $this->accessToken = $config['accessToken'];
        if (isset($config['secretKey'])) $this->secretKey = $config['secretKey'];
        if (isset($config['redirectUrl'])) $this->redirectUrl = $config['redirectUrl'];
        if (isset($config['apiVersion'])) $this->apiVersion = $config['apiVersion'];
        if (isset($config['apiDomain'])) $this->apiDomain = $config['apiDomain'];
        if (isset($config['timeout'])) $this->timeout = $config['timeout'];
        if (isset($config['retryAttempts'])) $this->retryAttempts = $config['retryAttempts'];
        if (isset($config['rateLimit'])) $this->rateLimit = $config['rateLimit'];
        if (isset($config['environment'])) $this->environment = $config['environment'];
        if (isset($config['enableLogging'])) $this->enableLogging = $config['enableLogging'];
        if (isset($config['logLevel'])) $this->logLevel = $config['logLevel'];
        if (isset($config['logFile'])) $this->logFile = $config['logFile'];
        if (isset($config['logToConsole'])) $this->logToConsole = $config['logToConsole'];
        if (isset($config['logToFile'])) $this->logToFile = $config['logToFile'];
        if (isset($config['logRotationDays'])) $this->logRotationDays = $config['logRotationDays'];
        if (isset($config['validateSSL'])) $this->validateSSL = $config['validateSSL'];

        return $this;
    }

    /**
     * Thiết lập cho development environment
     */
    public function forDevelopment(): self
    {
        return $this
            ->withEnvironment('development')
            ->withLogger()
            ->withLogLevel('DEBUG')
            ->withConsoleLogging()
            ->withSSLValidation(false);
    }

    /**
     * Thiết lập cho production environment
     */
    public function forProduction(): self
    {
        return $this
            ->withEnvironment('production')
            ->withLogLevel('WARNING')
            ->withFileLogging()
            ->withSSLValidation(true);
    }

    /**
     * Thiết lập cho testing environment
     */
    public function forTesting(): self
    {
        return $this
            ->withEnvironment('testing')
            ->withLogger()
            ->withLogLevel('DEBUG')
            ->withConsoleLogging()
            ->withSSLValidation(false);
    }

    // ==================== VALIDATION METHODS ====================

    /**
     * Validate configuration trước khi build
     */
    private function validateConfiguration(): void
    {
        $errors = [];

        if (empty($this->appId)) {
            $errors[] = 'App ID không được để trống';
        }

        if ($this->useOAuth) {
            // OAuth flow validation
            if (empty($this->secretKey)) {
                $errors[] = 'Secret Key không được để trống cho OAuth flow';
            }
            if (empty($this->redirectUrl)) {
                $errors[] = 'Redirect URL không được để trống cho OAuth flow';
            }
        } else {
            // API calls validation
            if (empty($this->businessId)) {
                $errors[] = 'Business ID không được để trống';
            }
            if (empty($this->accessToken)) {
                $errors[] = 'Access Token không được để trống';
            }
        }

        if (!empty($errors)) {
            throw new Exception("Configuration không hợp lệ:\n" . implode("\n", $errors));
        }
    }

    // ==================== BUILD METHOD ====================

        /**
     * Build và return NhanhVnClient instance
     */
    public function build(): NhanhVnClient
    {
        // Validate configuration
        $this->validateConfiguration();

        // Tạo config array cơ bản
        $configArray = [
            'appId' => $this->appId,
            'apiVersion' => $this->apiVersion,
            'apiDomain' => $this->apiDomain,
            'timeout' => $this->timeout,
            'retry_attempts' => $this->retryAttempts,
            'rate_limit' => $this->rateLimit,
            'enable_logging' => $this->enableLogging,
            'log_level' => strtolower($this->logLevel) // Convert to lowercase for ClientConfig
        ];

        if ($this->useOAuth) {
            // OAuth flow - chỉ cần appId, secretKey, redirectUrl
            if ($this->secretKey !== null) {
                $configArray['secretKey'] = $this->secretKey;
            }
            if ($this->redirectUrl !== null) {
                $configArray['returnLink'] = $this->redirectUrl;
            }
        } else {
            // API calls - cần businessId và accessToken
            $configArray['businessId'] = $this->businessId;
            $configArray['accessToken'] = $this->accessToken;
        }

        // Tạo ClientConfig
        $clientConfig = new ClientConfig($configArray);

        // Tạo NhanhVnClient
        $client = NhanhVnClient::getInstance($clientConfig);

        // Thiết lập logger nếu cần
        if ($this->enableLogging) {
            $logger = $this->createLogger();
            $client->setLogger($logger);
        }

        return $client;
    }

    /**
     * Tạo logger instance
     */
    private function createLogger(): LoggerInterface
    {
        if (!$this->enableLogging) {
            return new NullLogger();
        }

        // Tạo Monolog Logger
        $monologLogger = new Logger('nhanh-vn-sdk');

        // Map log level
        $levelMap = [
            'DEBUG' => Logger::DEBUG,
            'INFO' => Logger::INFO,
            'WARNING' => Logger::WARNING,
            'ERROR' => Logger::ERROR,
            'CRITICAL' => Logger::CRITICAL
        ];

        $level = $levelMap[strtoupper($this->logLevel)] ?? Logger::INFO;

        // Console handler
        if ($this->logToConsole) {
            $monologLogger->pushHandler(new StreamHandler('php://stdout', $level));
        }

        // File handler
        if ($this->logToFile) {
            $logFile = $this->logFile ?? 'logs/nhanh-vn-sdk.log';
            $logDir = dirname($logFile);

            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            $monologLogger->pushHandler(
                new RotatingFileHandler($logFile, $this->logRotationDays, $level)
            );
        }

        // Set formatter
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        );

        foreach ($monologLogger->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
        }

        return new MonologAdapter($monologLogger);
    }

    // ==================== STATIC CONVENIENCE METHODS ====================

    /**
     * Tạo client với config cơ bản
     */
    public static function createBasic(
        string $appId,
        string $businessId,
        string $accessToken
    ): NhanhVnClient {
        return self::create()
            ->withAppId($appId)
            ->withBusinessId($businessId)
            ->withAccessToken($accessToken)
            ->build();
    }

    /**
     * Tạo client với OAuth
     */
    public static function createOAuth(
        string $appId,
        string $secretKey,
        string $redirectUrl
    ): NhanhVnClient {
        return self::fromOAuth()
            ->withAppId($appId)
            ->withSecretKey($secretKey)
            ->withRedirectUrl($redirectUrl)
            ->build();
    }

    /**
     * Tạo client cho development
     */
    public static function createDevelopment(
        string $appId,
        string $businessId,
        string $accessToken
    ): NhanhVnClient {
        return self::create()
            ->withAppId($appId)
            ->withBusinessId($businessId)
            ->withAccessToken($accessToken)
            ->forDevelopment()
            ->build();
    }

    /**
     * Tạo client cho production
     */
    public static function createProduction(
        string $appId,
        string $businessId,
        string $accessToken
    ): NhanhVnClient {
        return self::create()
            ->withAppId($appId)
            ->withBusinessId($businessId)
            ->withAccessToken($accessToken)
            ->forProduction()
            ->build();
    }
}
