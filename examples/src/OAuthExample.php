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

        // Validate required fields
        $required = ['appId', 'secretKey', 'redirectUrl', 'businessId'];
        foreach ($required as $field) {
            if (empty($this->config[$field])) {
                throw new \RuntimeException("Thiếu trường bắt buộc: {$field}");
            }
        }
    }

    /**
     * Hiển thị thông tin server
     */
    public function showServerInfo(): void
    {
        echo "🚀 Nhanh.vn SDK v2.0 - OAuth Example\n";
        echo "=====================================\n\n";

        echo "📋 Thông tin Server:\n";
        echo "   - URL: http://localhost:8000\n";
        echo "   - Port: 8000\n";
        echo "   - PHP Version: " . PHP_VERSION . "\n";
        echo "   - Server Time: " . date('Y-m-d H:i:s') . "\n";
        echo "   - Config File: " . basename($this->configFile) . "\n\n";

        echo "⚙️  Cấu hình OAuth:\n";
        echo "   - App ID: " . $this->config['appId'] . "\n";
        echo "   - Business ID: " . $this->config['businessId'] . "\n";
        echo "   - Environment: " . $this->config['environment'] . "\n";
        echo "   - Redirect URL: " . $this->config['redirectUrl'] . "\n\n";
    }

    /**
     * Hiển thị link OAuth để user click
     */
    public function showOAuthLink(): void
    {
        $oauthUrl = $this->getOAuthUrl();

        echo "🔐 OAuth Authorization:\n";
        echo "   Click vào link bên dưới để authorize ứng dụng:\n\n";
        echo "   📱 " . $oauthUrl . "\n\n";

        echo "   Hoặc copy link này vào browser:\n";
        echo "   " . $oauthUrl . "\n\n";

        echo "   Sau khi authorize, bạn sẽ được redirect về: " . $this->config['redirectUrl'] . "\n\n";
    }

    /**
     * Tạo OAuth URL
     */
    private function getOAuthUrl(): string
    {
        $baseUrl = 'https://nhanh.vn/oauth';
        $params = [
            'version' => '2.0',
            'appId' => $this->config['appId'],
            'returnLink' => $this->config['redirectUrl']
        ];

        return $baseUrl . '?' . http_build_query($params);
    }

    /**
     * Hiển thị trạng thái hiện tại
     */
    public function showCurrentStatus(): void
    {
        echo "📊 Trạng thái hiện tại:\n";

        if (file_exists($this->tokenFile)) {
            $tokens = json_decode(file_get_contents($this->tokenFile), true);
            if (isset($tokens['access_token'])) {
                echo "   ✅ Đã có access token\n";
                echo "   🔑 Token: " . substr($tokens['access_token'], 0, 20) . "...\n";
                echo "   ⏰ Expires: " . ($tokens['expires_at'] ?? 'N/A') . "\n";
            } else {
                echo "   ❌ Chưa có access token\n";
            }
        } else {
            echo "   ❌ Chưa có access token\n";
        }

        echo "\n";
        echo "💡 Hướng dẫn:\n";
        echo "   1. Click vào link OAuth ở trên\n";
        echo "   2. Đăng nhập và authorize ứng dụng\n";
        echo "   3. Bạn sẽ được redirect về callback URL\n";
        echo "   4. Access token sẽ được lưu tự động\n\n";

        echo "🔄 Để test lại, truy cập: http://localhost:8000/callback\n\n";
    }

    /**
     * Xử lý callback từ Nhanh.vn OAuth
     */
    public function handleCallback(): void
    {
        echo "🔄 Xử lý OAuth Callback...\n\n";

        // Lấy access code từ query parameters
        $accessCode = $_GET['access_code'] ?? null;
        $error = $_GET['error'] ?? null;

        if ($error) {
            echo "❌ Lỗi OAuth: " . htmlspecialchars($error) . "\n";
            echo "   Mô tả: " . htmlspecialchars($_GET['error_description'] ?? 'Không có mô tả') . "\n\n";
            return;
        }

        if (!$accessCode) {
            echo "❌ Không nhận được access_code từ callback\n";
            echo "   Query parameters: " . json_encode($_GET) . "\n\n";
            return;
        }

        echo "✅ Nhận được access_code: " . substr($accessCode, 0, 20) . "...\n";
        echo "🔄 Đang đổi access_code lấy access_token...\n\n";

        try {
            // Đổi access code lấy access token
            $accessToken = $this->exchangeAccessCode($accessCode);

            // Lưu token vào file
            $this->saveTokens($accessToken);

            echo "🎉 Thành công! Đã lưu access token\n";
            echo "   🔑 Token: " . substr($accessToken, 0, 20) . "...\n";
            echo "   💾 Đã lưu vào file: " . basename($this->tokenFile) . "\n\n";

            echo "📱 Bây giờ bạn có thể sử dụng SDK để gọi API:\n";
            echo "   - Truy cập: http://localhost:8000\n";
            echo "   - Hoặc sử dụng trong code PHP\n\n";

        } catch (\Exception $e) {
            echo "❌ Lỗi khi đổi access code: " . $e->getMessage() . "\n\n";
        }
    }

    /**
     * Đổi access code lấy access token
     */
    private function exchangeAccessCode(string $accessCode): string
    {
        $url = 'https://pos.open.nhanh.vn/api/oauth/access_token';

        $data = [
            'version' => '2.0',
            'appId' => $this->config['appId'],
            'businessId' => $this->config['businessId'],
            'accessCode' => $accessCode
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: NhanhVnSDK/2.0'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("CURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("HTTP Error: {$httpCode}");
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON response");
        }

        if (isset($result['code']) && $result['code'] === 1) {
            if (isset($result['data']['access_token'])) {
                return $result['data']['access_token'];
            } else {
                throw new \RuntimeException("Response không chứa access_token: " . json_encode($result));
            }
        } else {
            $message = $result['messages'] ?? 'Unknown error';
            throw new \RuntimeException("API Error: " . implode(', ', $message));
        }
    }

    /**
     * Lưu access token vào file
     */
    private function saveTokens(string $accessToken): void
    {
        $tokens = [
            'access_token' => $accessToken,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')), // Giả sử token có hạn 30 ngày
            'app_id' => $this->config['appId'],
            'business_id' => $this->config['businessId']
        ];

        file_put_contents($this->tokenFile, json_encode($tokens, JSON_PRETTY_PRINT));
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
                'accessToken' => $accessToken,
                'environment' => $this->config['environment']
            ]);

            $this->client = NhanhVnClient::getInstance($config);
            return $this->client;

        } catch (ConfigurationException $e) {
            echo "❌ Lỗi cấu hình SDK: " . $e->getMessage() . "\n";
            return null;
        }
    }
}
