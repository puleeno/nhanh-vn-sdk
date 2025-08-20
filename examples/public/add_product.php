<?php
/**
 * Example: Thêm sản phẩm mới
 *
 * File này demo cách sử dụng SDK để thêm sản phẩm mới vào Nhanh.vn
 *
 * @package Puleeno\NhanhVn\Examples
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @since 2.0.0
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>➕ Thêm sản phẩm mới từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>➕ Thêm sản phẩm mới từ Nhanh.vn API sử dụng SDK</h1>
        <hr>

        <!-- Navigation Bar -->
        <div class="navigation-bar">
            <nav>
                <a href="index.php" class="nav-link">🏠 Trang chủ</a>
                <a href="get_products.php" class="nav-link">📦 Sản phẩm</a>
                <a href="get_categories.php" class="nav-link">📂 Danh mục</a>
                <a href="add_product.php" class="nav-link active">➕ Thêm sản phẩm</a>
                <a href="add_product_images.php" class="nav-link">🖼️ Thêm ảnh sản phẩm</a>
                <a href="search_customers.php" class="nav-link">👥 Khách hàng</a>
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

    // Lấy product module
    echo '<div class="section">';
    echo '<h3>📦 Khởi tạo Product Module</h3>';

    try {
        $productModule = $client->products();

        // DEBUG: Kiểm tra Product module
        echo '<div class="debug-info">';
        echo '<h4>🔍 Debug Product Module:</h4>';
        echo '<p><strong>Product Module Class:</strong> ' . get_class($productModule) . '</p>';
        echo '<p><strong>Product Module Methods:</strong></p>';
        echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($productModule))) . '</pre>';
        echo '</div>';

        echo '<div class="status success">';
        echo '<h4>✅ Product module đã sẵn sàng!</h4>';
        echo '<p><strong>Module:</strong> ' . get_class($productModule) . '</p>';
        echo '</div>';

    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khởi tạo Product Module</h4>';
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
    $productResult = null;
    $validationErrors = [];

    if ($_POST) {
        echo '<div class="section">';
        echo '<h3>📝 Xử lý thêm sản phẩm</h3>';

        try {
            // Chuẩn bị dữ liệu sản phẩm từ form
            $productData = [
                'id' => $_POST['productId'] ?? 'PROD_' . time(),
                'name' => $_POST['productName'] ?? '',
                'price' => (float)($_POST['price'] ?? 0),
                'code' => $_POST['productCode'] ?? '',
                'barcode' => $_POST['barcode'] ?? '',
                'description' => $_POST['description'] ?? '',
                'categoryId' => (int)($_POST['categoryId'] ?? 0),
                'brandId' => (int)($_POST['brandId'] ?? 0),
                'importPrice' => (float)($_POST['importPrice'] ?? 0),
                'wholesalePrice' => (float)($_POST['wholesalePrice'] ?? 0),
                'shippingWeight' => (int)($_POST['shippingWeight'] ?? 0),
                'vat' => (int)($_POST['vat'] ?? 0),
                'status' => $_POST['status'] ?? 'Active',
                'externalImages' => !empty($_POST['externalImages']) ? explode("\n", trim($_POST['externalImages'])) : []
            ];

            // Validate dữ liệu cơ bản
            if (empty($productData['name'])) {
                $validationErrors['productName'] = 'Tên sản phẩm không được để trống';
            }
            if ($productData['price'] <= 0) {
                $validationErrors['price'] = 'Giá sản phẩm phải lớn hơn 0';
            }
            if (empty($productData['code'])) {
                $validationErrors['productCode'] = 'Mã sản phẩm không được để trống';
            }

            if (empty($validationErrors)) {
                // Thêm sản phẩm
                $response = $productModule->add($productData);

                // DEBUG: Kiểm tra kết quả trả về
                echo '<div class="debug-info">';
                echo '<h5>🔍 Debug API Response:</h5>';
                echo '<p><strong>Response Type:</strong> ' . gettype($response) . '</p>';
                echo '<p><strong>Response Class:</strong> ' . (is_object($response) ? get_class($response) : 'N/A') . '</p>';
                echo '<p><strong>Response Null:</strong> ' . (is_null($response) ? 'Yes' : 'No') . '</p>';

                if (is_object($response)) {
                    echo '<p><strong>Response Methods:</strong></p>';
                    echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($response))) . '</pre>';
                }

                echo '<p><strong>Raw Response:</strong></p>';
                echo '<pre>' . htmlspecialchars(print_r($response, true)) . '</pre>';
                echo '</div>';

                echo '<div class="success">✅ Sản phẩm đã được thêm thành công!</div>';
                echo '<h5>Kết quả:</h5>';
                echo '<p><strong>ID hệ thống:</strong> ' . htmlspecialchars($productData['id']) . '</p>';

                if (is_object($response) && method_exists($response, 'getNhanhId')) {
                    echo '<p><strong>ID Nhanh.vn:</strong> ' . htmlspecialchars($response->getNhanhId($productData['id'])) . '</p>';
                }

                if (is_object($response) && method_exists($response, 'getBarcode')) {
                    echo '<p><strong>Barcode:</strong> ' . htmlspecialchars($response->getBarcode($productData['id'])) . '</p>';
                }

                $productResult = $response;
            } else {
                echo '<div class="error">❌ Có lỗi validation:</div>';
                echo '<ul>';
                foreach ($validationErrors as $field => $error) {
                    echo '<li><strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul>';
            }

        } catch (Exception $e) {
            echo '<div class="error">❌ Lỗi khi thêm sản phẩm: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }

        echo '</div>';
    }

    // Hiển thị form thêm sản phẩm
    echo '<div class="section">';
    echo '<h3>📝 Form thêm sản phẩm mới</h3>';

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

    <form method="POST" class="product-form">
        <div class="form-section">
            <h4>📋 Thông tin cơ bản</h4>

            <div class="form-group">
                <label for="productId">ID sản phẩm *</label>
                <input type="text" id="productId" name="productId" value="<?php echo htmlspecialchars($_POST['productId'] ?? 'PROD_' . time()); ?>" required>
                <small>Tự động tạo nếu để trống</small>
            </div>

            <div class="form-group">
                <label for="productName">Tên sản phẩm *</label>
                <input type="text" id="productName" name="productName" value="<?php echo htmlspecialchars($_POST['productName'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="productCode">Mã sản phẩm *</label>
                <input type="text" id="productCode" name="productCode" value="<?php echo htmlspecialchars($_POST['productCode'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" id="barcode" name="barcode" value="<?php echo htmlspecialchars($_POST['barcode'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h4>💰 Thông tin giá cả</h4>

            <div class="form-group">
                <label for="price">Giá bán *</label>
                <input type="number" id="price" name="price" step="1000" min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                <small>VNĐ</small>
            </div>

            <div class="form-group">
                <label for="importPrice">Giá nhập</label>
                <input type="number" id="importPrice" name="importPrice" step="1000" min="0" value="<?php echo htmlspecialchars($_POST['importPrice'] ?? ''); ?>">
                <small>VNĐ</small>
            </div>

            <div class="form-group">
                <label for="wholesalePrice">Giá bán buôn</label>
                <input type="number" id="wholesalePrice" name="wholesalePrice" step="1000" min="0" value="<?php echo htmlspecialchars($_POST['wholesalePrice'] ?? ''); ?>">
                <small>VNĐ</small>
            </div>

            <div class="form-group">
                <label for="vat">Thuế VAT (%)</label>
                <input type="number" id="vat" name="vat" min="0" max="100" value="<?php echo htmlspecialchars($_POST['vat'] ?? '10'); ?>">
                <small>Phần trăm</small>
            </div>
        </div>

        <div class="form-section">
            <h4>📂 Phân loại</h4>

            <div class="form-group">
                <label for="categoryId">ID danh mục</label>
                <input type="number" id="categoryId" name="categoryId" min="0" value="<?php echo htmlspecialchars($_POST['categoryId'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="brandId">ID thương hiệu</label>
                <input type="number" id="brandId" name="brandId" min="0" value="<?php echo htmlspecialchars($_POST['brandId'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <option value="Active" <?php echo ($_POST['status'] ?? 'Active') === 'Active' ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="Inactive" <?php echo ($_POST['status'] ?? '') === 'Inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                    <option value="Draft" <?php echo ($_POST['status'] ?? '') === 'Draft' ? 'selected' : ''; ?>>Nháp</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h4>📦 Thông tin vận chuyển</h4>

            <div class="form-group">
                <label for="shippingWeight">Cân nặng vận chuyển (gram)</label>
                <input type="number" id="shippingWeight" name="shippingWeight" min="0" value="<?php echo htmlspecialchars($_POST['shippingWeight'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section">
            <h4>🖼️ Ảnh sản phẩm</h4>

            <div class="form-group">
                <label for="externalImages">URL ảnh (mỗi dòng một URL)</label>
                <textarea id="externalImages" name="externalImages" rows="4" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"><?php echo htmlspecialchars($_POST['externalImages'] ?? ''); ?></textarea>
                <small>Mỗi dòng một URL ảnh, tối đa 20 ảnh</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
            <button type="reset" class="btn btn-secondary">Làm mới</button>
        </div>
    </form>

    <?php
    echo '</div>';

    // Hiển thị kết quả nếu có
    if ($productResult) {
        echo '<div class="section">';
        echo '<h3>📊 Kết quả thêm sản phẩm</h3>';
        echo '<div class="result-info">';
        echo '<p><strong>Trạng thái:</strong> Thành công</p>';
        echo '<p><strong>Thời gian:</strong> ' . date('Y-m-d H:i:s') . '</p>';
        echo '</div>';
        echo '</div>';
    }

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
        </div>
    </div>

    <script>
        // JavaScript cho form validation và UX
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate product ID nếu để trống
            const productIdField = document.getElementById('productId');
            if (productIdField && !productIdField.value) {
                productIdField.value = 'PROD_' + Date.now();
            }
        });
    </script>
</body>
</html>
?>
