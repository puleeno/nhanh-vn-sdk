<?php
/**
 * Example: Lấy sản phẩm từ Nhanh.vn API sử dụng SDK với Monolog Logger
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛍️ Lấy sản phẩm với Monolog Logger</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🛍️ Lấy sản phẩm từ Nhanh.vn API với Monolog Logger</h1>
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

    // Khởi tạo SDK client với Monolog Logger
    echo '<div class="section">';
    echo '<h3>🚀 Khởi tạo SDK Client với Monolog Logger</h3>';

    try {
        // Sử dụng boot file để khởi tạo client với logger
        $client = bootNhanhVnClientWithLogger('DEBUG');
        
        echo '<div class="status success">';
        echo '<h4>✅ SDK client đã sẵn sàng với Monolog Logger!</h4>';
        echo '<p><strong>Logger:</strong> Monolog với stdout và file rotation</p>';
        echo '<p><strong>Log Level:</strong> DEBUG</p>';
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
    echo '<h3>🔄 Lấy danh sách sản phẩm qua SDK (với Logger)</h3>';

    try {
        // Sử dụng Product module của SDK
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ProductModule::search()...</h4>';
        echo '</div>';

        $searchCriteria = [
            'page' => 1,
            'limit' => 5, // Chỉ lấy 5 sản phẩm để demo
            'status' => 'Active'
        ];

        $products = $client->products()->search($searchCriteria);

        if (empty($products)) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có sản phẩm nào</h4>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>📦 Tìm thấy ' . count($products) . ' sản phẩm</h4>';
            echo '</div>';

            echo '<div class="products-list">';
            foreach ($products as $index => $product) {
                $num = $index + 1;
                echo '<div class="product-item">';
                echo '<h5>' . $num . '. ' . htmlspecialchars($product->getName() ?? 'N/A') . '</h5>';
                echo '<ul>';
                echo '<li><strong>ID:</strong> ' . htmlspecialchars($product->getId() ?? 'N/A') . '</li>';
                echo '<li><strong>Mã:</strong> ' . htmlspecialchars($product->getCode() ?? 'N/A') . '</li>';
                echo '<li><strong>Giá:</strong> ' . number_format($product->getPrice() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Tồn kho:</strong> ' . $product->getAvailableQuantity() . ' / ' . $product->getTotalQuantity() . '</li>';
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
            <p><a href="get_products.php" class="btn btn-secondary">📦 Lấy sản phẩm (không có logger)</a></p>
            <p><a href="callback.php" class="btn btn-secondary">🔄 Test OAuth Callback</a></p>
        </div>
    </div>
</body>
</html>
