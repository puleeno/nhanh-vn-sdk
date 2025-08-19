<?php
/**
 * Example: Tính phí vận chuyển từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💰 Tính phí vận chuyển Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>💰 Tính phí vận chuyển Nhanh.vn API sử dụng SDK</h1>
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

    // Tính phí vận chuyển sử dụng SDK
    echo '<div class="section">';
    echo '<h3>🔄 Tính phí vận chuyển qua SDK</h3>';

    try {
        // DEBUG: Kiểm tra Shipping module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Shipping Module:</h4>';

        $shippingModule = $client->shipping();
        echo '<p><strong>Shipping Module Class:</strong> ' . get_class($shippingModule) . '</p>';
        echo '<p><strong>Shipping Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($shippingModule))) . '</pre>';
        echo '</div>';

        // Dữ liệu mẫu để tính phí
        $sampleData = [
            'fromCityName' => 'Hà Nội',
            'fromDistrictName' => 'Quận Hoàn Kiếm',
            'toCityName' => 'Hồ Chí Minh',
            'toDistrictName' => 'Quận 1',
            'codMoney' => 500000, // 500k VND
            'shippingWeight' => 1000, // 1kg = 1000g
            'carrierIds' => [2, 5] // Viettel Post, Giao hàng nhanh
        ];

        echo '<div class="debug-info">';
        echo '<h4>📝 Dữ liệu mẫu tính phí:</h4>';
        echo '<ul>';
        echo '<li><strong>Từ:</strong> ' . htmlspecialchars($sampleData['fromCityName']) . ', ' . htmlspecialchars($sampleData['fromDistrictName']) . '</li>';
        echo '<li><strong>Đến:</strong> ' . htmlspecialchars($sampleData['toCityName']) . ', ' . htmlspecialchars($sampleData['toDistrictName']) . '</li>';
        echo '<li><strong>COD:</strong> ' . number_format($sampleData['codMoney']) . ' VND</li>';
        echo '<li><strong>Trọng lượng:</strong> ' . $sampleData['shippingWeight'] . 'g</li>';
        echo '<li><strong>Hãng vận chuyển:</strong> ' . implode(', ', $sampleData['carrierIds']) . '</li>';
        echo '</ul>';
        echo '</div>';

        // Sử dụng Shipping module của SDK
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ShippingModule::calculateFeeFromArray()...</h4>';
        echo '</div>';

        $feeResponse = $client->shipping()->calculateFeeFromArray($sampleData);

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Calculate Fee Result:</h4>';
        echo '<p><strong>Result Type:</strong> ' . gettype($feeResponse) . '</p>';
        echo '<p><strong>Result Class:</strong> ' . (is_object($feeResponse) ? get_class($feeResponse) : 'N/A') . '</p>';
        echo '<p><strong>Result Count:</strong> ' . (is_countable($feeResponse) ? count($feeResponse) : 'N/A') . '</p>';
        echo '<p><strong>Result Empty:</strong> ' . (empty($feeResponse) ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Result Null:</strong> ' . (is_null($feeResponse) ? 'Yes' : 'No') . '</p>';

        if (is_object($feeResponse)) {
            echo '<p><strong>Result Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($feeResponse))) . '</pre>';
        }

        echo '<p><strong>Raw Result:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($feeResponse, true)) . '</pre>';
        echo '</div>';

        if (empty($feeResponse) || !$feeResponse->hasServices()) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có dịch vụ vận chuyển nào</h4>';
            echo '<p><strong>Lý do có thể:</strong></p>';
            echo '<ul>';
            echo '<li>ShippingModule::calculateFee() chưa implement API call thật</li>';
            echo '<li>API Nhanh.vn trả về empty data</li>';
            echo '<li>Response object rỗng</li>';
            echo '<li>Địa điểm không hỗ trợ</li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>💰 Tìm thấy ' . $feeResponse->getTotalServices() . ' dịch vụ vận chuyển</h4>';
            echo '</div>';

            // Hiển thị bảng giá
            echo '<div class="shipping-fees-list">';
            echo '<h5>📋 Bảng giá các dịch vụ vận chuyển:</h5>';

            $services = $feeResponse->getServices();
            foreach ($services as $index => $service) {
                $num = $index + 1;
                echo '<div class="fee-item">';
                echo '<div class="fee-header">';
                echo '<h6>' . $num . '. ' . htmlspecialchars($service['carrierName'] ?? 'N/A') . '</h6>';
                echo '<div class="fee-service">' . htmlspecialchars($service['serviceName'] ?? 'N/A') . '</div>';
                if (!empty($service['serviceDescription'])) {
                    echo '<div class="fee-description">' . htmlspecialchars($service['serviceDescription']) . '</div>';
                }
                echo '</div>';

                echo '<div class="fee-details">';
                echo '<div class="fee-row">';
                echo '<span class="fee-label">Phí vận chuyển:</span>';
                echo '<span class="fee-value">' . number_format($service['shipFee'] ?? 0) . ' VND</span>';
                echo '</div>';

                if (isset($service['codFee']) && $service['codFee'] > 0) {
                    echo '<div class="fee-row">';
                    echo '<span class="fee-label">Phí thu tiền hộ:</span>';
                    echo '<span class="fee-value">' . number_format($service['codFee']) . ' VND</span>';
                    echo '</div>';
                }

                if (isset($service['declaredFee']) && $service['declaredFee'] > 0) {
                    echo '<div class="fee-row">';
                    echo '<span class="fee-label">Phí bảo hiểm:</span>';
                    echo '<span class="fee-value">' . number_format($service['declaredFee']) . ' VND</span>';
                    echo '</div>';
                }

                if (isset($service['estimatedDeliveryTime'])) {
                    echo '<div class="fee-row">';
                    echo '<span class="fee-label">Thời gian giao:</span>';
                    echo '<span class="fee-value">' . $service['estimatedDeliveryTime'] . ' ngày</span>';
                    echo '</div>';
                }

                // Tính tổng phí
                $totalFee = ($service['shipFee'] ?? 0) + ($service['codFee'] ?? 0);
                if (isset($service['isRequiredInsurance']) && $service['isRequiredInsurance']) {
                    $totalFee += ($service['declaredFee'] ?? 0);
                    echo '<div class="fee-note">⚠️ Bắt buộc mua bảo hiểm</div>';
                }

                if (isset($service['isBulkyGoods']) && $service['isBulkyGoods']) {
                    echo '<div class="fee-note">📦 Hàng cồng kềnh</div>';
                }

                echo '<div class="fee-row total">';
                echo '<span class="fee-label"><strong>Tổng phí:</strong></span>';
                echo '<span class="fee-value"><strong>' . number_format($totalFee) . ' VND</strong></span>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';

            // Thống kê
            echo '<div class="fee-summary">';
            echo '<h5>📊 Thống kê phí vận chuyển:</h5>';
            $cheapest = $feeResponse->getCheapestService();
            $fastest = $feeResponse->getFastestService();

            if ($cheapest) {
                $cheapestTotal = ($cheapest['shipFee'] ?? 0) + ($cheapest['codFee'] ?? 0);
                echo '<p><strong>🏷️ Rẻ nhất:</strong> ' . htmlspecialchars($cheapest['carrierName'] ?? 'N/A') . ' - ' . htmlspecialchars($cheapest['serviceName'] ?? 'N/A') . ' (' . number_format($cheapestTotal) . ' VND)</p>';
            }

            if ($fastest) {
                echo '<p><strong>⚡ Nhanh nhất:</strong> ' . htmlspecialchars($fastest['carrierName'] ?? 'N/A') . ' - ' . htmlspecialchars($fastest['serviceName'] ?? 'N/A') . ' (' . ($fastest['estimatedDeliveryTime'] ?? 'N/A') . ' ngày)</p>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi tính phí vận chuyển</h4>';
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
            <p><a href="get_shipping_carriers.php" class="btn btn-secondary">🚚 Xem hãng vận chuyển</a></p>
        </div>
    </div>
</body>
</html>
