<?php
/**
 * Example: Cập nhật đơn hàng trên Nhanh.vn
 *
 * File này demo cách sử dụng SDK để cập nhật đơn hàng với các tùy chọn khác nhau:
 * - Cập nhật trạng thái đơn hàng
 * - Cập nhật thông tin thanh toán
 * - Gửi đơn hàng sang hãng vận chuyển
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔄 Cập nhật đơn hàng trên Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🔄 Cập nhật đơn hàng trên Nhanh.vn API</h1>
        <p class="subtitle">Sử dụng SDK để cập nhật thông tin đơn hàng</p>
        <hr>

        <!-- Navigation Bar -->
        <div class="navigation-bar">
            <nav>
                <a href="index.php" class="nav-link">🏠 Trang chủ</a>
                <a href="get_products.php" class="nav-link">📦 Sản phẩm</a>
                <a href="get_categories.php" class="nav-link">📂 Danh mục</a>
                <a href="add_product.php" class="nav-link">➕ Thêm sản phẩm</a>
                <a href="add_product_images.php" class="nav-link">🖼️ Thêm ảnh sản phẩm</a>
                <a href="search_customers.php" class="nav-link">👥 Tìm kiếm khách hàng</a>
                <a href="add_customer.php" class="nav-link">➕ Thêm khách hàng</a>
                <a href="get_orders.php" class="nav-link">📦 Lấy đơn hàng</a>
                <a href="add_order.php" class="nav-link">➕ Thêm đơn hàng</a>
                <a href="update_order.php" class="nav-link active">🔄 Cập nhật đơn hàng</a>
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

use Puleeno\NhanhVn\Entities\Order\OrderUpdateRequest;

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

    // Xử lý form submit
    $message = '';
    $updateResult = null;
    $validationErrors = [];

    if ($_POST) {
        echo '<div class="section">';
        echo '<h3>📝 Xử lý cập nhật đơn hàng</h3>';

        try {
            // Chuẩn bị dữ liệu từ form
            $updateData = [];

            // Thông tin định danh đơn hàng
            if (!empty($_POST['id'])) {
                $updateData['id'] = $_POST['id'];
            }
            if (!empty($_POST['orderId'])) {
                $updateData['orderId'] = $_POST['orderId'];
            }

            // Thông tin cập nhật
            if (!empty($_POST['autoSend'])) {
                $updateData['autoSend'] = (int)$_POST['autoSend'];
            }

            if (!empty($_POST['moneyTransfer'])) {
                $updateData['moneyTransfer'] = (float)$_POST['moneyTransfer'];
            }

            if (!empty($_POST['moneyTransferAccountId'])) {
                $updateData['moneyTransferAccountId'] = (int)$_POST['moneyTransferAccountId'];
            }

            if (!empty($_POST['paymentCode'])) {
                $updateData['paymentCode'] = $_POST['paymentCode'];
            }

            if (!empty($_POST['paymentGateway'])) {
                $updateData['paymentGateway'] = $_POST['paymentGateway'];
            }

            if (!empty($_POST['status'])) {
                $updateData['status'] = $_POST['status'];
            }

            if (!empty($_POST['description'])) {
                $updateData['description'] = $_POST['description'];
            }

            if (!empty($_POST['privateDescription'])) {
                $updateData['privateDescription'] = $_POST['privateDescription'];
            }

            if (!empty($_POST['customerShipFee'])) {
                $updateData['customerShipFee'] = (float)$_POST['customerShipFee'];
            }

            // Tạo OrderUpdateRequest từ array data
            $updateRequest = new OrderUpdateRequest($updateData);

            // Kiểm tra validation
            if (!$updateRequest->isValid()) {
                $validationErrors = $updateRequest->getErrors();
                $message = 'Dữ liệu không hợp lệ. Vui lòng kiểm tra các lỗi bên dưới.';

                echo '<div class="status error">';
                echo '<h4>❌ Validation failed</h4>';
                echo '<p>' . htmlspecialchars($message) . '</p>';
                echo '</div>';
            } else {
                // Gọi API cập nhật đơn hàng
                echo '<div class="debug-info">';
                echo '<h4>🔄 Đang gọi OrderModule::update()...</h4>';
                echo '</div>';

                $updateResult = $client->orders()->update($updateRequest);

                if ($updateResult->isSuccess()) {
                    $message = 'Cập nhật đơn hàng thành công!';

                    echo '<div class="status success">';
                    echo '<h4>✅ Cập nhật đơn hàng thành công!</h4>';
                    echo '<p><strong>ID đơn hàng:</strong> ' . htmlspecialchars($updateResult->getOrderId()) . '</p>';
                    echo '<p><strong>Trạng thái:</strong> ' . htmlspecialchars($updateResult->getStatus() ?? 'N/A') . '</p>';
                    
                    if ($updateResult->hasCarrierCode()) {
                        echo '<p><strong>Mã vận đơn:</strong> ' . htmlspecialchars($updateResult->getCarrierCode()) . '</p>';
                    }
                    
                    if ($updateResult->hasShipFee()) {
                        echo '<p><strong>Phí vận chuyển:</strong> ' . number_format($updateResult->getShipFee()) . ' VNĐ</p>';
                    }
                    
                    if ($updateResult->hasCodFee()) {
                        echo '<p><strong>Phí thu tiền hộ:</strong> ' . number_format($updateResult->getCodFee()) . ' VNĐ</p>';
                    }
                    
                    if ($updateResult->hasDiscounts()) {
                        echo '<p><strong>Tổng giảm giá:</strong> ' . number_format($updateResult->getTotalDiscounts()) . ' VNĐ</p>';
                    }
                    echo '</div>';
                } else {
                    $message = 'Cập nhật đơn hàng thất bại: ' . $updateResult->getAllMessagesAsString();

                    echo '<div class="status error">';
                    echo '<h4>❌ Cập nhật đơn hàng thất bại</h4>';
                    echo '<p><strong>Lỗi:</strong> ' . htmlspecialchars($updateResult->getAllMessagesAsString()) . '</p>';
                    echo '</div>';
                }
            }

        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '<h4>❌ Lỗi khi cập nhật đơn hàng</h4>';
            echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Stack trace:</strong></p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }

        echo '</div>';
    }

    // Hiển thị form cập nhật đơn hàng
    echo '<div class="section">';
    echo '<h3>📝 Form cập nhật đơn hàng</h3>';

    if (!empty($validationErrors)) {
        echo '<div class="validation-errors">';
        echo '<h4>❌ Lỗi validation:</h4>';
        echo '<ul>';
        foreach ($validationErrors as $field => $error) {
            echo '<li><strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    ?>

    <form method="POST" class="order-form">
        <div class="form-section">
            <h4>🆔 Thông tin định danh đơn hàng</h4>
            <p class="form-note">Phải cung cấp ít nhất một trong hai giá trị: id hoặc orderId</p>

            <div class="form-group">
                <label for="id">ID đơn hàng trên website</label>
                <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($_POST['id'] ?? ''); ?>" placeholder="VD: ORD_123456">
            </div>

            <div class="form-group">
                <label for="orderId">ID đơn hàng Nhanh.vn *</label>
                <input type="text" id="orderId" name="orderId" value="<?php echo htmlspecialchars($_POST['orderId'] ?? ''); ?>" placeholder="VD: 125123098" required>
            </div>
        </div>

        <div class="form-section">
            <h4>📊 Cập nhật trạng thái</h4>

            <div class="form-group">
                <label for="status">Trạng thái mới</label>
                <select id="status" name="status">
                    <option value="">Chọn trạng thái</option>
                    <option value="Success" <?php echo ($_POST['status'] ?? '') === 'Success' ? 'selected' : ''; ?>>Thành công</option>
                    <option value="Confirmed" <?php echo ($_POST['status'] ?? '') === 'Confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                    <option value="Canceled" <?php echo ($_POST['status'] ?? '') === 'Canceled' ? 'selected' : ''; ?>>Khách hủy</option>
                    <option value="Aborted" <?php echo ($_POST['status'] ?? '') === 'Aborted' ? 'selected' : ''; ?>>Hệ thống hủy</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Ghi chú khách hàng</label>
                <textarea id="description" name="description" rows="3" placeholder="Ghi chú cho khách hàng"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="privateDescription">Ghi chú nội bộ</label>
                <textarea id="privateDescription" name="privateDescription" rows="3" placeholder="Ghi chú nội bộ"><?php echo htmlspecialchars($_POST['privateDescription'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h4>💰 Cập nhật thông tin thanh toán</h4>

            <div class="form-group">
                <label for="moneyTransfer">Số tiền chuyển khoản</label>
                <input type="number" id="moneyTransfer" name="moneyTransfer" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['moneyTransfer'] ?? ''); ?>" placeholder="VD: 500000">
            </div>

            <div class="form-group">
                <label for="moneyTransferAccountId">ID tài khoản nhận tiền</label>
                <input type="number" id="moneyTransferAccountId" name="moneyTransferAccountId" value="<?php echo htmlspecialchars($_POST['moneyTransferAccountId'] ?? ''); ?>" placeholder="VD: 123">
            </div>

            <div class="form-group">
                <label for="paymentCode">Mã giao dịch thanh toán</label>
                <input type="text" id="paymentCode" name="paymentCode" value="<?php echo htmlspecialchars($_POST['paymentCode'] ?? ''); ?>" placeholder="VD: TXN_123456789">
            </div>

            <div class="form-group">
                <label for="paymentGateway">Tên cổng thanh toán</label>
                <input type="text" id="paymentGateway" name="paymentGateway" value="<?php echo htmlspecialchars($_POST['paymentGateway'] ?? ''); ?>" placeholder="VD: VNPay, Momo, ZaloPay">
            </div>
        </div>

        <div class="form-section">
            <h4>🚚 Cập nhật thông tin vận chuyển</h4>

            <div class="form-group">
                <label for="autoSend">Tự động gửi sang hãng vận chuyển</label>
                <select id="autoSend" name="autoSend">
                    <option value="">Chọn</option>
                    <option value="0" <?php echo ($_POST['autoSend'] ?? '') === '0' ? 'selected' : ''; ?>>Không</option>
                    <option value="1" <?php echo ($_POST['autoSend'] ?? '') === '1' ? 'selected' : ''; ?>>Có</option>
                </select>
                <small>Set value = 1 để gửi đơn hàng sang hãng vận chuyển</small>
            </div>

            <div class="form-group">
                <label for="customerShipFee">Phí ship báo khách</label>
                <input type="number" id="customerShipFee" name="customerShipFee" step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['customerShipFee'] ?? ''); ?>" placeholder="VD: 30000">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">🔄 Cập nhật đơn hàng</button>
            <button type="reset" class="btn btn-secondary">Làm mới</button>
        </div>
    </form>

    <?php
    echo '</div>';

    // Demo các method tiện ích
    echo '<div class="section">';
    echo '<h3>🔧 Demo các method tiện ích</h3>';

    try {
        echo '<div class="debug-info">';
        echo '<h4>🔄 Đang test các method tiện ích...</h4>';
        echo '</div>';

        // Test updateStatus method
        echo '<div class="method-test">';
        echo '<h4>📋 Test updateStatus() method:</h4>';
        echo '<p>Method này cho phép cập nhật trạng thái đơn hàng một cách nhanh chóng:</p>';
        echo '<pre><code>$response = $client->orders()->updateStatus("125123098", "Confirmed", "Đã xác nhận đơn hàng", "Ghi chú nội bộ");</code></pre>';
        echo '</div>';

        // Test updatePayment method
        echo '<div class="method-test">';
        echo '<h4>📋 Test updatePayment() method:</h4>';
        echo '<p>Method này cho phép cập nhật thông tin thanh toán:</p>';
        echo '<pre><code>$response = $client->orders()->updatePayment("125123098", 500000, "TXN_123456", "VNPay", 123);</code></pre>';
        echo '</div>';

        // Test sendToCarrier method
        echo '<div class="method-test">';
        echo '<h4>📋 Test sendToCarrier() method:</h4>';
        echo '<p>Method này cho phép gửi đơn hàng sang hãng vận chuyển:</p>';
        echo '<pre><code>$response = $client->orders()->sendToCarrier("125123098", 30000);</code></pre>';
        echo '</div>';

        // Test updateFromArray method
        echo '<div class="method-test">';
        echo '<h4>📋 Test updateFromArray() method:</h4>';
        echo '<p>Method này cho phép cập nhật từ array data:</p>';
        echo '<pre><code>$updateData = [
    "orderId" => "125123098",
    "status" => "Success",
    "moneyTransfer" => 500000,
    "paymentCode" => "TXN_123456"
];
$response = $client->orders()->updateFromArray($updateData);</code></pre>';
        echo '</div>';

    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khi test các method tiện ích</h4>';
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
            <h3>📚 Hướng dẫn sử dụng</h3>
            <div class="info-box">
                <h4>Cập nhật trạng thái đơn hàng:</h4>
                <pre><code>$response = $client->orders()->updateStatus(
    "125123098",           // ID đơn hàng Nhanh.vn
    "Confirmed",           // Trạng thái mới
    "Đã xác nhận đơn hàng", // Ghi chú khách hàng
    "Ghi chú nội bộ"       // Ghi chú nội bộ
);</code></pre>

                <h4>Cập nhật thông tin thanh toán:</h4>
                <pre><code>$response = $client->orders()->updatePayment(
    "125123098",           // ID đơn hàng Nhanh.vn
    500000,                // Số tiền chuyển khoản
    "TXN_123456",          // Mã giao dịch
    "VNPay",               // Tên cổng thanh toán
    123                    // ID tài khoản nhận tiền (tùy chọn)
);</code></pre>

                <h4>Gửi đơn hàng sang hãng vận chuyển:</h4>
                <pre><code>$response = $client->orders()->sendToCarrier(
    "125123098",           // ID đơn hàng Nhanh.vn
    30000                  // Phí ship báo khách (tùy chọn)
);</code></pre>

                <h4>Kiểm tra kết quả:</h4>
                <pre><code>if ($response->isSuccess()) {
    echo "Thành công! ID: " . $response->getOrderId();
    
    if ($response->hasCarrierCode()) {
        echo "Mã vận đơn: " . $response->getCarrierCode();
    }
    
    if ($response->hasShipFee()) {
        echo "Phí ship: " . number_format($response->getShipFee()) . " VNĐ";
    }
} else {
    echo "Lỗi: " . $response->getAllMessagesAsString();
}</code></pre>
            </div>
        </div>

        <!-- Navigation Bar Bottom -->
        <div class="navigation-bar">
            <nav>
                <a href="index.php" class="nav-link">🏠 Trang chủ</a>
                <a href="get_products.php" class="nav-link">📦 Sản phẩm</a>
                <a href="get_categories.php" class="nav-link">📂 Danh mục</a>
                <a href="add_product.php" class="nav-link">➕ Thêm sản phẩm</a>
                <a href="add_product_images.php" class="nav-link">🖼️ Thêm ảnh sản phẩm</a>
                <a href="search_customers.php" class="nav-link">👥 Tìm kiếm khách hàng</a>
                <a href="add_customer.php" class="nav-link">➕ Thêm khách hàng</a>
                <a href="get_orders.php" class="nav-link">📦 Lấy đơn hàng</a>
                <a href="add_order.php" class="nav-link">➕ Thêm đơn hàng</a>
                <a href="update_order.php" class="nav-link active">🔄 Cập nhật đơn hàng</a>
            </nav>
        </div>
    </div>
</body>
</html>
