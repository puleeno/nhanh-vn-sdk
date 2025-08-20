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

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🖼️ Thêm ảnh sản phẩm từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🖼️ Thêm ảnh sản phẩm từ Nhanh.vn API sử dụng SDK</h1>
        <hr>

        <!-- Navigation Bar -->
        <div class="navigation-bar">
            <nav>
                <a href="index.php" class="nav-link">🏠 Trang chủ</a>
                <a href="get_products.php" class="nav-link">📦 Sản phẩm</a>
                <a href="get_categories.php" class="nav-link">📂 Danh mục</a>
                <a href="add_product.php" class="nav-link">➕ Thêm sản phẩm</a>
                <a href="add_product_images.php" class="nav-link active">🖼️ Thêm ảnh sản phẩm</a>
                <a href="search_customers.php" class="nav-link">👥 Khách hàng</a>
            </nav>
        </div>

        <div class="section">
            <h2>📋 Thông tin Debug</h2>
            <div class="debug-info">
                <p><strong>Script:</strong> <?php echo htmlspecialchars(__FILE__); ?></p>
                <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            </div>
        </div>

<?php

try {
    // Kiểm tra xem client có sẵn sàng không
    if (!isClientReady()) {
        echo '<div class="status error">';
        echo '<h3>❌ Chưa có access token</h3>';
        echo '<p>Hãy chạy OAuth flow trước!</p>';
        echo '<p><a href="index.php" class="btn btn-primary">🔐 Chạy OAuth Flow</a></p>';
        echo '</div>';
        echo '</div></body></html>';
        exit(1);
    }

    // Hiển thị thông tin client
    $clientInfo = getClientInfo();
    echo '<div class="status success">';
    echo '<h3>✅ Đã có access token</h3>';
    echo '<p><strong>Token:</strong> ' . htmlspecialchars($clientInfo['accessTokenPreview']) . '</p>';
    echo '</div>';

    // Khởi tạo SDK client
    echo '<div class="section">';
    echo '<h3>🚀 Khởi tạo SDK Client</h3>';

    try {
        // Sử dụng boot file để khởi tạo client
        $client = bootNhanhVnClientSilent();

        echo '<div class="status success">';
        echo '<h4>✅ SDK client đã sẵn sàng!</h4>';
        echo '<p><strong>Logger:</strong> NullLogger (không log)</p>';
        echo '</div>';

    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khởi tạo SDK</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
        echo '</div></body></html>';
        exit(1);
    }
    echo '</div>';

    // Lấy product module
    echo '<div class="section">';
    echo '<h3>📦 Khởi tạo Product Module</h3>';

    try {
        $productModule = $client->products();

        // DEBUG: Kiểm tra Product module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Product Module:</h4>';
        echo '<p><strong>Product Module Class:</strong> ' . get_class($productModule) . '</p>';
        echo '<p><strong>Product Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($productModule))) . '</pre>';
        echo '</div>';

        echo '<div class="status success">';
        echo '<h4>✅ Product module đã sẵn sàng!</h4>';
        echo '<p><strong>Module:</strong> ' . get_class($productModule) . '</p>';
        echo '</div>';

    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khởi tạo Product Module</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
        echo '</div></body></html>';
        exit(1);
    }
    echo '</div>';

    // Bắt đầu các example
    echo '<div class="section">';
    echo '<h3>🖼️ Product Image Examples</h3>';

    // Example 1: Thêm ảnh cho một sản phẩm
    echo '<div class="example info">';
    echo '<h4>Example 1: Thêm ảnh cho một sản phẩm</h4>';

    $singleProductData = [
        'productId' => 312311,
        'externalImages' => [
            'https://external.cdn.com/product/image1.jpg',
            'https://external.cdn.com/product/image2.jpg',
            'https://external.cdn.com/product/image3.jpg'
        ],
        'mode' => 'update' // Có thể là 'update' hoặc 'deleteall'
    ];

    echo '<h5>Dữ liệu sản phẩm:</h5>';
    echo '<pre>' . json_encode($singleProductData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

    // Validate dữ liệu trước khi gửi
    if ($productModule->validateExternalImageRequest($singleProductData)) {
        echo '<div class="success">✅ Dữ liệu hợp lệ</div>';

        // Gọi API thêm ảnh
        $response = $productModule->addExternalImage($singleProductData);

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h6>🔍 Debug API Response:</h6>';
        echo '<p><strong>Response Type:</strong> ' . gettype($response) . '</p>';
        echo '<p><strong>Response Class:</strong> ' . (is_object($response) ? get_class($response) : 'N/A') . '</p>';
        echo '<p><strong>Response Null:</strong> ' . (is_null($response) ? 'Yes' : 'No') . '</p>';

        if (is_object($response)) {
            echo '<p><strong>Response Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($response))) . '</pre>';
        }

        echo '<p><strong>Raw Response:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($response, true)) . '</pre>';
        echo '</div>';

        echo '<h5>Kết quả:</h5>';
        echo '<p><strong>Thành công:</strong> ' . ($response->isSuccess() ? 'Có' : 'Không') . '</p>';
        echo '<p><strong>Mã kết quả:</strong> ' . $response->getCode() . '</p>';
        echo '<p><strong>Số sản phẩm đã xử lý:</strong> ' . $response->getTotalProcessedProducts() . '</p>';

        if ($response->isSuccess()) {
            echo '<p><strong>ID sản phẩm đã xử lý:</strong> ' . implode(', ', $response->getAllProcessedProductIds()) . '</p>';
        } else {
            echo '<p><strong>Lỗi:</strong> ' . $response->getAllMessagesAsString() . '</p>';
        }

        echo '<h5>Thông tin tóm tắt:</h5>';
        echo '<pre>' . json_encode($response->getSummary(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

    } else {
        echo '<div class="error">❌ Dữ liệu không hợp lệ</div>';
    }
    echo '</div>';

    // Example 2: Thêm ảnh cho nhiều sản phẩm cùng lúc (batch)
    echo '<div class="example info">';
    echo '<h4>Example 2: Thêm ảnh cho nhiều sản phẩm cùng lúc (Batch)</h4>';

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

    echo '<h5>Dữ liệu batch:</h5>';
    echo '<pre>' . json_encode($batchProductsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

    // Validate dữ liệu batch
    $validationErrors = $productModule->validateExternalImageRequests($batchProductsData);

    if (empty($validationErrors)) {
        echo '<div class="success">✅ Tất cả dữ liệu đều hợp lệ</div>';

        // Gọi API thêm ảnh batch
        $batchResponse = $productModule->addExternalImages($batchProductsData);

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h6>🔍 Debug Batch API Response:</h6>';
        echo '<p><strong>Response Type:</strong> ' . gettype($batchResponse) . '</p>';
        echo '<p><strong>Response Class:</strong> ' . (is_object($batchResponse) ? get_class($batchResponse) : 'N/A') . '</p>';
        echo '<p><strong>Response Null:</strong> ' . (is_null($batchResponse) ? 'Yes' : 'No') . '</p>';

        if (is_object($batchResponse)) {
            echo '<p><strong>Response Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($batchResponse))) . '</pre>';
        }

        echo '<p><strong>Raw Response:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($batchResponse, true)) . '</pre>';
        echo '</div>';

        echo '<h5>Kết quả batch:</h5>';
        echo '<p><strong>Thành công:</strong> ' . ($batchResponse->isSuccess() ? 'Có' : 'Không') . '</p>';
        echo '<p><strong>Mã kết quả:</strong> ' . $batchResponse->getCode() . '</p>';
        echo '<p><strong>Số sản phẩm đã xử lý:</strong> ' . $batchResponse->getTotalProcessedProducts() . '</p>';

        if ($batchResponse->isSuccess()) {
            echo '<p><strong>ID sản phẩm đã xử lý:</strong> ' . implode(', ', $batchResponse->getAllProcessedProductIds()) . '</p>';
        } else {
            echo '<p><strong>Lỗi:</strong> ' . $batchResponse->getAllMessagesAsString() . '</p>';
        }

        echo '<h5>Thông tin tóm tắt batch:</h5>';
        echo '<pre>' . json_encode($batchResponse->getSummary(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

    } else {
        echo '<div class="error">❌ Có lỗi validation:</div>';
        echo '<ul>';
        foreach ($validationErrors as $error) {
            echo '<li style="color: red;">' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    }
    echo '</div>';

    // Example 3: Demo các trường hợp lỗi validation
    echo '<div class="example info">';
    echo '<h4>Example 3: Demo các trường hợp lỗi validation</h4>';

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
        echo '<h5>' . htmlspecialchars($example['name']) . ':</h5>';
        echo '<pre>' . json_encode($example['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

        $isValid = $productModule->validateExternalImageRequest($example['data']);
        echo '<p style="color: ' . ($isValid ? 'green' : 'red') . ';">' .
             ($isValid ? '✅ Hợp lệ' : '❌ Không hợp lệ') . '</p>';
        echo '<br>';
    }
    echo '</div>';

    // Example 4: Demo giới hạn batch size
    echo '<div class="example info">';
    echo '<h4>Example 4: Demo giới hạn batch size (tối đa 10 sản phẩm)</h4>';

    $largeBatchData = array_fill(0, 12, [
        'productId' => rand(100000, 999999),
        'externalImages' => ['https://example.com/image.jpg']
    ]);

    echo '<p><strong>Số sản phẩm trong batch:</strong> ' . count($largeBatchData) . '</p>';

    try {
        $largeBatchResponse = $productModule->addExternalImages($largeBatchData);
        echo '<div class="success">✅ Batch xử lý thành công</div>';
    } catch (\InvalidArgumentException $e) {
        echo '<div class="error">❌ Lỗi: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';

    echo '<div class="example info">';
    echo '<h4>Summary</h4>';
    echo '<p>This example demonstrates the Product Image module functionality including:</p>';
    echo '<ul>';
    echo '<li>Adding external images to single products</li>';
    echo '<li>Batch processing multiple products</li>';
    echo '<li>Request validation and error handling</li>';
    echo '<li>Batch size limitations</li>';
    echo '</ul>';
    echo '<p>The module provides a clean API for managing product images from external CDNs.</p>';
    echo '</div>';

    echo '</div>'; // End of examples section

} catch (Exception $e) {
    echo '<div class="status error">';
    echo '<h3>❌ Lỗi chung</h3>';
    echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Stack trace:</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}

echo '</div>'; // End of container
echo '</body>';
echo '</html>';
?>
