<?php
/**
 * Example: Thêm khách hàng sử dụng Nhanh.vn SDK
 *
 * File này demo cách sử dụng CustomerModule để thêm khách hàng
 * bao gồm cả thêm một khách hàng và thêm nhiều khách hàng cùng lúc
 */

require_once __DIR__ . '/../boot/client.php';

// Khởi tạo client
bootNhanhVnClientSilent();

// Kiểm tra client đã sẵn sàng chưa
if (!isClientReady()) {
    die('❌ Client chưa sẵn sàng. Vui lòng kiểm tra cấu hình.');
}

// Lấy thông tin client
$clientInfo = getClientInfo();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm khách hàng - Nhanh.vn SDK Example</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>👥 Thêm khách hàng sử dụng Nhanh.vn SDK</h1>
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
                <a href="add_customer.php" class="nav-link active">➕ Thêm khách hàng</a>
            </nav>
        </div>

        <div class="section">
            <h2>📋 Thông tin Debug</h2>
            <div class="debug-info">
                <p><strong>Client Status:</strong> <?php echo $clientInfo['status']; ?></p>
                <p><strong>API Version:</strong> <?php echo $clientInfo['api_version']; ?></p>
                <p><strong>Business ID:</strong> <?php echo $clientInfo['business_id']; ?></p>
                <p><strong>Access Token:</strong> <?php echo $clientInfo['access_token'] ? '✅ Có' : '❌ Không có'; ?></p>
            </div>
        </div>

        <div class="section">
            <h2>➕ Thêm một khách hàng</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Tên khách hàng *:</label>
                    <input type="text" id="name" name="name" value="Nguyễn Văn A" required>
                </div>

                <div class="form-group">
                    <label for="mobile">Số điện thoại *:</label>
                    <input type="text" id="mobile" name="mobile" value="0987654321" required>
                </div>

                <div class="form-group">
                    <label for="type">Loại khách hàng:</label>
                    <select id="type" name="type">
                        <option value="1">Khách lẻ</option>
                        <option value="2">Khách buôn</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="nguyenvana@example.com">
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ:</label>
                    <input type="text" id="address" name="address" value="123 Đường ABC, Quận 1, TP.HCM">
                </div>

                <div class="form-group">
                    <label for="gender">Giới tính:</label>
                    <select id="gender" name="gender">
                        <option value="">Chọn giới tính</option>
                        <option value="1">Nam</option>
                        <option value="2">Nữ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="birthday">Ngày sinh:</label>
                    <input type="date" id="birthday" name="birthday" value="1990-01-01">
                </div>

                <div class="form-group">
                    <label for="points">Điểm tích lũy:</label>
                    <input type="number" id="points" name="points" value="0" min="0">
                </div>

                <div class="form-group">
                    <label for="description">Mô tả:</label>
                    <textarea id="description" name="description" rows="3">Khách hàng mới</textarea>
                </div>

                <button type="submit" name="add_single" class="btn btn-primary">➕ Thêm khách hàng</button>
            </form>
        </div>

        <div class="section">
            <h2>📦 Thêm nhiều khách hàng cùng lúc</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="batch_data">Dữ liệu khách hàng (JSON):</label>
                    <textarea id="batch_data" name="batch_data" rows="10" placeholder='[
  {
    "name": "Trần Thị B",
    "mobile": "0987654322",
    "type": 1,
    "email": "tranthib@example.com",
    "address": "456 Đường XYZ, Quận 2, TP.HCM",
    "gender": 2,
    "birthday": "1992-05-15",
    "points": 100
  },
  {
    "name": "Lê Văn C",
    "mobile": "0987654323",
    "type": 2,
    "email": "levanc@example.com",
    "address": "789 Đường DEF, Quận 3, TP.HCM",
    "gender": 1,
    "birthday": "1988-12-20",
    "points": 500
  }
]'><?php echo isset($_POST['batch_data']) ? htmlspecialchars($_POST['batch_data']) : '[
  {
    "name": "Trần Thị B",
    "mobile": "0987654322",
    "type": 1,
    "email": "tranthib@example.com",
    "address": "456 Đường XYZ, Quận 2, TP.HCM",
    "gender": 2,
    "birthday": "1992-05-15",
    "points": 100
  },
  {
    "name": "Lê Văn C",
    "mobile": "0987654323",
    "type": 2,
    "email": "levanc@example.com",
    "address": "789 Đường DEF, Quận 3, TP.HCM",
    "gender": 1,
    "birthday": "1988-12-20",
    "points": 500
  }
]'; ?></textarea>
                </div>

                <button type="submit" name="add_batch" class="btn btn-success">📦 Thêm nhiều khách hàng</button>
            </form>
        </div>

        <?php if ($_POST): ?>
        <div class="section">
            <h2>📊 Kết quả</h2>
            <?php
            try {
                $client = getNhanhVnClient();

                if (isset($_POST['add_single'])) {
                    // Thêm một khách hàng
                    $customerData = [
                        'name' => $_POST['name'],
                        'mobile' => $_POST['mobile'],
                        'type' => $_POST['type'] ? (int)$_POST['type'] : null,
                        'email' => $_POST['email'] ?: null,
                        'address' => $_POST['address'] ?: null,
                        'gender' => $_POST['gender'] ? (int)$_POST['gender'] : null,
                        'birthday' => $_POST['birthday'] ?: null,
                        'points' => $_POST['points'] ? (int)$_POST['points'] : null,
                        'description' => $_POST['description'] ?: null
                    ];

                    echo "<h3>➕ Thêm khách hàng đơn lẻ</h3>";
                    echo "<div class='debug-info'>";
                    echo "<p><strong>Dữ liệu gửi:</strong></p>";
                    echo "<pre>" . json_encode($customerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                    echo "</div>";

                    $response = $client->customers()->add($customerData);

                    echo "<div class='result-info'>";
                    echo "<p><strong>Kết quả:</strong></p>";
                    echo "<p><strong>Trạng thái:</strong> " . ($response->isSuccess() ? '✅ Thành công' : '❌ Thất bại') . "</p>";
                    echo "<p><strong>Mã phản hồi:</strong> " . $response->getCode() . "</p>";

                    if ($response->isSuccess()) {
                        echo "<p><strong>Số khách hàng đã xử lý:</strong> " . $response->getSuccessCount() . "</p>";
                        echo "<p><strong>ID khách hàng:</strong> " . ($response->getFirstProcessedCustomerId() ?? 'N/A') . "</p>";
                    } else {
                        echo "<p><strong>Lỗi:</strong> " . $response->getAllMessagesAsString() . "</p>";
                    }

                    echo "<p><strong>Summary:</strong></p>";
                    echo "<pre>" . json_encode($response->getSummary(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                    echo "</div>";

                } elseif (isset($_POST['add_batch'])) {
                    // Thêm nhiều khách hàng
                    $batchData = $_POST['batch_data'];
                    $customersData = json_decode($batchData, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Dữ liệu JSON không hợp lệ: ' . json_last_error_msg());
                    }

                    echo "<h3>📦 Thêm nhiều khách hàng</h3>";
                    echo "<div class='debug-info'>";
                    echo "<p><strong>Số lượng khách hàng:</strong> " . count($customersData) . "</p>";
                    echo "<p><strong>Dữ liệu gửi:</strong></p>";
                    echo "<pre>" . json_encode($customersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                    echo "</div>";

                    $response = $client->customers()->addBatch($customersData);

                    echo "<div class='result-info'>";
                    echo "<p><strong>Kết quả:</strong></p>";
                    echo "<p><strong>Trạng thái:</strong> " . ($response->isSuccess() ? '✅ Thành công' : '❌ Thất bại') . "</p>";
                    echo "<p><strong>Mã phản hồi:</strong> " . $response->getCode() . "</p>";

                    if ($response->isSuccess()) {
                        echo "<p><strong>Số khách hàng đã xử lý:</strong> " . $response->getSuccessCount() . "</p>";
                        echo "<p><strong>Danh sách ID:</strong> " . implode(', ', $response->getProcessedCustomerIds()) . "</p>";
                        echo "<p><strong>Tỷ lệ thành công:</strong> " . round($response->getSuccessRate() * 100, 2) . "%</p>";
                    } else {
                        echo "<p><strong>Lỗi:</strong> " . $response->getAllMessagesAsString() . "</p>";
                    }

                    echo "<p><strong>Summary:</strong></p>";
                    echo "<pre>" . json_encode($response->getSummary(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                    echo "</div>";
                }

            } catch (Exception $e) {
                echo "<div class='error-info'>";
                echo "<p><strong>❌ Lỗi:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
                echo "</div>";
            }
            ?>
        </div>
        <?php endif; ?>

        <div class="section">
            <h2>📚 Hướng dẫn sử dụng</h2>
            <div class="info-box">
                <h3>Thêm một khách hàng:</h3>
                <pre><code>$response = $client->customers()->add([
    'name' => 'Nguyễn Văn A',
    'mobile' => '0987654321',
    'type' => 1, // 1: Khách lẻ, 2: Khách buôn
    'email' => 'nguyenvana@example.com',
    'address' => '123 Đường ABC, Quận 1, TP.HCM',
    'gender' => 1, // 1: Nam, 2: Nữ
    'birthday' => '1990-01-01',
    'points' => 0
]);</code></pre>

                <h3>Thêm nhiều khách hàng:</h3>
                <pre><code>$response = $client->customers()->addBatch([
    [
        'name' => 'Trần Thị B',
        'mobile' => '0987654322',
        'type' => 1
    ],
    [
        'name' => 'Lê Văn C',
        'mobile' => '0987654323',
        'type' => 2
    ]
]);</code></pre>

                <h3>Kiểm tra kết quả:</h3>
                <pre><code>if ($response->isSuccess()) {
    echo "Thành công! Đã xử lý " . $response->getSuccessCount() . " khách hàng";
    echo "ID: " . implode(', ', $response->getProcessedCustomerIds());
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
                <a href="add_customer.php" class="nav-link active">➕ Thêm khách hàng</a>
            </nav>
        </div>
    </div>
</body>
</html>
