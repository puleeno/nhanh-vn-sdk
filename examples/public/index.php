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
        <h1>🚀 Nhanh.vn SDK v2.0</h1>
        <p class="subtitle">PHP SDK tiêu chuẩn để tích hợp với Nhanh.vn API</p>
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
                <div class="example-item">
                    <h3>🔐 OAuth Flow</h3>
                    <p>Demo OAuth flow để lấy access token từ Nhanh.vn</p>
                    <a href="oauth.php" class="btn btn-primary">Chạy OAuth</a>
                </div>
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
                    <h3>🔄 OAuth Callback</h3>
                    <p>Test OAuth callback URL</p>
                    <a href="callback.php" class="btn btn-info">Test Callback</a>
                </div>
                <div class="example-item">
                    <h3>🧪 Test Boot File</h3>
                    <p>Kiểm tra việc khởi tạo NhanhVnClient qua boot file</p>
                    <a href="test_boot.php" class="btn btn-warning">Test Boot</a>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>🔗 Navigation</h2>
            <p><a href="callback.php" class="btn btn-secondary">🔄 Test OAuth Callback</a></p>
        </div>
    </div>
</body>
</html>
