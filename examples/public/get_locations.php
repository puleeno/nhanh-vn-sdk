<?php
/**
 * Example: Lấy danh sách địa điểm (thành phố, quận huyện, phường xã) từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🗺️ Lấy địa điểm từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🗺️ Lấy danh sách địa điểm từ Nhanh.vn API sử dụng SDK</h1>
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

    // Lấy danh sách thành phố sử dụng SDK
    echo '<div class="section">';
    echo '<h3>🏙️ Lấy danh sách thành phố qua SDK</h3>';

    try {
        // DEBUG: Kiểm tra Shipping module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Shipping Module:</h4>';

        $shippingModule = $client->shipping();
        echo '<p><strong>Shipping Module Class:</strong> ' . get_class($shippingModule) . '</p>';
        echo '<p><strong>Shipping Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($shippingModule))) . '</pre>';
        echo '</div>';

        // Sử dụng Shipping module của SDK để tìm kiếm thành phố
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ShippingModule::searchCities()...</h4>';
        echo '</div>';

        $cities = $client->shipping()->searchCities();

        // DEBUG: Kiểm tra kết quả trả về
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Search Cities Result:</h4>';
        echo '<p><strong>Result Type:</strong> ' . gettype($cities) . '</p>';
        echo '<p><strong>Result Class:</strong> ' . (is_object($cities) ? get_class($cities) : 'N/A') . '</p>';
        echo '<p><strong>Result Count:</strong> ' . (is_countable($cities) ? count($cities) : 'N/A') . '</p>';
        echo '<p><strong>Result Empty:</strong> ' . (empty($cities) ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Result Null:</strong> ' . (is_null($cities) ? 'Yes' : 'No') . '</p>';

        if (is_object($cities)) {
            echo '<p><strong>Result Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($cities))) . '</pre>';
        }

        echo '<p><strong>Raw Result:</strong></p>';
        echo '<pre>' . htmlspecialchars(print_r($cities, true)) . '</pre>';
        echo '</div>';

        if (empty($cities) || $cities->isEmpty()) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có thành phố nào</h4>';
            echo '<p><strong>Lý do có thể:</strong></p>';
            echo '<ul>';
            echo '<li>ShippingModule::searchCities() chưa implement API call thật</li>';
            echo '<li>API Nhanh.vn trả về empty data</li>';
            echo '<li>Response object rỗng</li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>🏙️ Tìm thấy ' . $cities->getCount() . ' thành phố</h4>';
            echo '</div>';

            echo '<div class="locations-list">';
            foreach ($cities->getData() as $index => $city) {
                $num = $index + 1;
                echo '<div class="location-item">';
                echo '<h5>' . $num . '. ' . htmlspecialchars($city->getName()) . '</h5>';
                echo '<ul>';
                echo '<li><strong>ID:</strong> ' . htmlspecialchars($city->getId()) . '</li>';
                echo '<li><strong>Loại:</strong> ' . htmlspecialchars($city->getType()) . '</li>';
                echo '<li><strong>Parent ID:</strong> ' . htmlspecialchars($city->getParentId() ?? 'N/A') . '</li>';
                echo '</ul>';
                echo '</div>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi lấy danh sách thành phố</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }

    echo '</div>';

    // Lấy danh sách quận huyện của Hà Nội (ID = 2)
    echo '<div class="section">';
    echo '<h3>🏘️ Lấy danh sách quận huyện của Hà Nội qua SDK</h3>';

    try {
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ShippingModule::searchDistricts(1)...</h4>';
        echo '</div>';

        $districts = $client->shipping()->searchDistricts(1);

        if (empty($districts) || $districts->isEmpty()) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không có quận huyện nào</h4>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>🏘️ Tìm thấy ' . $districts->getCount() . ' quận huyện của Hà Nội</h4>';
            echo '</div>';

            echo '<div class="locations-list">';
            foreach ($districts->getData() as $index => $district) {
                $num = $index + 1;
                echo '<div class="location-item">';
                echo '<h5>' . $num . '. ' . htmlspecialchars($district->getName()) . '</h5>';
                echo '<ul>';
                echo '<li><strong>ID:</strong> ' . htmlspecialchars($district->getId()) . '</li>';
                echo '<li><strong>Loại:</strong> ' . htmlspecialchars($district->getType()) . '</li>';
                echo '<li><strong>Parent ID:</strong> ' . htmlspecialchars($district->getParentId()) . '</li>';
                echo '</ul>';
                echo '</div>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi lấy danh sách quận huyện</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }

    echo '</div>';

    // Tìm kiếm địa điểm theo tên
    echo '<div class="section">';
    echo '<h3>🔍 Tìm kiếm địa điểm theo tên qua SDK</h3>';

    try {
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang gọi ShippingModule::searchByName("Hà")...</h4>';
        echo '</div>';

        $searchResults = $client->shipping()->searchByName("Hà", "CITY");

        if (empty($searchResults) || $searchResults->isEmpty()) {
            echo '<div class="status warning">';
            echo '<h4>📭 Không tìm thấy địa điểm nào</h4>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h4>🔍 Tìm thấy ' . $searchResults->getCount() . ' địa điểm chứa "Hà"</h4>';
            echo '</div>';

            echo '<div class="locations-list">';
            foreach ($searchResults->getData() as $index => $location) {
                $num = $index + 1;
                echo '<div class="location-item">';
                echo '<h5>' . $num . '. ' . htmlspecialchars($location->getName()) . '</h5>';
                echo '<ul>';
                echo '<li><strong>ID:</strong> ' . htmlspecialchars($location->getId()) . '</li>';
                echo '<li><strong>Loại:</strong> ' . htmlspecialchars($location->getType()) . '</li>';
                echo '<li><strong>Parent ID:</strong> ' . htmlspecialchars($location->getParentId() ?? 'N/A') . '</li>';
                echo '</ul>';
                echo '</div>';
            }
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi tìm kiếm địa điểm</h4>';
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
