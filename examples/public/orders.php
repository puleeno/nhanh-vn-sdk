<?php
/**
 * Ví dụ sử dụng Order API
 * 
 * File này minh họa cách sử dụng Order Module để:
 * - Tìm kiếm đơn hàng
 * - Lọc theo các tiêu chí khác nhau
 * - Phân tích dữ liệu đơn hàng
 * - Quản lý cache
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Services\Logger\MonologAdapter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Khởi tạo logger
$logger = new Logger('order-example');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/order-example.log', Logger::DEBUG));

try {
    // Khởi tạo cấu hình
    $config = new ClientConfig([
        'appId' => 'your_app_id',
        'secretKey' => 'your_secret_key',
        'businessId' => 'your_business_id',
        'accessToken' => 'your_access_token',
        'environment' => 'production'
    ]);

    // Khởi tạo client
    $client = NhanhVnClient::getInstance($config);
    $client->setLogger(new MonologAdapter($logger));

    echo "<h1>📋 Order API Examples</h1>\n";
    echo "<p>Ví dụ sử dụng Order Module để quản lý đơn hàng từ Nhanh.vn</p>\n";

    // 1. Lấy tất cả đơn hàng (10 ngày gần nhất)
    echo "<h2>1. Lấy tất cả đơn hàng (10 ngày gần nhất)</h2>\n";
    try {
        $orders = $client->orders()->getAll();
        echo "<p>✅ Tổng số đơn hàng: " . $orders->getTotalRecords() . "</p>\n";
        echo "<p>📄 Tổng số trang: " . $orders->getTotalPages() . "</p>\n";
        echo "<p>📦 Số đơn hàng trang hiện tại: " . $orders->getCurrentPageOrderCount() . "</p>\n";
        
        if ($orders->hasNextPage()) {
            echo "<p>➡️ Có trang tiếp theo: " . $orders->getNextPage() . "</p>\n";
        }
        
        if ($orders->hasPreviousPage()) {
            echo "<p>⬅️ Có trang trước: " . $orders->getPreviousPage() . "</p>\n";
        }
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    // 2. Tìm kiếm đơn hàng theo ID
    echo "<h2>2. Tìm kiếm đơn hàng theo ID</h2>\n";
    try {
        $orders = $client->orders()->searchById(12345);
        if ($orders->getTotalRecords() > 0) {
            echo "<p>✅ Tìm thấy đơn hàng ID: 12345</p>\n";
            $order = $orders->getOrderById(12345);
            if ($order) {
                echo "<p>📋 Thông tin đơn hàng:</p>\n";
                echo "<ul>\n";
                echo "<li>ID: " . $order->getId() . "</li>\n";
                echo "<li>Khách hàng: " . $order->getCustomerName() . "</li>\n";
                echo "<li>Số điện thoại: " . $order->getCustomerMobile() . "</li>\n";
                echo "<li>Tổng tiền: " . number_format($order->getCalcTotalMoney()) . " VNĐ</li>\n";
                echo "<li>Trạng thái: " . $order->getStatusName() . "</li>\n";
                echo "<li>Loại: " . $order->getType() . "</li>\n";
                echo "<li>Kênh bán: " . $order->getSaleChannelName() . "</li>\n";
                echo "</ul>\n";
            }
        } else {
            echo "<p>ℹ️ Không tìm thấy đơn hàng ID: 12345</p>\n";
        }
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    // 3. Tìm kiếm đơn hàng theo số điện thoại khách hàng
    echo "<h2>3. Tìm kiếm đơn hàng theo số điện thoại khách hàng</h2>\n";
    try {
        $orders = $client->orders()->searchByCustomerMobile('0987654321');
        echo "<p>✅ Tìm thấy " . $orders->getTotalRecords() . " đơn hàng cho số điện thoại: 0987654321</p>\n";
        
        if ($orders->getTotalRecords() > 0) {
            echo "<p>📋 Danh sách đơn hàng:</p>\n";
            foreach ($orders->getOrders() as $order) {
                echo "<ul>\n";
                echo "<li>ID: " . $order->getId() . " - " . $order->getCustomerName() . " - " . number_format($order->getCalcTotalMoney()) . " VNĐ</li>\n";
                echo "</ul>\n";
            }
        }
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    // 4. Lấy đơn hàng theo loại
    echo "<h2>4. Lấy đơn hàng theo loại</h2>\n";
    try {
        // Đơn hàng giao hàng tận nhà
        $shippingOrders = $client->orders()->getShippingOrders();
        echo "<p>🚚 Đơn hàng giao hàng tận nhà: " . $shippingOrders->getTotalRecords() . "</p>\n";
        
        // Đơn hàng mua tại quầy
        $counterOrders = $client->orders()->getCounterOrders();
        echo "<p>🏪 Đơn hàng mua tại quầy: " . $counterOrders->getTotalRecords() . "</p>\n";
        
        // Đơn hàng đặt trước
        $preOrders = $client->orders()->getPreOrders();
        echo "<p>📅 Đơn hàng đặt trước: " . $preOrders->getTotalRecords() . "</p>\n";
        
        // Đơn hàng trả hàng
        $returnOrders = $client->orders()->getReturnOrders();
        echo "<p>↩️ Đơn hàng trả hàng: " . $returnOrders->getTotalRecords() . "</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    // 5. Tìm kiếm theo khoảng thời gian
    echo "<h2>5. Tìm kiếm theo khoảng thời gian</h2>\n";
    try {
        $orders = $client->orders()->getByDateRange('2024-01-01', '2024-01-10');
        echo "<p>📅 Đơn hàng từ 01/01/2024 đến 10/01/2024: " . $orders->getTotalRecords() . "</p>\n";
        
        if ($orders->getTotalRecords() > 0) {
            // Lọc theo trạng thái
            $pendingOrders = $orders->filterByStatus('pending');
            echo "<p>⏳ Đơn hàng chờ xử lý: " . $pendingOrders->count() . "</p>\n";
            
            // Lọc theo loại
            $shippingOrders = $orders->filterByType(1);
            echo "<p>🚚 Đơn hàng giao hàng: " . $shippingOrders->count() . "</p>\n";
            
            // Lọc theo khoảng giá
            $highValueOrders = $orders->filterByAmountRange(1000000, 5000000);
            echo "<p>💰 Đơn hàng giá trị cao (1M-5M): " . $highValueOrders->count() . "</p>\n";
        }
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    // 6. Tìm kiếm nâng cao với nhiều tiêu chí
    echo "<h2>6. Tìm kiếm nâng cao với nhiều tiêu chí</h2>\n";
    try {
        $searchParams = [
            'fromDate' => '2024-01-01',
            'toDate' => '2024-01-10',
            'statuses' => ['pending', 'processing'],
            'type' => 1, // Giao hàng tận nhà
            'dataOptions' => ['giftProducts', 'marketingUtm'],
            'page' => 1,
            'limit' => 50
        ];
        
        // Validate dữ liệu tìm kiếm
        $isValid = $client->orders()->validateSearchRequest($searchParams);
        if ($isValid) {
            echo "<p>✅ Dữ liệu tìm kiếm hợp lệ</p>\n";
            
            $orders = $client->orders()->search($searchParams);
            echo "<p>📋 Kết quả tìm kiếm: " . $orders->getTotalRecords() . " đơn hàng</p>\n";
            
            if ($orders->getTotalRecords() > 0) {
                // Lấy thống kê
                $statusStats = $orders->getStatusStatistics();
                echo "<p>📊 Thống kê theo trạng thái:</p>\n";
                foreach ($statusStats as $status => $count) {
                    echo "<p>• " . $status . ": " . $count . "</p>\n";
                }
                
                $typeStats = $orders->getTypeStatistics();
                echo "<p>📊 Thống kê theo loại:</p>\n";
                foreach ($typeStats as $type => $count) {
                    echo "<p>• " . $type . ": " . $count . "</p>\n";
                }
                
                $channelStats = $orders->getSaleChannelStatistics();
                echo "<p>📊 Thống kê theo kênh bán:</p>\n";
                foreach ($channelStats as $channel => $count) {
                    echo "<p>• " . $channel . ": " . $count . "</p>\n";
                }
                
                // Tính tổng doanh thu và phí vận chuyển
                $totalRevenue = $orders->getTotalRevenue();
                $totalShippingFee = $orders->getTotalShippingFee();
                echo "<p>💰 Tổng doanh thu: " . number_format($totalRevenue) . " VNĐ</p>\n";
                echo "<p>🚚 Tổng phí vận chuyển: " . number_format($totalShippingFee) . " VNĐ</p>\n";
            }
        } else {
            echo "<p>❌ Dữ liệu tìm kiếm không hợp lệ</p>\n";
            $errors = $client->orders()->getSearchRequestErrors($searchParams);
            echo "<p>🔍 Chi tiết lỗi:</p>\n";
            foreach ($errors as $field => $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    echo "<p>• " . $field . ": " . $error . "</p>\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    // 7. Quản lý cache
    echo "<h2>7. Quản lý cache</h2>\n";
    try {
        // Lấy trạng thái cache
        $cacheStatus = $client->orders()->getCacheStatus();
        echo "<p>📊 Trạng thái cache:</p>\n";
        echo "<p>• Tổng số keys: " . $cacheStatus['total_keys'] . "</p>\n";
        
        if (isset($cacheStatus['keys'])) {
            foreach ($cacheStatus['keys'] as $key => $info) {
                echo "<p>• " . $key . " - TTL: " . $info['ttl'] . "s - Hết hạn: " . $info['expires_in'] . "</p>\n";
            }
        }
        
        // Kiểm tra cache có sẵn không
        $hasCache = $client->orders()->isCacheAvailable();
        echo "<p>💾 Cache có sẵn: " . ($hasCache ? 'Có' : 'Không') . "</p>\n";
        
        // Xóa cache (comment để tránh xóa cache trong ví dụ)
        // $client->orders()->clearCache();
        // echo "<p>🗑️ Đã xóa cache</p>\n";
        
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    // 8. Xử lý lỗi và validation
    echo "<h2>8. Xử lý lỗi và validation</h2>\n";
    try {
        // Test validation với dữ liệu không hợp lệ
        $invalidParams = [
            'fromDate' => '2024-01-01',
            'toDate' => '2024-01-25' // Vượt quá 10 ngày
        ];
        
        $isValid = $client->orders()->validateSearchRequest($invalidParams);
        if (!$isValid) {
            echo "<p>❌ Validation thất bại (như mong đợi)</p>\n";
            $errors = $client->orders()->getSearchRequestErrors($invalidParams);
            echo "<p>🔍 Chi tiết lỗi:</p>\n";
            foreach ($errors as $field => $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    echo "<p>• " . $field . ": " . $error . "</p>\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>\n";
    }

    echo "<h2>🎉 Hoàn thành!</h2>\n";
    echo "<p>Bạn đã thấy cách sử dụng Order Module để:</p>\n";
    echo "<ul>\n";
    echo "<li>✅ Tìm kiếm đơn hàng theo nhiều tiêu chí</li>\n";
    echo "<li>✅ Lọc và phân tích dữ liệu đơn hàng</li>\n";
    echo "<li>✅ Quản lý cache để tăng hiệu suất</li>\n";
    echo "<li>✅ Validate dữ liệu đầu vào</li>\n";
    echo "<li>✅ Xử lý lỗi một cách an toàn</li>\n";
    echo "</ul>\n";

} catch (Exception $e) {
    echo "<h2>❌ Lỗi khởi tạo</h2>\n";
    echo "<p>Không thể khởi tạo client: " . $e->getMessage() . "</p>\n";
    echo "<p>Vui lòng kiểm tra cấu hình trong file auth.json</p>\n";
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    margin: 20px;
    background-color: #f5f5f5;
}

h1 {
    color: #2c3e50;
    border-bottom: 3px solid #3498db;
    padding-bottom: 10px;
}

h2 {
    color: #34495e;
    border-left: 4px solid #3498db;
    padding-left: 15px;
    margin-top: 30px;
}

p {
    margin: 10px 0;
    padding: 5px 0;
}

ul {
    margin: 10px 0;
    padding-left: 20px;
}

li {
    margin: 5px 0;
}

.✅ { color: #27ae60; }
.❌ { color: #e74c3c; }
.ℹ️ { color: #3498db; }
.📋 { color: #9b59b6; }
.📊 { color: #f39c12; }
.💰 { color: #f1c40f; }
.🚚 { color: #e67e22; }
.🏪 { color: #95a5a6; }
.📅 { color: #1abc9c; }
.↩️ { color: #e91e63; }
.⏳ { color: #9c27b0; }
.💾 { color: #607d8b; }
.🗑️ { color: #795548; }
.🔍 { color: #ff9800; }
.🎉 { color: #4caf50; }
</style>
