<?php

namespace Puleeno\NhanhVn\Client;

use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Modules\ProductModule;
use Puleeno\NhanhVn\Modules\OAuthModule;
use Puleeno\NhanhVn\Modules\CustomerModule;
use Puleeno\NhanhVn\Managers\ProductManager;
use Puleeno\NhanhVn\Managers\CustomerManager;
use Puleeno\NhanhVn\Repositories\ProductRepository;
use Puleeno\NhanhVn\Repositories\CustomerRepository;
use Puleeno\NhanhVn\Services\ProductService;
use Puleeno\NhanhVn\Services\CustomerService;
use Puleeno\NhanhVn\Services\OAuthService;
use Puleeno\NhanhVn\Services\CacheService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Services\Logger\NullLogger;
use Puleeno\NhanhVn\Exceptions\ConfigurationException;

/**
 * Nhanh.vn API Client
 */
class NhanhVnClient
{
    private static ?self $instance = null;
    private ClientConfig $config;
    private ProductModule $products;
    private OAuthModule $oauth;
    private CustomerModule $customers;
    private LoggerInterface $logger;

    private function __construct(ClientConfig $config)
    {
        $this->config = $config;
        $this->logger = new NullLogger(); // Default null logger
        $this->initializeModules();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(ClientConfig $config): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * Initialize all modules
     */
    private function initializeModules(): void
    {
        // Initialize services
        $cacheService = new CacheService();
        $httpService = new \Puleeno\NhanhVn\Services\HttpService($this->config, $this->logger);

        // Initialize repositories
        $productRepository = new ProductRepository($cacheService);
        $customerRepository = new CustomerRepository();

        // Initialize services
        $productService = new ProductService($productRepository, $cacheService);
        $customerService = new CustomerService($customerRepository);
        $oauthService = new OAuthService($this->config, $this->logger);

        // Initialize managers
        $productManager = new ProductManager($productRepository, $productService);
        $customerManager = new CustomerManager($customerService);

        // Initialize modules
        $this->products = new ProductModule($productManager, $httpService, $this->logger);
        $this->customers = new CustomerModule($customerManager, $httpService, $this->logger);
        $this->oauth = new OAuthModule($oauthService, $this->logger);
    }

    /**
     * Get configuration
     */
    public function getConfig(): ClientConfig
    {
        return $this->config;
    }

    /**
     * Get products module
     */
    public function products(): ProductModule
    {
        return $this->products;
    }

    /**
     * Get customers module
     */
    public function customers(): CustomerModule
    {
        return $this->customers;
    }

    /**
     * Get OAuth module
     */
    public function oauth(): OAuthModule
    {
        return $this->oauth;
    }

    /**
     * Set logger for the client
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        // Re-initialize modules with new logger
        $this->initializeModules();

        return $this;
    }

    /**
     * Get current logger
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Get API base URL
     */
    public function getApiBaseUrl(): string
    {
        return $this->config->getApiBaseUrl();
    }

    /**
     * Get API version
     */
    public function getApiVersion(): string
    {
        return $this->config->getApiVersion();
    }

    /**
     * Get app ID
     */
    public function getAppId(): string
    {
        return $this->config->getAppId();
    }

    /**
     * Get business ID
     */
    public function getBusinessId(): string
    {
        return $this->config->getBusinessId();
    }

    /**
     * Get access token
     */
    public function getAccessToken(): string
    {
        return $this->config->getAccessToken();
    }

    /**
     * Check if client is configured
     */
    public function isConfigured(): bool
    {
        return $this->config->isValid();
    }

    /**
     * Get OAuth authorization URL
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

    /**
     * Exchange access code for access token
     */
    public function exchangeAccessCode(string $accessCode): array
    {
        // TODO: Implement OAuth token exchange
        // POST https://open.nhanh.vn/api/oauth/access_token
        // Content-Type: application/x-www-form-urlencoded

        return [
            'success' => false,
            'message' => 'Not implemented yet'
        ];
    }

    /**
     * Make API request
     */
    public function request(string $endpoint, array $data = [], string $method = 'POST'): array
    {
        // TODO: Implement API request logic
        // All API calls use POST with multipart/form-data
        // Common parameters: version, appId, businessId, accessToken, data (JSON string)

        return [
            'success' => false,
            'message' => 'Not implemented yet'
        ];
    }

    /**
     * Get client info
     */
    public function getInfo(): array
    {
        return [
            'api_base_url' => $this->getApiBaseUrl(),
            'api_version' => $this->getApiVersion(),
            'app_id' => $this->getAppId(),
            'business_id' => $this->getBusinessId(),
            'configured' => $this->isConfigured(),
            'modules' => [
                'products' => 'ProductModule'
            ]
        ];
    }
}
