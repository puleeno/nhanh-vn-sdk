<?php
/**
 * Example: Lấy danh sách đơn hàng từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📦 Lấy đơn hàng từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📦 Lấy danh sách đơn hàng từ Nhanh.vn API sử dụng SDK</h1>
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

    // Lấy danh sách đơn hàng sử dụng SDK
    echo '<div class="section">';
    echo '<h3>🔄 Lấy danh sách đơn hàng qua SDK</h3>';

    try {
        // DEBUG: Kiểm tra Order module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Order Module:</h4>';

        $orderModule = $client->orders();
        echo '<p><strong>Order Module Class:</strong> ' . get_class($orderModule) . '</p>';
        echo '<p><strong>Order Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($orderModule))) . '</pre>';
        echo '</div>';

        // DEBUG: Kiểm tra search method
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Search Method:</h4>';

        $searchCriteria = [
            'page' => 1,
            'icpp' => 10,
            'fromDate' => date('Y-m-d', strtotime('-7 days')),
            'toDate' => date('Y-m-d')
        ];
        echo '<p><strong>Search Criteria:</strong></p>';
        echo '<pre>' . htmlspecialchars(json_encode($searchCriteria, JSON_PRETTY_PRINT)) . '</pre>';
        echo '</div>';

        // Sử dụng Order module của SDK
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi OrderModule::search()...</h4>';
        echo '</div>';

        $orders = $client->orders()->search($searchCriteria);

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Search Result:</h4>';
        echo '<p><strong>Result Type:</strong> ' . gettype($orders) . '</p>';
        echo '<p><strong>Result Class:</strong> ' . (is_object($orders) ? get_class($orders) : 'N/A') . '</p>';
        echo '<p><strong>Result Count:</strong> ' . (is_countable($orders) ? count($orders) : 'N/A') . '</p>';
        echo '<p><strong>Result Empty:</strong> ' . (empty($orders) ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Result Null:</strong> ' . (is_null($orders) ? 'Yes' : 'No') . '</p>';

        if (is_object($orders)) {
            echo '<p><strong>Result Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($orders))) . '</pre>';
        }

        echo '<p><strong>Raw Result:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($orders, true)) . '</pre>';
        echo '</div>';

        // Lấy orders collection từ response
        $ordersCollection = $orders->getOrders();
        $orderCount = $ordersCollection->count();

        if ($orderCount === 0) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có đơn hàng nào</h4>';
            echo '<p><strong>Lý do có thể:</strong></p>';
            echo '<ul>';
            echo '<li>OrderModule::search() chưa implement API call thật</li>';
            echo '<li>API Nhanh.vn trả về empty data</li>';
            echo '<li>Collection object rỗng</li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>📦 Tìm thấy ' . $orderCount . ' đơn hàng</h4>';
            echo '<p><strong>Tổng số bản ghi:</strong> ' . $orders->getTotalRecords() . '</p>';
            echo '<p><strong>Trang hiện tại:</strong> ' . $orders->getPage() . ' / ' . $orders->getTotalPages() . '</p>';
            echo '</div>';

            echo '<div class="orders-list">';
            foreach ($ordersCollection as $orderId => $order) {
                $num = $ordersCollection->search($order) + 1;
                echo '<div class="order-item">';
                echo '<h5>' . $num . '. Đơn hàng #' . htmlspecialchars($order->getId() ?? 'N/A') . '</h5>';
                echo '<ul>';
                echo '<li><strong>ID:</strong> ' . htmlspecialchars($order->getId() ?? 'N/A') . '</li>';
                echo '<li><strong>Khách hàng:</strong> ' . htmlspecialchars($order->getCustomerName() ?? 'N/A') . '</li>';
                echo '<li><strong>SĐT:</strong> ' . htmlspecialchars($order->getCustomerMobile() ?? 'N/A') . '</li>';
                echo '<li><strong>Tổng tiền:</strong> ' . number_format($order->getCalcTotalMoney() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Phí ship:</strong> ' . number_format($order->getShipFee() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Trạng thái:</strong> ' . htmlspecialchars($order->getStatusName() ?? 'N/A') . '</li>';
                echo '<li><strong>Loại:</strong> ' . htmlspecialchars($order->getType() ?? 'N/A') . '</li>';
                echo '<li><strong>Ngày tạo:</strong> ' . htmlspecialchars($order->getCreatedDateTime() ?? 'N/A') . '</li>';
                echo '<li><strong>Ngày giao:</strong> ' . htmlspecialchars($order->getDeliveryDate() ?? 'N/A') . '</li>';
                echo '<li><strong>Kênh bán:</strong> ' . htmlspecialchars($order->getSaleChannelName() ?? 'N/A') . '</li>';
                echo '</ul>';
                echo '</div>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi lấy đơn hàng</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }

    echo '</div>';

    // Demo các method khác của Order Module
    echo '<div class="section">';
    echo '<h3>🔍 Demo các method khác của Order Module</h3>';

    try {
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang test các method khác...</h4>';
        echo '</div>';

        // Test getAll method
        echo '<div class="method-test">';
        echo '<h4>📋 Test getAll() method:</h4>';
        try {
            $allOrders = $client->orders()->getAll();
            $allOrdersCollection = $allOrders->getOrders();
            echo '<p><strong>✅ getAll() thành công:</strong> ' . $allOrdersCollection->count() . ' đơn hàng</p>';
        } catch (Exception $e) {
            echo '<p><strong>❌ getAll() lỗi:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        echo '</div>';

        // Test getByType method
        echo '<div class="method-test">';
        echo '<h4>📋 Test getByType() method:</h4>';
        try {
            $shippingOrders = $client->orders()->getByType(1); // Giao hàng tận nhà
            $shippingOrdersCollection = $shippingOrders->getOrders();
            echo '<p><strong>✅ getByType(1) thành công:</strong> ' . $shippingOrdersCollection->count() . ' đơn giao hàng</p>';
        } catch (Exception $e) {
            echo '<p><strong>❌ getByType(1) lỗi:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        echo '</div>';

        // Test getByStatuses method
        echo '<div class="method-test">';
        echo '<h4>📋 Test getByStatuses() method:</h4>';
        try {
            $statusOrders = $client->orders()->getByStatuses(['pending', 'processing']);
            $statusOrdersCollection = $statusOrders->getOrders();
            echo '<p><strong>✅ getByStatuses() thành công:</strong> ' . $statusOrdersCollection->count() . ' đơn theo trạng thái</p>';
        } catch (Exception $e) {
            echo '<p><strong>❌ getByStatuses() lỗi:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        echo '</div>';

        // Test cache methods
        echo '<div class="method-test">';
        echo '<h4>📋 Test Cache Methods:</h4>';
        try {
            $cacheStatus = $client->orders()->getCacheStatus();
            $isCacheAvailable = $client->orders()->isCacheAvailable();
            echo '<p><strong>✅ Cache Status:</strong> ' . htmlspecialchars(json_encode($cacheStatus)) . '</p>';
            echo '<p><strong>✅ Is Cache Available:</strong> ' . ($isCacheAvailable ? 'Yes' : 'No') . '</p>';
        } catch (Exception $e) {
            echo '<p><strong>❌ Cache methods lỗi:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        echo '</div>';

    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi test các method khác</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
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
            <p><a href="get_products.php" class="btn btn-secondary">🛍️ Lấy sản phẩm</a></p>
        </div>
    </div>
</body>
</html>
