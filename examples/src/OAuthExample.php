<?php

namespace Examples;

use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Exceptions\ConfigurationException;

/**
 * OAuth Example Application
 *
 * Demo OAuth flow của Nhanh.vn SDK v2.0
 */
class OAuthExample
{
    private string $configFile;
    private array $config;
    private ?NhanhVnClient $client = null;
    private string $tokenFile;

    public function __construct()
    {
        $this->configFile = __DIR__ . '/../auth.json';
        $this->tokenFile = __DIR__ . '/../tokens.json';
        $this->loadConfig();
    }

        /**
     * Load cấu hình từ file auth.json
     */
    private function loadConfig(): void
    {
        if (!file_exists($this->configFile)) {
            throw new \RuntimeException("File cấu hình {$this->configFile} không tồn tại!");
        }

        $this->config = json_decode(file_get_contents($this->configFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("File cấu hình JSON không hợp lệ!");
        }

        // Validate required fields for OAuth initiation
        $required = ['appId', 'secretKey', 'redirectUrl'];
        foreach ($required as $field) {
            if (empty($this->config[$field])) {
                throw new \RuntimeException("Thiếu trường bắt buộc: {$field}");
            }
        }

        // businessId và environment có thể để trống ban đầu
        if (empty($this->config['businessId'])) {
            $this->config['businessId'] = null;
        }

        if (empty($this->config['environment'])) {
            $this->config['environment'] = 'sandbox';
        }
    }

        /**
     * Hiển thị thông tin server
     */
    public function showServerInfo(): void
    {
        echo '<div class="container">';
        echo '<h1>🚀 Nhanh.vn SDK v2.0 - OAuth Example</h1>';
        echo '<hr>';

        echo '<div class="section">';
        echo '<h2>📋 Thông tin Server</h2>';
        echo '<ul>';
        echo '<li><strong>URL:</strong> http://localhost:8000</li>';
        echo '<li><strong>Port:</strong> 8000</li>';
        echo '<li><strong>PHP Version:</strong> ' . PHP_VERSION . '</li>';
        echo '<li><strong>Server Time:</strong> ' . date('Y-m-d H:i:s') . '</li>';
        echo '<li><strong>Config File:</strong> ' . basename($this->configFile) . '</li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>⚙️ Cấu hình OAuth</h2>';
        echo '<ul>';
        echo '<li><strong>App ID:</strong> ' . htmlspecialchars($this->config['appId']) . '</li>';
        echo '<li><strong>Business ID:</strong> ' . htmlspecialchars($this->config['businessId'] ?? 'Chưa có (sẽ có sau khi xác thực)') . '</li>';
        echo '<li><strong>Environment:</strong> ' . htmlspecialchars($this->config['environment']) . '</li>';
        echo '<li><strong>Redirect URL:</strong> ' . htmlspecialchars($this->config['redirectUrl']) . '</li>';
        echo '</ul>';
        echo '</div>';
    }

        /**
     * Hiển thị link OAuth để user click
     */
    public function showOAuthLink(): void
    {
        $oauthUrl = $this->getOAuthUrl();

        echo '<div class="section">';
        echo '<h2>🔐 OAuth Authorization</h2>';
        echo '<p>Click vào link bên dưới để authorize ứng dụng:</p>';

        echo '<div class="oauth-link">';
        echo '<a href="' . htmlspecialchars($oauthUrl) . '" class="btn btn-primary" target="_blank">📱 Authorize với Nhanh.vn</a>';
        echo '</div>';

        echo '<div class="oauth-info">';
        echo '<p><strong>OAuth URL:</strong></p>';
        echo '<input type="text" value="' . htmlspecialchars($oauthUrl) . '" readonly class="url-input" onclick="this.select()">';
        echo '<p><small>Hoặc copy link này vào browser</small></p>';
        echo '</div>';

        echo '<p><strong>Sau khi authorize, bạn sẽ được redirect về:</strong> ' . htmlspecialchars($this->config['redirectUrl']) . '</p>';
        echo '</div>';
    }

    /**
     * Tạo OAuth URL
     */
    private function getOAuthUrl(): string
    {
        try {
            $client = $this->initializeClient();
            if ($client) {
                // Sử dụng SDK method
                return $client->getOAuthUrl($this->config['redirectUrl']);
            }
        } catch (\Exception $e) {
            // Log error nhưng không throw
            error_log("SDK Error in getOAuthUrl: " . $e->getMessage());
        }

        // Fallback nếu SDK chưa sẵn sàng
        $baseUrl = 'https://nhanh.vn/oauth';
        $params = [
            'version' => '2.0',
            'appId' => $this->config['appId'],
            'returnLink' => $this->config['redirectUrl']
        ];

        // Thêm businessId nếu có
        if (isset($this->config['businessId'])) {
            $params['businessId'] = $this->config['businessId'];
        }

        return $baseUrl . '?' . http_build_query($params);
    }

        /**
     * Hiển thị trạng thái hiện tại
     */
    public function showCurrentStatus(): void
    {
        echo '<div class="section">';
        echo '<h2>📊 Trạng thái hiện tại</h2>';

        if (file_exists($this->tokenFile)) {
            $tokens = json_decode(file_get_contents($this->tokenFile), true);
            if (isset($tokens['access_token'])) {
                echo '<div class="status success">';
                echo '<h3>✅ Đã có access token</h3>';
                echo '<ul>';
                echo '<li><strong>Token:</strong> ' . htmlspecialchars(substr($tokens['access_token'], 0, 20)) . '...</li>';
                echo '<li><strong>Expires:</strong> ' . htmlspecialchars($tokens['expires_at'] ?? 'N/A') . '</li>';
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<div class="status error">';
                echo '<h3>❌ Chưa có access token</h3>';
                echo '</div>';
            }
        } else {
            echo '<div class="status error">';
            echo '<h3>❌ Chưa có access token</h3>';
            echo '</div>';
        }

        echo '<div class="section">';
        echo '<h3>💡 Hướng dẫn</h3>';
        echo '<ol>';
        echo '<li>Click vào link OAuth ở trên</li>';
        echo '<li>Đăng nhập và authorize ứng dụng</li>';
        echo '<li>Bạn sẽ được redirect về callback URL</li>';
        echo '<li>Access token sẽ được lưu tự động</li>';
        echo '</ol>';
        echo '</div>';

        echo '<div class="section">';
        echo '<p><strong>🔄 Để test lại:</strong> <a href="http://localhost:8000/callback.php">http://localhost:8000/callback.php</a></p>';
        echo '</div>';
        echo '</div>';
    }

        /**
     * Xử lý callback từ Nhanh.vn OAuth
     */
    public function handleCallback(): void
    {
        echo '<div class="container">';
        echo '<h1>🔄 Xử lý OAuth Callback</h1>';
        echo '<hr>';

        // Lấy access code từ query parameters (Nhanh.vn sử dụng accessCode)
        $accessCode = $_GET['accessCode'] ?? $_GET['access_code'] ?? null;
        $error = $_GET['error'] ?? null;

        if ($error) {
            echo '<div class="status error">';
            echo '<h2>❌ Lỗi OAuth</h2>';
            echo '<p><strong>Lỗi:</strong> ' . htmlspecialchars($error) . '</p>';
            echo '<p><strong>Mô tả:</strong> ' . htmlspecialchars($_GET['error_description'] ?? 'Không có mô tả') . '</p>';
            echo '</div>';
            echo '</div>';
            return;
        }

        if (!$accessCode) {
            echo '<div class="status error">';
            echo '<h2>❌ Không nhận được accessCode từ callback</h2>';
            echo '<p><strong>Lưu ý:</strong> Nhanh.vn sử dụng parameter <code>accessCode</code> (không phải <code>access_code</code>)</p>';
            echo '<p><strong>Query parameters nhận được:</strong></p>';
            echo '<pre>' . htmlspecialchars(json_encode($_GET, JSON_PRETTY_PRINT)) . '</pre>';
            echo '<p><strong>Expected parameters:</strong></p>';
            echo '<ul>';
            echo '<li><code>accessCode</code> - Access code từ Nhanh.vn</li>';
            echo '<li><code>error</code> - Mã lỗi (nếu có)</li>';
            echo '<li><code>error_description</code> - Mô tả lỗi (nếu có)</li>';
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            return;
        }

        echo '<div class="status success">';
        echo '<h2>✅ Nhận được accessCode từ Nhanh.vn</h2>';
        echo '<div class="oauth-info">';
        echo '<p><strong>Access Code:</strong></p>';
        echo '<input type="text" value="' . htmlspecialchars($accessCode) . '" readonly class="url-input" onclick="this.select()">';
        echo '<p><small>Access code này sẽ được đổi lấy access token</small></p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>🔄 Đang đổi access_code lấy access_token...</h2>';

                try {
            // Đổi access code lấy access token sử dụng SDK
            $results = $this->exchangeAccessCode($accessCode);

            // Lưu token vào file
            $this->saveTokens($results['accessToken'], $results['businessId']);

            echo '<div class="status success">';
            echo '<h3>🎉 Thành công! Đã lưu access token</h3>';
            echo '<ul>';
            echo '<li><strong>Token:</strong> ' . htmlspecialchars(substr($results['accessToken'], 0, 20)) . '...</li>';
            echo '<li><strong>File:</strong> ' . htmlspecialchars(basename($this->tokenFile)) . '</li>';
            echo '</ul>';
            echo '</div>';

            echo '<div class="section">';
            echo '<h3>📱 Bây giờ bạn có thể sử dụng SDK để gọi API:</h3>';
            echo '<ul>';
            echo '<li><a href="http://localhost:8000">Truy cập trang chính</a></li>';
            echo '<li>Hoặc sử dụng trong code PHP</li>';
            echo '</ul>';
            echo '</div>';

        } catch (\Exception $e) {
            echo '<div class="status error">';
            echo '<h3>❌ Lỗi khi đổi access code</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

            /**
     * Đổi access code lấy access token sử dụng SDK
     */
    private function exchangeAccessCode(string $accessCode): array
    {
        // Sử dụng SDK để đổi access code lấy access token
        try {
            // Tạo config tạm thời để khởi tạo client
            $tempConfig = new \Puleeno\NhanhVn\Config\ClientConfig([
                'appId' => $this->config['appId'],
                'secretKey' => $this->config['secretKey'],
                'apiVersion' => '2.0',
                'baseUrl' => 'https://pos.open.nhanh.vn'
            ]);

            // Khởi tạo client tạm thời
            $tempClient = \Puleeno\NhanhVn\Client\NhanhVnClient::getInstance($tempConfig);

            // Sử dụng OAuth service của SDK
            $result = $tempClient->oauth()->exchangeAccessCode($accessCode);

            // Debug: Log response để xem cấu trúc
            error_log("Nhanh.vn API Response via SDK: " . json_encode($result));

            // Hiển thị debug info trực tiếp trên web
            echo '<div class="section">';
            echo '<h3>🔍 Debug Info (via SDK):</h3>';
            echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . '</pre>';
            echo '</div>';

            return $result;

        } catch (\Exception $e) {
            throw new \RuntimeException("SDK Error: " . $e->getMessage());
        }
    }

        /**
     * Lưu access token vào file
     */
    private function saveTokens(string $accessToken, $businessId = null): void
    {
        $tokens = [
            'access_token' => $accessToken,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')), // Giả sử token có hạn 30 ngày
            'app_id' => $this->config['appId'],
            'business_id' => $businessId // Sử dụng businessId từ parameter
        ];

        // Cập nhật cấu hình nếu có businessId từ OAuth response
        if (empty($this->config['businessId']) && $businessId) {
            $this->config['businessId'] = $businessId;
        }

        file_put_contents($this->tokenFile, json_encode($tokens, JSON_PRETTY_PRINT));
    }

    /**
     * Lấy cấu hình
     */
    public function getConfig(): array
    {
        // Cập nhật businessId từ tokens.json nếu có
        if (file_exists($this->tokenFile)) {
            $tokens = json_decode(file_get_contents($this->tokenFile), true);
            if (isset($tokens['business_id']) && $tokens['business_id']) {
                $this->config['businessId'] = $tokens['business_id'];
            }
        }

        return $this->config;
    }

    /**
     * Lấy access token đã lưu
     */
    public function getStoredAccessToken(): ?string
    {
        if (file_exists($this->tokenFile)) {
            $tokens = json_decode(file_get_contents($this->tokenFile), true);
            return $tokens['access_token'] ?? null;
        }
        return null;
    }

    /**
     * Khởi tạo SDK client với access token
     */
    public function initializeClient(): ?NhanhVnClient
    {
        $accessToken = $this->getStoredAccessToken();
        if (!$accessToken) {
            return null;
        }

        try {
            $config = new ClientConfig([
                'appId' => $this->config['appId'],
                'businessId' => $this->config['businessId'],
                'accessToken' => $accessToken
            ]);

            $this->client = NhanhVnClient::getInstance($config);
            return $this->client;

        } catch (ConfigurationException $e) {
            echo "❌ Lỗi cấu hình SDK: " . $e->getMessage() . "\n";
            return null;
        }
    }
}
