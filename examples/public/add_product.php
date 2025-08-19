<?php
/**
 * Example: Thêm sản phẩm mới
 *
 * File này demo cách sử dụng SDK để thêm sản phẩm mới vào Nhanh.vn
 *
 * @package Puleeno\NhanhVn\Examples
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @since 2.0.0
 */

require_once __DIR__ . '/../boot/client.php';

use Puleeno\NhanhVn\Entities\Product\ProductAddRequest;

try {
    // Khởi tạo client
    $client = bootNhanhVnClientWithLogger('DEBUG');

    echo "<h1>Nhanh.vn SDK - Thêm sản phẩm mới</h1>\n";

    // Example 1: Thêm một sản phẩm đơn lẻ
    echo "<h2>1. Thêm sản phẩm đơn lẻ</h2>\n";

    $productData = [
        'id' => 'PROD_' . time(), // ID hệ thống riêng
        'name' => 'iPhone 15 Pro Max 256GB',
        'price' => 45000000, // 45 triệu VNĐ
        'code' => 'IPHONE15PM-256',
        'barcode' => '1234567890123',
        'description' => 'Điện thoại iPhone mới nhất với chip A17 Pro',
        'categoryId' => 1, // ID danh mục điện thoại
        'brandId' => 2, // ID thương hiệu Apple
        'importPrice' => 40000000, // Giá nhập
        'wholesalePrice' => 42000000, // Giá bán buôn
        'shippingWeight' => 221, // Cân nặng vận chuyển (gram)
        'vat' => 10, // Thuế VAT 10%
        'status' => 'Active',
        'externalImages' => [
            'https://example.com/iphone15-1.jpg',
            'https://example.com/iphone15-2.jpg'
        ]
    ];

    echo "<h3>Dữ liệu sản phẩm:</h3>\n";
    echo "<pre>" . json_encode($productData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

    // Validate data trước khi gửi
    if ($client->products()->validateProductAddRequest($productData)) {
        echo "<p style='color: green;'>✓ Dữ liệu sản phẩm hợp lệ</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Dữ liệu sản phẩm không hợp lệ</p>\n";
        exit;
    }

    // Thêm sản phẩm
    $response = $client->products()->add($productData);

    echo "<h3>Kết quả:</h3>\n";
    echo "<p><strong>ID hệ thống:</strong> " . $productData['id'] . "</p>\n";
    echo "<p><strong>ID Nhanh.vn:</strong> " . $response->getNhanhId($productData['id']) . "</p>\n";
    echo "<p><strong>Barcode:</strong> " . $response->getBarcode($productData['id']) . "</p>\n";

    // Example 2: Thêm nhiều sản phẩm cùng lúc (batch)
    echo "<h2>2. Thêm nhiều sản phẩm cùng lúc (Batch)</h2>\n";

    $batchProducts = [
        [
            'id' => 'PROD_' . (time() + 1),
            'name' => 'MacBook Pro 14" M3 Pro',
            'price' => 55000000,
            'code' => 'MBP14-M3PRO',
            'description' => 'Laptop mạnh mẽ với chip M3 Pro',
            'categoryId' => 2, // Danh mục laptop
            'brandId' => 2, // Apple
            'shippingWeight' => 1600,
            'vat' => 10
        ],
        [
            'id' => 'PROD_' . (time() + 2),
            'name' => 'iPad Air 5 64GB',
            'price' => 18000000,
            'code' => 'IPADAIR5-64',
            'description' => 'iPad Air thế hệ 5 với chip M1',
            'categoryId' => 3, // Danh mục tablet
            'brandId' => 2, // Apple
            'shippingWeight' => 461,
            'vat' => 10
        ],
        [
            'id' => 'PROD_' . (time() + 3),
            'name' => 'AirPods Pro 2',
            'price' => 7500000,
            'code' => 'AIRPODSPRO2',
            'description' => 'Tai nghe không dây cao cấp',
            'categoryId' => 4, // Danh mục phụ kiện
            'brandId' => 2, // Apple
            'shippingWeight' => 30,
            'vat' => 10
        ]
    ];

    echo "<h3>Danh sách sản phẩm batch:</h3>\n";
    echo "<pre>" . json_encode($batchProducts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";

    // Validate batch data
    $validationErrors = $client->products()->validateProductAddRequests($batchProducts);
    if (empty($validationErrors)) {
        echo "<p style='color: green;'>✓ Tất cả dữ liệu sản phẩm đều hợp lệ</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Có lỗi validation:</p>\n";
        echo "<pre>" . json_encode($validationErrors, JSON_PRETTY_PRINT) . "</pre>\n";
        exit;
    }

    // Thêm batch sản phẩm
    $batchResponse = $client->products()->addBatch($batchProducts);

    echo "<h3>Kết quả batch:</h3>\n";
    echo "<p><strong>Tổng số sản phẩm:</strong> " . $batchResponse->getTotalProducts() . "</p>\n";
    echo "<p><strong>Số sản phẩm thành công:</strong> " . $batchResponse->getSuccessCount() . "</p>\n";
    echo "<p><strong>Số sản phẩm thất bại:</strong> " . $batchResponse->getFailedCount() . "</p>\n";
    echo "<p><strong>Tỷ lệ thành công:</strong> " . $batchResponse->getSuccessRate() . "%</p>\n";

    if ($batchResponse->isAllSuccess()) {
        echo "<p style='color: green;'>✓ Tất cả sản phẩm đều được thêm thành công!</p>\n";
    } else {
        echo "<p style='color: orange;'>⚠ Có một số sản phẩm thất bại</p>\n";
    }

    // Hiển thị chi tiết từng sản phẩm
    echo "<h3>Chi tiết từng sản phẩm:</h3>\n";
    foreach ($batchProducts as $product) {
        $systemId = $product['id'];
        $nhanhId = $batchResponse->getNhanhId($systemId);
        $barcode = $batchResponse->getBarcode($systemId);

        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>\n";
        echo "<p><strong>Tên:</strong> " . $product['name'] . "</p>\n";
        echo "<p><strong>ID hệ thống:</strong> " . $systemId . "</p>\n";
        echo "<p><strong>ID Nhanh.vn:</strong> " . ($nhanhId ?: 'N/A') . "</p>\n";
        echo "<p><strong>Barcode:</strong> " . ($barcode ?: 'N/A') . "</p>\n";
        echo "</div>\n";
    }

    // Example 3: Sử dụng ProductAddRequest entity
    echo "<h2>3. Sử dụng ProductAddRequest Entity</h2>\n";

    $requestEntity = new ProductAddRequest([
        'id' => 'PROD_ENTITY_' . time(),
        'name' => 'Apple Watch Series 9',
        'price' => 12000000,
        'code' => 'AWATCH9',
        'description' => 'Đồng hồ thông minh mới nhất',
        'categoryId' => 5, // Danh mục đồng hồ
        'brandId' => 2, // Apple
        'shippingWeight' => 50,
        'vat' => 10
    ]);

    echo "<h3>ProductAddRequest Entity:</h3>\n";
    echo "<p><strong>ID:</strong> " . $requestEntity->getId() . "</p>\n";
    echo "<p><strong>Tên:</strong> " . $requestEntity->getName() . "</p>\n";
    echo "<p><strong>Giá:</strong> " . number_format($requestEntity->getPrice()) . " VNĐ</p>\n";
    echo "<p><strong>Là sản phẩm mới:</strong> " . ($requestEntity->isNew() ? 'Có' : 'Không') . "</p>\n";
    echo "<p><strong>Hợp lệ:</strong> " . ($requestEntity->isValid() ? 'Có' : 'Không') . "</p>\n";

    if ($requestEntity->isValid()) {
        $entityResponse = $client->products()->add($requestEntity);
        echo "<p><strong>ID Nhanh.vn:</strong> " . $entityResponse->getNhanhId($requestEntity->getId()) . "</p>\n";
    }

    echo "<hr>\n";
    echo "<p><em>Example hoàn thành thành công!</em></p>\n";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Lỗi:</h2>\n";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>\n";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>\n";
    echo "<p><strong>Trace:</strong></p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>
