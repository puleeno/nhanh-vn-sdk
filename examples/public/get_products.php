<?php
/**
 * Example: Lấy 10 sản phẩm từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛍️ Lấy sản phẩm từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🛍️ Lấy 10 sản phẩm từ Nhanh.vn API sử dụng SDK</h1>
        <hr>

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

    // Lấy danh sách sản phẩm sử dụng SDK
    echo '<div class="section">';
    echo '<h3>🔄 Lấy danh sách sản phẩm qua SDK</h3>';

    try {
        // DEBUG: Kiểm tra Product module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Product Module:</h4>';

        $productModule = $client->products();
        echo '<p><strong>Product Module Class:</strong> ' . get_class($productModule) . '</p>';
        echo '<p><strong>Product Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($productModule))) . '</pre>';
        echo '</div>';

        // DEBUG: Kiểm tra search method
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Search Method:</h4>';

        $searchCriteria = [];
        echo '<p><strong>Search Criteria:</strong></p>';
        echo '<pre>' . htmlspecialchars(json_encode($searchCriteria, JSON_PRETTY_PRINT)) . '</pre>';
        echo '</div>';

        // Sử dụng Product module của SDK
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ProductModule::search()...</h4>';
        echo '</div>';

        $products = $client->products()->search($searchCriteria);

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Search Result:</h4>';
        echo '<p><strong>Result Type:</strong> ' . gettype($products) . '</p>';
        echo '<p><strong>Result Class:</strong> ' . (is_object($products) ? get_class($products) : 'N/A') . '</p>';
        echo '<p><strong>Result Count:</strong> ' . (is_countable($products) ? count($products) : 'N/A') . '</p>';
        echo '<p><strong>Result Empty:</strong> ' . (empty($products) ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Result Null:</strong> ' . (is_null($products) ? 'Yes' : 'No') . '</p>';

        if (is_object($products)) {
            echo '<p><strong>Result Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($products))) . '</pre>';
        }

        echo '<p><strong>Raw Result:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($products, true)) . '</pre>';
        echo '</div>';

        if (empty($products)) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có sản phẩm nào</h4>';
            echo '<p><strong>Lý do có thể:</strong></p>';
            echo '<ul>';
            echo '<li>ProductModule::search() chưa implement API call thật</li>';
            echo '<li>API Nhanh.vn trả về empty data</li>';
            echo '<li>Collection object rỗng</li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>📦 Tìm thấy ' . count($products) . ' sản phẩm</h4>';
            echo '</div>';

            echo '<div class="products-list">';
            foreach ($products as $index => $product) {
                $num = $index + 1;
                echo '<div class="product-item">';
                echo '<h5>' . $num . '. ' . htmlspecialchars($product->getName()) . '</h5>';
                echo '<ul>';
                echo '<li><strong>ID:</strong> ' . htmlspecialchars($product->getId() ?? 'N/A') . '</li>';
                echo '<li><strong>Mã:</strong> ' . htmlspecialchars($product->getCode() ?? 'N/A') . '</li>';
                echo '<li><strong>Giá:</strong> ' . number_format($product->getPrice() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Tồn kho:</strong> ' . $product->getAvailableQuantity() . ' / ' . $product->getTotalQuantity() . '</li>';
                echo '<li><strong>Danh mục:</strong> ' . htmlspecialchars($product->getCategoryName() ?? 'N/A') . '</li>';
                echo '<li><strong>Trạng thái:</strong> ' . ($product->isActive() ? '✅ Hoạt động' : '❌ Không hoạt động') . '</li>';
                echo '</ul>';
                echo '</div>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi lấy sản phẩm</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }

    echo '</div>';

} catch (Exception $e) {
    echo '<div class="status error">';
    echo '<h3>❌ Lỗi chung</h3>';
    echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Stack trace:</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}

// HTML footer
?>
        <div class="section">
            <h3>🔗 Navigation</h3>
            <p><a href="index.php" class="btn btn-primary">🏠 Về trang chủ</a></p>
            <p><a href="callback.php" class="btn btn-secondary">🔄 Test OAuth Callback</a></p>
        </div>
    </div>
</body>
</html>
