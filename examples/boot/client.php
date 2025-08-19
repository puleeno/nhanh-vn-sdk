<?php
/**
 * Boot file để khởi tạo NhanhVnClient
 * File này sẽ được include bởi các examples để có sẵn client instance
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Examples\OAuthExample;
use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Services\Logger\MonologAdapter;
use Puleeno\NhanhVn\Services\Logger\NullLogger;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Khởi tạo và return NhanhVnClient instance
 *
 * @param bool $useLogger Có sử dụng Monolog logger hay không (default: false)
 * @param string $logLevel Log level cho Monolog (default: 'INFO')
 * @return NhanhVnClient
 * @throws Exception
 */
function bootNhanhVnClient(bool $useLogger = false, string $logLevel = 'INFO'): NhanhVnClient
{
    try {
        // Khởi tạo OAuthExample để lấy config
        $app = new OAuthExample();

        // Kiểm tra access token
        $accessToken = $app->getStoredAccessToken();
        if (!$accessToken) {
            throw new Exception('Chưa có access token. Hãy chạy OAuth flow trước!');
        }

        // Lấy config từ OAuthExample
        $config = $app->getConfig();

        // Tạo SDK config array
        $sdkConfigArray = [
            'appId' => $config['appId'],
            'businessId' => $config['businessId'],
            'accessToken' => $accessToken,
            'apiVersion' => '2.0'
        ];

        // Tạo ClientConfig object
        $sdkConfig = new ClientConfig($sdkConfigArray);

        // Khởi tạo NhanhVnClient
        $client = NhanhVnClient::getInstance($sdkConfig);

        // Nếu cần sử dụng logger
        if ($useLogger) {
            $logger = createMonologLogger($logLevel);
            $client->setLogger($logger);
        }

        return $client;

    } catch (Exception $e) {
        throw new Exception('Không thể khởi tạo NhanhVnClient: ' . $e->getMessage(), 0, $e);
    }
}

/**
 * Tạo Monolog Logger với configuration đầy đủ
 *
 * @param string $logLevel Log level
 * @return MonologAdapter
 */
function createMonologLogger(string $logLevel = 'INFO'): MonologAdapter
{
    // Tạo Monolog Logger
    $monologLogger = new Logger('nhanh-vn-sdk');

    // Map log level string sang Monolog constant
    $levelMap = [
        'DEBUG' => Logger::DEBUG,
        'INFO' => Logger::INFO,
        'WARNING' => Logger::WARNING,
        'ERROR' => Logger::ERROR,
        'CRITICAL' => Logger::CRITICAL
    ];

    $level = $levelMap[strtoupper($logLevel)] ?? Logger::INFO;

    // Thêm handlers
    // 1. Console output (stdout)
    $monologLogger->pushHandler(new StreamHandler('php://stdout', $level));

    // 2. File rotation (30 ngày)
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    $monologLogger->pushHandler(new RotatingFileHandler($logDir . '/nhanh-vn-sdk.log', 30, $level));

    // Set formatter
    $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n");
    foreach ($monologLogger->getHandlers() as $handler) {
        $handler->setFormatter($formatter);
    }

    return new MonologAdapter($monologLogger);
}

/**
 * Khởi tạo NhanhVnClient với NullLogger (không log gì)
 *
 * @return NhanhVnClient
 * @throws Exception
 */
function bootNhanhVnClientSilent(): NhanhVnClient
{
    return bootNhanhVnClient(false);
}

/**
 * Khởi tạo NhanhVnClient với Monolog Logger
 *
 * @param string $logLevel Log level
 * @return NhanhVnClient
 * @throws Exception
 */
function bootNhanhVnClientWithLogger(string $logLevel = 'INFO'): NhanhVnClient
{
    return bootNhanhVnClient(true, $logLevel);
}

/**
 * Lấy thông tin client hiện tại
 *
 * @return array
 */
function getClientInfo(): array
{
    try {
        $app = new OAuthExample();
        $accessToken = $app->getStoredAccessToken();
        $config = $app->getConfig();

        return [
            'hasAccessToken' => !empty($accessToken),
            'accessTokenPreview' => $accessToken ? substr($accessToken, 0, 20) . '...' : null,
            'appId' => $config['appId'] ?? null,
            'businessId' => $config['businessId'] ?? null,
            'apiVersion' => '2.0',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    } catch (Exception $e) {
        return [
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

/**
 * Kiểm tra xem client có sẵn sàng không
 *
 * @return bool
 */
function isClientReady(): bool
{
    try {
        $info = getClientInfo();
        return $info['hasAccessToken'] === true;
    } catch (Exception $e) {
        return false;
    }
}

// Auto-boot client nếu được gọi trực tiếp
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    try {
        $client = bootNhanhVnClient();
        echo "✅ NhanhVnClient đã được khởi tạo thành công!\n";
        echo "📋 Client Info: " . json_encode(getClientInfo(), JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "❌ Lỗi khởi tạo NhanhVnClient: " . $e->getMessage() . "\n";
        exit(1);
    }
}
