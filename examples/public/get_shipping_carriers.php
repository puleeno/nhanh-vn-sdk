<?php
/**
 * Example: Lấy danh sách hãng vận chuyển từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚚 Danh sách hãng vận chuyển Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🚚 Danh sách hãng vận chuyển Nhanh.vn API sử dụng SDK</h1>
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

    // Lấy danh sách hãng vận chuyển sử dụng SDK
    echo '<div class="section">';
    echo '<h3>🔄 Lấy danh sách hãng vận chuyển qua SDK</h3>';

    try {
        // DEBUG: Kiểm tra Shipping module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Shipping Module:</h4>';

        $shippingModule = $client->shipping();
        echo '<p><strong>Shipping Module Class:</strong> ' . get_class($shippingModule) . '</p>';
        echo '<p><strong>Shipping Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($shippingModule))) . '</pre>';
        echo '</div>';

        // DEBUG: Kiểm tra getCarriers method
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug GetCarriers Method:</h4>';
        echo '<p><strong>Method exists:</strong> ' . (method_exists($shippingModule, 'getCarriers') ? 'Yes' : 'No') . '</p>';
        echo '</div>';

        // Sử dụng Shipping module của SDK
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ShippingModule::getCarriers()...</h4>';
        echo '</div>';

        $carriersResponse = $client->shipping()->getCarriers();

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug GetCarriers Result:</h4>';
        echo '<p><strong>Result Type:</strong> ' . gettype($carriersResponse) . '</p>';
        echo '<p><strong>Result Class:</strong> ' . (is_object($carriersResponse) ? get_class($carriersResponse) : 'N/A') . '</p>';
        echo '<p><strong>Result Count:</strong> ' . (is_countable($carriersResponse) ? count($carriersResponse) : 'N/A') . '</p>';
        echo '<p><strong>Result Empty:</strong> ' . (empty($carriersResponse) ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Result Null:</strong> ' . (is_null($carriersResponse) ? 'Yes' : 'No') . '</p>';

        if (is_object($carriersResponse)) {
            echo '<p><strong>Result Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($carriersResponse))) . '</pre>';
        }

        echo '<p><strong>Raw Result:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($carriersResponse, true)) . '</pre>';
        echo '</div>';

        if (empty($carriersResponse) || !$carriersResponse->hasCarriers()) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có hãng vận chuyển nào</h4>';
            echo '<p><strong>Lý do có thể:</strong></p>';
            echo '<ul>';
            echo '<li>ShippingModule::getCarriers() chưa implement API call thật</li>';
            echo '<li>API Nhanh.vn trả về empty data</li>';
            echo '<li>Response object rỗng</li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>🚚 Tìm thấy ' . $carriersResponse->getTotalCarriers() . ' hãng vận chuyển</h4>';
            echo '</div>';

            echo '<div class="carriers-list">';
            foreach ($carriersResponse->getCarriers() as $index => $carrier) {
                $num = $index + 1;
                echo '<div class="carrier-item">';
                echo '<div class="carrier-header">';
                echo '<h5>' . $num . '. ' . htmlspecialchars($carrier['name'] ?? 'N/A') . '</h5>';
                echo '<div class="carrier-id">ID: ' . htmlspecialchars($carrier['id'] ?? 'N/A') . '</div>';
                if (!empty($carrier['logo'])) {
                    echo '<div class="carrier-logo">Logo: ' . htmlspecialchars($carrier['logo']) . '</div>';
                }
                echo '</div>';

                if (!empty($carrier['services']) && is_array($carrier['services'])) {
                    echo '<div class="carrier-services">';
                    echo '<h6>Dịch vụ:</h6>';
                    echo '<ul>';
                    foreach ($carrier['services'] as $service) {
                        if (is_array($service)) {
                            $serviceName = $service['name'] ?? 'N/A';
                            $serviceDesc = $service['description'] ?? '';
                            echo '<li><strong>' . htmlspecialchars($serviceName) . '</strong>';
                            if (!empty($serviceDesc)) {
                                echo ' - ' . htmlspecialchars($serviceDesc);
                            }
                            echo '</li>';
                        } else {
                            echo '<li>' . htmlspecialchars($service) . '</li>';
                        }
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    echo '<div class="no-services">Không có dịch vụ nào</div>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi lấy hãng vận chuyển</h4>';
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
            <p><a href="get_products.php" class="btn btn-secondary">📦 Xem danh sách sản phẩm</a></p>
            <p><a href="get_customers.php" class="btn btn-secondary">👥 Xem danh sách khách hàng</a></p>
            <p><a href="get_orders.php" class="btn btn-secondary">📋 Xem danh sách đơn hàng</a></p>
        </div>
    </div>
</body>
</html>
