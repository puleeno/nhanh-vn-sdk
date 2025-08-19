<?php
/**
 * Example: Thêm ảnh sản phẩm từ CDN bên ngoài
 *
 * File này demo cách sử dụng SDK để thêm ảnh sản phẩm
 * từ CDN bên ngoài vào hệ thống Nhanh.vn
 *
 * @package Puleeno\NhanhVn\Examples
 * @author Puleeno
 * @since 1.0.0
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Services\Logger\MonologAdapter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Cấu hình logging
$logger = new Logger('nhanh-vn-sdk');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/nhanh-vn-sdk.log', Logger::DEBUG));

// Cấu hình client
$config = new ClientConfig([
    'appId' => 'your_app_id',
    'businessId' => 'your_business_id',
    'accessToken' => 'your_access_token',
    'apiVersion' => '2.0',
    'baseUrl' => 'https://pos.open.nhanh.vn',
    'timeout' => 30,
    'retryAttempts' => 3,
    'retryDelay' => 1000,
]);

// Khởi tạo client
$client = new NhanhVnClient($config, new MonologAdapter($logger));

// Lấy product module
$productModule = $client->products();

echo "<h1>Demo: Thêm ảnh sản phẩm từ CDN bên ngoài</h1>\n";
echo "<hr>\n";

try {
    // Example 1: Thêm ảnh cho một sản phẩm
    echo "<h2>1. Thêm ảnh cho một sản phẩm</h2>\n";

    $singleProductData = [
        'productId' => 312311,
        'externalImages' => [
            'https://external.cdn.com/product/image1.jpg',
            'https://external.cdn.com/product/image2.jpg',
            'https://external.cdn.com/product/image3.jpg'
        ],
        'mode' => 'update' // Có thể là 'update' hoặc 'deleteall'
    ];

    echo "<h3>Dữ liệu sản phẩm:</h3>\n";
    echo "<pre>" . json_encode($singleProductData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

    // Validate dữ liệu trước khi gửi
    if ($productModule->validateExternalImageRequest($singleProductData)) {
        echo "<p style='color: green;'>✅ Dữ liệu hợp lệ</p>\n";

        // Gọi API thêm ảnh
        $response = $productModule->addExternalImage($singleProductData);

        echo "<h3>Kết quả:</h3>\n";
        echo "<p><strong>Thành công:</strong> " . ($response->isSuccess() ? 'Có' : 'Không') . "</p>\n";
        echo "<p><strong>Mã kết quả:</strong> " . $response->getCode() . "</p>\n";
        echo "<p><strong>Số sản phẩm đã xử lý:</strong> " . $response->getTotalProcessedProducts() . "</p>\n";

        if ($response->isSuccess()) {
            echo "<p><strong>ID sản phẩm đã xử lý:</strong> " . implode(', ', $response->getAllProcessedProductIds()) . "</p>\n";
        } else {
            echo "<p><strong>Lỗi:</strong> " . $response->getAllMessagesAsString() . "</p>\n";
        }

        echo "<h3>Thông tin tóm tắt:</h3>\n";
        echo "<pre>" . json_encode($response->getSummary(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

    } else {
        echo "<p style='color: red;'>❌ Dữ liệu không hợp lệ</p>\n";
    }

    echo "<hr>\n";

    // Example 2: Thêm ảnh cho nhiều sản phẩm cùng lúc (batch)
    echo "<h2>2. Thêm ảnh cho nhiều sản phẩm cùng lúc (Batch)</h2>\n";

    $batchProductsData = [
        [
            'productId' => 312311,
            'externalImages' => [
                'https://external.cdn.com/product/image1.jpg',
                'https://external.cdn.com/product/image2.jpg'
            ],
            'mode' => 'update'
        ],
        [
            'productId' => 312312,
            'externalImages' => [
                'https://external.cdn.com/product/image3.jpg',
                'https://external.cdn.com/product/image4.jpg',
                'https://external.cdn.com/product/image5.jpg'
            ],
            'mode' => 'update'
        ],
        [
            'productId' => 312313,
            'externalImages' => [
                'https://external.cdn.com/product/image6.jpg'
            ],
            'mode' => 'deleteall' // Xóa tất cả ảnh cũ và thêm ảnh mới
        ]
    ];

    echo "<h3>Dữ liệu batch:</h3>\n";
    echo "<pre>" . json_encode($batchProductsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

    // Validate dữ liệu batch
    $validationErrors = $productModule->validateExternalImageRequests($batchProductsData);

    if (empty($validationErrors)) {
        echo "<p style='color: green;'>✅ Tất cả dữ liệu đều hợp lệ</p>\n";

        // Gọi API thêm ảnh batch
        $batchResponse = $productModule->addExternalImages($batchProductsData);

        echo "<h3>Kết quả batch:</h3>\n";
        echo "<p><strong>Thành công:</strong> " . ($batchResponse->isSuccess() ? 'Có' : 'Không') . "</p>\n";
        echo "<p><strong>Mã kết quả:</strong> " . $batchResponse->getCode() . "</p>\n";
        echo "<p><strong>Số sản phẩm đã xử lý:</strong> " . $batchResponse->getTotalProcessedProducts() . "</p>\n";

        if ($batchResponse->isSuccess()) {
            echo "<p><strong>ID sản phẩm đã xử lý:</strong> " . implode(', ', $batchResponse->getAllProcessedProductIds()) . "</p>\n";
        } else {
            echo "<p><strong>Lỗi:</strong> " . $batchResponse->getAllMessagesAsString() . "</p>\n";
        }

        echo "<h3>Thông tin tóm tắt batch:</h3>\n";
        echo "<pre>" . json_encode($batchResponse->getSummary(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

    } else {
        echo "<p style='color: red;'>❌ Có lỗi validation:</p>\n";
        echo "<ul>\n";
        foreach ($validationErrors as $error) {
            echo "<li style='color: red;'>{$error}</li>\n";
        }
        echo "</ul>\n";
    }

    echo "<hr>\n";

    // Example 3: Demo các trường hợp lỗi validation
    echo "<h2>3. Demo các trường hợp lỗi validation</h2>\n";

    $invalidDataExamples = [
        [
            'name' => 'Thiếu productId',
            'data' => [
                'externalImages' => ['https://example.com/image.jpg']
            ]
        ],
        [
            'name' => 'Thiếu externalImages',
            'data' => [
                'productId' => 123
            ]
        ],
        [
            'name' => 'productId không phải số',
            'data' => [
                'productId' => 'invalid_id',
                'externalImages' => ['https://example.com/image.jpg']
            ]
        ],
        [
            'name' => 'Quá nhiều ảnh (tối đa 20)',
            'data' => [
                'productId' => 123,
                'externalImages' => array_fill(0, 25, 'https://example.com/image.jpg')
            ]
        ],
        [
            'name' => 'URL ảnh không hợp lệ',
            'data' => [
                'productId' => 123,
                'externalImages' => ['invalid_url', 'https://example.com/image.jpg']
            ]
        ],
        [
            'name' => 'Mode không hợp lệ',
            'data' => [
                'productId' => 123,
                'externalImages' => ['https://example.com/image.jpg'],
                'mode' => 'invalid_mode'
            ]
        ]
    ];

    foreach ($invalidDataExamples as $example) {
        echo "<h4>{$example['name']}:</h4>\n";
        echo "<pre>" . json_encode($example['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

        $isValid = $productModule->validateExternalImageRequest($example['data']);
        echo "<p style='color: " . ($isValid ? 'green' : 'red') . ";'>" .
             ($isValid ? '✅ Hợp lệ' : '❌ Không hợp lệ') . "</p>\n";
        echo "<br>\n";
    }

    echo "<hr>\n";

    // Example 4: Demo giới hạn batch size
    echo "<h2>4. Demo giới hạn batch size (tối đa 10 sản phẩm)</h2>\n";

    $largeBatchData = array_fill(0, 12, [
        'productId' => rand(100000, 999999),
        'externalImages' => ['https://example.com/image.jpg']
    ]);

    echo "<p><strong>Số sản phẩm trong batch:</strong> " . count($largeBatchData) . "</p>\n";

    try {
        $largeBatchResponse = $productModule->addExternalImages($largeBatchData);
        echo "<p style='color: green;'>✅ Batch xử lý thành công</p>\n";
    } catch (\InvalidArgumentException $e) {
        echo "<p style='color: red;'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }

} catch (Exception $e) {
    echo "<h2>❌ Lỗi xảy ra:</h2>\n";
    echo "<p style='color: red;'><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p style='color: red;'><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>\n";
    echo "<p style='color: red;'><strong>Line:</strong> " . $e->getLine() . "</p>\n";

    if ($logger) {
        $logger->error('Error in add_product_images example', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

echo "<hr>\n";
echo "<h2>📚 Tài liệu tham khảo:</h2>\n";
echo "<ul>\n";
echo "<li><strong>API Endpoint:</strong> /api/product/externalimage</li>\n";
echo "<li><strong>Giới hạn:</strong> Tối đa 10 sản phẩm mỗi request, mỗi sản phẩm tối đa 20 ảnh</li>\n";
echo "<li><strong>Mode:</strong> 'update' (mặc định) hoặc 'deleteall'</li>\n";
echo "<li><strong>Lưu ý:</strong> Nhanh.vn sẽ không tải ảnh về mà dùng trực tiếp URL từ CDN</li>\n";
echo "</ul>\n";

// Giải phóng memory
unset($client, $productModule, $config, $logger);
