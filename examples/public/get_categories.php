<?php
/**
 * Example: Lấy danh mục sản phẩm từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📂 Lấy danh mục sản phẩm từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📂 Lấy danh mục sản phẩm từ Nhanh.vn API</h1>
        <p class="subtitle">Sử dụng SDK để lấy danh sách danh mục sản phẩm</p>
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
        $client = bootNhanhVnClientWithLogger('DEBUG');

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

    // Lấy danh mục sản phẩm sử dụng SDK
    echo '<div class="section">';
    echo '<h3>🔄 Lấy danh mục sản phẩm qua SDK</h3>';

    try {
        // DEBUG: Kiểm tra Product module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Product Module:</h4>';

        $productModule = $client->products();
        echo '<p><strong>Product Module Class:</strong> ' . get_class($productModule) . '</p>';
        echo '<p><strong>Product Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($productModule))) . '</pre>';
        echo '</div>';

        // DEBUG: Kiểm tra getCategories method
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Get Categories Method:</h4>';
        echo '<p><strong>Method exists:</strong> ' . (method_exists($productModule, 'getCategories') ? 'Yes' : 'No') . '</p>';
        echo '</div>';

        // Sử dụng Product module của SDK để lấy categories
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ProductModule::getCategories()...</h4>';
        echo '</div>';

        $categories = $client->products()->getCategories();

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Categories Result:</h4>';
        echo '<p><strong>Result Type:</strong> ' . gettype($categories) . '</p>';
        echo '<p><strong>Result Count:</strong> ' . (is_countable($categories) ? count($categories) : 'N/A') . '</p>';
        echo '<p><strong>Result Empty:</strong> ' . (empty($categories) ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Result Null:</strong> ' . (is_null($categories) ? 'Yes' : 'No') . '</p>';

        if (is_array($categories) && !empty($categories)) {
            echo '<p><strong>First Category Class:</strong> ' . get_class($categories[0]) . '</p>';
            echo '<p><strong>First Category Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($categories[0]))) . '</pre>';
        }

        echo '<p><strong>Raw Result:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($categories, true)) . '</pre>';
        echo '</div>';

        if (empty($categories)) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có danh mục nào</h4>';
            echo '<p><strong>Lý do có thể:</strong></p>';
            echo '<ul>';
            echo '<li>API Nhanh.vn chưa có endpoint /product/categories</li>';
            echo '<li>API trả về empty data</li>';
            echo '<li>Chưa có sản phẩm nào được tạo</li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>📂 Tìm thấy ' . count($categories) . ' danh mục</h4>';
            echo '</div>';

            echo '<div class="categories-list">';
            foreach ($categories as $index => $category) {
                $num = $index + 1;
                echo '<div class="category-item">';
                echo '<h5>' . $num . '. ' . htmlspecialchars($category->getName() ?? 'N/A') . '</h5>';
                echo '<ul>';
                echo '<li><strong>ID:</strong> ' . htmlspecialchars($category->getId() ?? 'N/A') . '</li>';
                echo '<li><strong>Mã:</strong> ' . htmlspecialchars($category->getCode() ?? 'N/A') . '</li>';
                                 echo '<li><strong>Mô tả:</strong> ' . htmlspecialchars($category->getDescription() ?? 'N/A') . '</li>';
                 echo '<li><strong>Trạng thái:</strong> ' . ($category->isActive() ? '✅ Hoạt động' : '❌ Không hoạt động') . '</li>';
                 echo '<li><strong>Số sản phẩm:</strong> ' . ($category->getProductCount() ?? 'N/A') . '</li>';
                 echo '<li><strong>Thứ tự:</strong> ' . ($category->getOrder() ?? 'N/A') . '</li>';
                 echo '<li><strong>Level:</strong> ' . $category->getLevel() . '</li>';
                 echo '<li><strong>Parent ID:</strong> ' . ($category->getParentId() ?? 'Root') . '</li>';
                 echo '<li><strong>Slug:</strong> ' . htmlspecialchars($category->getSlug() ?? 'N/A') . '</li>';
                 echo '<li><strong>Meta Title:</strong> ' . htmlspecialchars($category->getMetaTitle() ?? 'N/A') . '</li>';
                 echo '<li><strong>Meta Description:</strong> ' . htmlspecialchars($category->getMetaDescription() ?? 'N/A') . '</li>';
                echo '</ul>';
                echo '</div>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi lấy danh mục</h4>';
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
            <p><a href="get_products.php" class="btn btn-secondary">📦 Lấy sản phẩm</a></p>
            <p><a href="get_products_with_logger.php" class="btn btn-success">📝 Lấy sản phẩm (với Logger)</a></p>
            <p><a href="callback.php" class="btn btn-info">🔄 Test OAuth Callback</a></p>
        </div>
    </div>
</body>
</html>
