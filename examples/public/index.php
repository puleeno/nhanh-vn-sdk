<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhanh.vn SDK v2.0 - Examples</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🚀 Nhanh.vn SDK v0.4.0</h1>
        <p class="subtitle">PHP SDK tiêu chuẩn để tích hợp với Nhanh.vn API v2.0</p>
        <hr>

        <div class="section">
            <h2>📋 Thông tin Server</h2>
            <?php
            require_once __DIR__ . '/../vendor/autoload.php';
            use Examples\OAuthExample;

            // Khởi tạo ứng dụng
            $app = new OAuthExample();

            // Hiển thị thông tin server
            $app->showServerInfo();

            // Hiển thị link OAuth
            $app->showOAuthLink();

            // Hiển thị trạng thái hiện tại
            $app->showCurrentStatus();
            ?>
        </div>

        <div class="examples">
            <h2>🚀 Examples</h2>
            <div class="example-grid">
                <!-- Authentication & OAuth -->
                <div class="example-item">
                    <h3>🔐 OAuth Flow</h3>
                    <p>Demo OAuth flow để lấy access token từ Nhanh.vn</p>
                    <a href="oauth.php" class="btn btn-primary">Chạy OAuth</a>
                </div>
                <div class="example-item">
                    <h3>🔄 OAuth Callback</h3>
                    <p>Test OAuth callback URL</p>
                    <a href="callback.php" class="btn btn-info">Test Callback</a>
                </div>
                <div class="example-item">
                    <h3>🧪 Test Boot File</h3>
                    <p>Kiểm tra việc khởi tạo NhanhVnClient qua boot file</p>
                    <a href="test_boot.php" class="btn btn-warning">Test Boot</a>
                </div>

                <!-- Product Management -->
                <div class="example-item">
                    <h3>📦 Lấy Sản Phẩm</h3>
                    <p>Demo lấy danh sách sản phẩm từ Nhanh.vn API</p>
                    <a href="get_products.php" class="btn btn-secondary">Lấy Sản Phẩm</a>
                </div>
                <div class="example-item">
                    <h3>📝 Lấy Sản Phẩm (với Logger)</h3>
                    <p>Demo lấy sản phẩm với Monolog logging</p>
                    <a href="get_products_with_logger.php" class="btn btn-success">Lấy Sản Phẩm + Logger</a>
                </div>
                <div class="example-item">
                    <h3>📂 Lấy Danh Mục</h3>
                    <p>Demo lấy danh mục sản phẩm từ Nhanh.vn API</p>
                    <a href="get_categories.php" class="btn btn-warning">Lấy Danh Mục</a>
                </div>
                <div class="example-item">
                    <h3>🔍 Chi Tiết Sản Phẩm</h3>
                    <p>Demo lấy chi tiết sản phẩm từ Nhanh.vn API</p>
                    <a href="get_product_detail.php" class="btn btn-info">Chi Tiết Sản Phẩm</a>
                </div>
                <div class="example-item">
                    <h3>➕ Thêm Sản Phẩm</h3>
                    <p>Demo thêm sản phẩm mới và thêm nhiều sản phẩm cùng lúc</p>
                    <a href="add_product.php" class="btn btn-primary">Thêm Sản Phẩm</a>
                </div>
                <div class="example-item">
                    <h3>🖼️ Thêm Ảnh Sản Phẩm</h3>
                    <p>Demo thêm ảnh cho sản phẩm từ CDN khác</p>
                    <a href="add_product_images.php" class="btn btn-success">Thêm Ảnh Sản Phẩm</a>
                </div>

                <!-- Customer Management -->
                <div class="example-item">
                    <h3>👥 Tìm Kiếm Khách Hàng</h3>
                    <p>Demo tìm kiếm khách hàng với các bộ lọc khác nhau</p>
                    <a href="search_customers.php" class="btn btn-success">Tìm Kiếm Khách Hàng</a>
                </div>
                <div class="example-item">
                    <h3>➕ Thêm Khách Hàng</h3>
                    <p>Demo thêm khách hàng mới và thêm nhiều khách hàng cùng lúc</p>
                    <a href="add_customer.php" class="btn btn-primary">Thêm Khách Hàng</a>
                </div>

                <!-- Order Management -->
                <div class="example-item">
                    <h3>📦 Lấy Đơn Hàng</h3>
                    <p>Demo lấy danh sách đơn hàng với các bộ lọc và phân trang</p>
                    <a href="get_orders.php" class="btn btn-success">Lấy Đơn Hàng</a>
                </div>
                <div class="example-item">
                    <h3>➕ Thêm Đơn Hàng</h3>
                    <p>Demo thêm đơn hàng mới với đầy đủ tùy chọn vận chuyển và thanh toán</p>
                    <a href="add_order.php" class="btn btn-primary">Thêm Đơn Hàng</a>
                </div>
                <div class="example-item">
                    <h3>🔄 Cập Nhật Đơn Hàng</h3>
                    <p>Demo cập nhật đơn hàng: trạng thái, thanh toán, vận chuyển</p>
                    <a href="update_order.php" class="btn btn-warning">Cập Nhật Đơn Hàng</a>
                </div>

                <!-- Shipping & Location -->
                <div class="example-item">
                    <h3>🗺️ Lấy Địa Điểm</h3>
                    <p>Demo lấy danh sách thành phố, quận huyện, phường xã từ Nhanh.vn API</p>
                    <a href="get_locations.php" class="btn btn-info">Lấy Địa Điểm</a>
                </div>
                <div class="example-item">
                    <h3>🚚 Hãng Vận Chuyển</h3>
                    <p>Demo lấy danh sách hãng vận chuyển và dịch vụ vận chuyển với cache management</p>
                    <a href="get_shipping_carriers.php" class="btn btn-info">Hãng Vận Chuyển</a>
                </div>
                <div class="example-item">
                    <h3>💰 Tính Phí Vận Chuyển</h3>
                    <p>Demo tính phí vận chuyển cho đơn hàng từ Nhanh.vn (cả cổng Nhanh.vn và tự kết nối)</p>
                    <a href="calculate_shipping_fee.php" class="btn btn-success">Tính Phí Vận Chuyển</a>
                </div>

                <!-- Advanced Features -->
                <div class="example-item">
                    <h3>🚀 Nhanh Client Builder</h3>
                    <p>Demo Nhanh Client Builder - Cách tạo client với syntax gọn gàng và trực quan</p>
                    <a href="client_builder_demo.php" class="btn btn-warning">Client Builder Demo</a>
                </div>

                <!-- Additional Examples -->
                <div class="example-item">
                    <h3>📋 Orders (Legacy)</h3>
                    <p>Demo legacy orders API với các tính năng cũ</p>
                    <a href="orders.php" class="btn btn-secondary">Orders Legacy</a>
                </div>
                <div class="example-item">
                    <h3>⚙️ Config Example</h3>
                    <p>File cấu hình mẫu cho Nhanh.vn SDK</p>
                    <a href="config-example.json" class="btn btn-info">Config Example</a>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>📚 Tài liệu tham khảo</h2>
            <div class="reference-links">
                <a href="https://puleeno.github.io/nhanh-vn-sdk/" class="btn btn-secondary" target="_blank">📖 SDK Documentation</a>
                <a href="https://puleeno.github.io/nhanh-vn-sdk/docs/" class="btn btn-info" target="_blank">📋 API Reference</a>
                <a href="https://puleeno.github.io/nhanh-vn-sdk/docs/v2/" class="btn btn-warning" target="_blank">🚀 Advanced Usage</a>
            </div>
        </div>
    </div>
</body>
</html>
