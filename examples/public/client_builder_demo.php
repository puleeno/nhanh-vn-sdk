<?php
/**
 * Demo Nhanh Client Builder
 *
 * File này demo các cách khác nhau để tạo NhanhVnClient sử dụng NhanhClientBuilder
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Puleeno\NhanhVn\Client\NhanhClientBuilder;

// Thiết lập error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Nhanh Client Builder Demo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .demo-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .info {
            color: #17a2b8;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Nhanh Client Builder Demo</h1>
        <p class="subtitle">Demo các cách khác nhau để tạo NhanhVnClient với syntax gọn gàng</p>
        <hr>

        <div class="demo-section">
            <h2>🎯 Demo 1: Tạo Client Cơ Bản</h2>
            <p>Sử dụng Builder Pattern với fluent interface:</p>

            <div class="code-block">
$client = NhanhClientBuilder::create()
    ->withAppId('demo_app_id')
    ->withBusinessId('demo_business_id')
    ->withAccessToken('demo_access_token')
    ->build();
            </div>

            <?php
            try {
                $client = NhanhClientBuilder::create()
                    ->withAppId('demo_app_id')
                    ->withBusinessId('demo_business_id')
                    ->withAccessToken('demo_access_token')
                    ->build();

                echo '<p class="success">✅ Client được tạo thành công!</p>';
                echo '<p><strong>Client Info:</strong></p>';
                echo '<ul>';
                echo '<li>App ID: ' . $client->getAppId() . '</li>';
                echo '<li>Business ID: ' . $client->getBusinessId() . '</li>';
                echo '<li>API Version: ' . $client->getApiVersion() . '</li>';
                echo '<li>Configured: ' . ($client->isConfigured() ? 'Yes' : 'No') . '</li>';
                echo '</ul>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>🔧 Demo 2: Tạo Client với Logging</h2>
            <p>Thiết lập logging với Monolog:</p>

            <div class="code-block">
$client = NhanhClientBuilder::create()
    ->withAppId('demo_app_id')
    ->withBusinessId('demo_business_id')
    ->withAccessToken('demo_access_token')
    ->withLogger()
    ->withLogLevel('DEBUG')
    ->withLogFile('logs/demo.log')
    ->withConsoleLogging()
    ->build();
            </div>

            <?php
            try {
                $client = NhanhClientBuilder::create()
                    ->withAppId('demo_app_id')
                    ->withBusinessId('demo_business_id')
                    ->withAccessToken('demo_access_token')
                    ->withLogger()
                    ->withLogLevel('DEBUG')
                    ->withLogFile('logs/demo.log')
                    ->withConsoleLogging()
                    ->build();

                echo '<p class="success">✅ Client với logging được tạo thành công!</p>';
                echo '<p><strong>Logger Info:</strong></p>';
                echo '<ul>';
                echo '<li>Logger Type: ' . get_class($client->getLogger()) . '</li>';
                echo '<li>Log Level: DEBUG</li>';
                echo '<li>Log File: logs/demo.log</li>';
                echo '<li>Console Logging: Enabled</li>';
                echo '</ul>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>🌍 Demo 3: Environment Presets</h2>
            <p>Sử dụng các preset có sẵn cho development và production:</p>

            <div class="code-block">
// Development preset
$devClient = NhanhClientBuilder::create()
    ->withAppId('dev_app_id')
    ->withBusinessId('dev_business_id')
    ->withAccessToken('dev_access_token')
    ->forDevelopment()
    ->build();

// Production preset
$prodClient = NhanhClientBuilder::create()
    ->withAppId('prod_app_id')
    ->withBusinessId('prod_business_id')
    ->withAccessToken('prod_access_token')
    ->forProduction()
    ->build();
            </div>

            <?php
            try {
                // Development client
                $devClient = NhanhClientBuilder::create()
                    ->withAppId('dev_app_id')
                    ->withBusinessId('dev_business_id')
                    ->withAccessToken('dev_access_token')
                    ->forDevelopment()
                    ->build();

                echo '<p class="success">✅ Development Client được tạo thành công!</p>';
                echo '<p class="info">🔧 Development preset: Logging DEBUG, Console enabled, SSL disabled</p>';

                // Production client
                $prodClient = NhanhClientBuilder::create()
                    ->withAppId('prod_app_id')
                    ->withBusinessId('prod_business_id')
                    ->withAccessToken('prod_access_token')
                    ->forProduction()
                    ->build();

                echo '<p class="success">✅ Production Client được tạo thành công!</p>';
                echo '<p class="info">🚀 Production preset: Logging WARNING, File logging, SSL enabled</p>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>🚀 Demo 4: Static Convenience Methods</h2>
            <p>Sử dụng các method tiện ích để tạo client nhanh:</p>

            <div class="code-block">
// Tạo client cơ bản
$client = NhanhClientBuilder::createBasic(
    'demo_app_id',
    'demo_business_id',
    'demo_access_token'
);

// Tạo client cho development
$devClient = NhanhClientBuilder::createDevelopment(
    'dev_app_id',
    'dev_business_id',
    'dev_access_token'
);

// Tạo client cho production
$prodClient = NhanhClientBuilder::createProduction(
    'prod_app_id',
    'prod_business_id',
    'prod_access_token'
);
            </div>

            <?php
            try {
                // Basic client
                $basicClient = NhanhClientBuilder::createBasic(
                    'basic_app_id',
                    'basic_business_id',
                    'basic_access_token'
                );
                echo '<p class="success">✅ Basic Client được tạo thành công!</p>';

                // Development client
                $devClient = NhanhClientBuilder::createDevelopment(
                    'dev_app_id',
                    'dev_business_id',
                    'dev_access_token'
                );
                echo '<p class="success">✅ Development Client được tạo thành công!</p>';

                // Production client
                $prodClient = NhanhClientBuilder::createProduction(
                    'prod_app_id',
                    'prod_business_id',
                    'prod_access_token'
                );
                echo '<p class="success">✅ Production Client được tạo thành công!</p>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>🔐 Demo 5: OAuth Client</h2>
            <p>Tạo client cho OAuth flow:</p>

            <div class="code-block">
$oauthClient = NhanhClientBuilder::fromOAuth()
    ->withAppId('oauth_app_id')
    ->withSecretKey('oauth_secret_key')
    ->withRedirectUrl('https://demo.com/callback')
    ->build();
            </div>

            <?php
            try {
                $oauthClient = NhanhClientBuilder::fromOAuth()
                    ->withAppId('oauth_app_id')
                    ->withSecretKey('oauth_secret_key')
                    ->withRedirectUrl('https://demo.com/callback')
                    ->build();

                echo '<p class="success">✅ OAuth Client được tạo thành công!</p>';
                echo '<p><strong>OAuth Info:</strong></p>';
                echo '<ul>';
                echo '<li>App ID: ' . $oauthClient->getAppId() . '</li>';
                echo '<li>Redirect URL: https://demo.com/callback</li>';
                echo '<li>OAuth URL: ' . $oauthClient->getOAuthUrl('https://demo.com/callback') . '</li>';
                echo '</ul>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>📁 Demo 6: Từ File Config</h2>
            <p>Tạo client từ file JSON config:</p>

            <div class="code-block">
// Tạo file config.json
{
    "appId": "file_app_id",
    "businessId": "file_business_id",
    "accessToken": "file_access_token",
    "enableLogging": true,
    "logLevel": "INFO"
}

// Sử dụng trong code
$client = NhanhClientBuilder::fromConfigFile('config.json')->build();
            </div>

            <?php
            try {
                // Tạo file config tạm thời
                $configData = [
                    'appId' => 'file_app_id',
                    'businessId' => 'file_business_id',
                    'accessToken' => 'file_access_token',
                    'enableLogging' => true,
                    'logLevel' => 'INFO'
                ];

                $configFile = __DIR__ . '/temp_config.json';
                file_put_contents($configFile, json_encode($configData, JSON_PRETTY_PRINT));

                // Tạo client từ file config
                $fileClient = NhanhClientBuilder::fromConfigFile($configFile)->build();

                echo '<p class="success">✅ Client từ file config được tạo thành công!</p>';
                echo '<p><strong>Config File Info:</strong></p>';
                echo '<ul>';
                echo '<li>Config File: ' . basename($configFile) . '</li>';
                echo '<li>App ID: ' . $fileClient->getAppId() . '</li>';
                echo '<li>Business ID: ' . $fileClient->getBusinessId() . '</li>';
                echo '<li>Logging Enabled: ' . ($fileClient->getLogger() instanceof \Puleeno\NhanhVn\Services\Logger\NullLogger ? 'No' : 'Yes') . '</li>';
                echo '</ul>';

                // Xóa file config tạm thời
                unlink($configFile);

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>🌍 Demo 7: Từ Environment Variables</h2>
            <p>Tạo client từ environment variables:</p>

            <div class="code-block">
// Thiết lập environment variables
$_ENV['NHANH_APP_ID'] = 'env_app_id';
$_ENV['NHANH_BUSINESS_ID'] = 'env_business_id';
$_ENV['NHANH_ACCESS_TOKEN'] = 'env_access_token';
$_ENV['NHANH_LOG_LEVEL'] = 'DEBUG';

// Tạo client
$client = NhanhClientBuilder::fromEnvironment()->build();
            </div>

            <?php
            try {
                // Thiết lập environment variables tạm thời
                $_ENV['NHANH_APP_ID'] = 'env_app_id';
                $_ENV['NHANH_BUSINESS_ID'] = 'env_business_id';
                $_ENV['NHANH_ACCESS_TOKEN'] = 'env_access_token';
                $_ENV['NHANH_LOG_LEVEL'] = 'DEBUG';

                // Tạo client từ environment
                $envClient = NhanhClientBuilder::fromEnvironment()->build();

                echo '<p class="success">✅ Client từ environment được tạo thành công!</p>';
                echo '<p><strong>Environment Info:</strong></p>';
                echo '<ul>';
                echo '<li>App ID: ' . $envClient->getAppId() . '</li>';
                echo '<li>Business ID: ' . $envClient->getBusinessId() . '</li>';
                echo '<li>Access Token: ' . $envClient->getAccessToken() . '</li>';
                echo '<li>Log Level: DEBUG</li>';
                echo '</ul>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>⚠️ Demo 8: Validation và Error Handling</h2>
            <p>Demo validation tự động và error handling:</p>

            <div class="code-block">
try {
    $client = NhanhClientBuilder::create()
        ->withAppId('app_id')
        // Thiếu businessId và accessToken
        ->build();
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
            </div>

            <?php
            try {
                $client = NhanhClientBuilder::create()
                    ->withAppId('app_id')
                    // Thiếu businessId và accessToken
                    ->build();

                echo '<p class="success">✅ Client được tạo thành công (không nên xảy ra)</p>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi validation (đúng như mong đợi):</p>';
                echo '<div class="code-block">' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>🎯 Demo 9: Custom Configuration</h2>
            <p>Thiết lập configuration tùy chỉnh:</p>

            <div class="code-block">
$client = NhanhClientBuilder::create()
    ->withAppId('custom_app_id')
    ->withBusinessId('custom_business_id')
    ->withAccessToken('custom_access_token')
    ->withApiVersion('2.1')
    ->withTimeout(60)
    ->withRetryAttempts(5)
    ->withRateLimit(300)
    ->withLogger()
    ->withLogLevel('INFO')
    ->withLogFile('logs/custom.log')
    ->withConsoleLogging()
    ->withSSLValidation(false)
    ->build();
            </div>

            <?php
            try {
                $customClient = NhanhClientBuilder::create()
                    ->withAppId('custom_app_id')
                    ->withBusinessId('custom_business_id')
                    ->withAccessToken('custom_access_token')
                    ->withApiVersion('2.1')
                    ->withTimeout(60)
                    ->withRetryAttempts(5)
                    ->withRateLimit(300)
                    ->withLogger()
                    ->withLogLevel('INFO')
                    ->withLogFile('logs/custom.log')
                    ->withConsoleLogging()
                    ->withSSLValidation(false)
                    ->build();

                echo '<p class="success">✅ Custom Client được tạo thành công!</p>';
                echo '<p><strong>Custom Configuration:</strong></p>';
                echo '<ul>';
                echo '<li>API Version: ' . $customClient->getApiVersion() . '</li>';
                echo '<li>Timeout: 60 giây</li>';
                echo '<li>Retry Attempts: 5</li>';
                echo '<li>Rate Limit: 300 requests/30s</li>';
                echo '<li>Log Level: INFO</li>';
                echo '<li>Log File: logs/custom.log</li>';
                echo '<li>Console Logging: Enabled</li>';
                echo '<li>SSL Validation: Disabled</li>';
                echo '</ul>';

            } catch (Exception $e) {
                echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="demo-section">
            <h2>📊 Tổng kết</h2>
            <p><strong>Nhanh Client Builder</strong> cung cấp nhiều cách khác nhau để tạo NhanhVnClient:</p>

            <ul>
                <li>🎯 <strong>Builder Pattern</strong> - Fluent interface cho configuration phức tạp</li>
                <li>🚀 <strong>Static Convenience Methods</strong> - Tạo client nhanh cho use cases đơn giản</li>
                <li>🌍 <strong>Environment Presets</strong> - Cấu hình tự động cho development, production, testing</li>
                <li>📁 <strong>File Config</strong> - Tạo client từ file JSON</li>
                <li>🌍 <strong>Environment Variables</strong> - Tạo client từ environment</li>
                <li>🔐 <strong>OAuth Support</strong> - Hỗ trợ OAuth flow</li>
                <li>📝 <strong>Logging Integration</strong> - Tích hợp Monolog với nhiều tùy chọn</li>
                <li>✅ <strong>Auto Validation</strong> - Validate configuration tự động</li>
            </ul>

            <p class="info">💡 <strong>Tip:</strong> Sử dụng Builder Pattern cho configuration phức tạp và Static Methods cho configuration đơn giản!</p>
        </div>

        <div class="demo-section">
            <h2>🔗 Liên kết</h2>
            <p>
                <a href="index.php" class="btn btn-primary">← Quay về trang chủ</a>
                <a href="https://github.com/puleeno/nhanh-vn-sdk" class="btn btn-secondary" target="_blank">📚 GitHub Repository</a>
                <a href="../../docs/client-builder.md" class="btn btn-info" target="_blank">📖 Tài liệu chi tiết</a>
            </p>
        </div>
    </div>
</body>
</html>
