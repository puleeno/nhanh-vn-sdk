<?php
/**
 * Example: Lấy chi tiết sản phẩm từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

use Puleeno\NhanhVn\Entities\Product\Product;

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Lấy chi tiết sản phẩm từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🔍 Lấy chi tiết sản phẩm từ Nhanh.vn API sử dụng SDK</h1>
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

    // Form nhập Product ID
    echo '<div class="section">';
    echo '<h3>🔍 Nhập Product ID</h3>';
    echo '<form method="POST" class="form-group">';
    echo '<label for="product_id">Product ID:</label>';
    echo '<input type="number" id="product_id" name="product_id" placeholder="Nhập Product ID (VD: 5003116)" required>';
    echo '<button type="submit" class="btn btn-primary">🔍 Lấy Chi Tiết Sản Phẩm</button>';
    echo '</form>';
    echo '</div>';

    // Xử lý form submit
    if ($_POST && isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $productId = (int) $_POST['product_id'];

        echo '<div class="section">';
        echo '<h3>🔄 Lấy chi tiết sản phẩm qua SDK</h3>';

        try {
            // DEBUG: Kiểm tra Product module
            echo '<div class="debug-info">';
            echo '<h4>🔍 Debug Product Module:</h4>';

            $productModule = $client->products();
            echo '<p><strong>Product Module Class:</strong> ' . get_class($productModule) . '</p>';
            echo '<p><strong>Product Module Methods:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($productModule))) . '</pre>';
            echo '</div>';

            // DEBUG: Kiểm tra detail method
            echo '<div class="debug-info">';
            echo '<h4>🔍 Debug Detail Method:</h4>';
            echo '<p><strong>Product ID:</strong> ' . $productId . '</p>';
            echo '<p><strong>Product ID Type:</strong> ' . gettype($productId) . '</p>';
            echo '</div>';

            // Sử dụng Product module của SDK
            echo '<div class="debug-info">';
            echo '<h4>🔄 Đang gọi ProductModule::detail()...</h4>';
            echo '</div>';

            $product = $client->products()->detail($productId);

            // DEBUG: Kiểm tra kết quả trả về
            echo '<div class="debug-info">';
            echo '<h4>🔍 Debug Detail Result:</h4>';
            echo '<p><strong>Result Type:</strong> ' . gettype($product) . '</p>';
            echo '<p><strong>Result Class:</strong> ' . (is_object($product) ? get_class($product) : 'N/A') . '</p>';
            echo '<p><strong>Result Null:</strong> ' . (is_null($product) ? 'Yes' : 'No') . '</p>';
            echo '<p><strong>Result Empty:</strong> ' . (empty($product) ? 'Yes' : 'No') . '</p>';

            if (is_object($product)) {
                echo '<p><strong>Result Methods:</strong></p>';
                echo '<pre>' . htmlspecialchars(implode(', ', get_class_methods($product))) . '</pre>';
            }

            echo '<p><strong>Raw Result:</strong></p>';
            echo '<pre>' . htmlspecialchars(print_r($product, true)) . '</pre>';
            echo '</div>';

            if ($product) {
                echo '<div class="status success">';
                echo '<h4>✅ Thành công! Đã lấy được chi tiết sản phẩm</h4>';
                echo '</div>';

                // Hiển thị thông tin sản phẩm
                echo '<div class="section">';
                echo '<h3>📋 Chi Tiết Sản Phẩm</h3>';

                echo '<div class="product-item">';
                echo '<h4>' . htmlspecialchars($product->getName() ?? 'N/A') . '</h4>';
                echo '<ul>';
                echo '<li><strong>ID Nhanh:</strong> ' . htmlspecialchars($product->getIdNhanh() ?? 'N/A') . '</li>';
                echo '<li><strong>Mã sản phẩm:</strong> ' . htmlspecialchars($product->getCode() ?? 'N/A') . '</li>';
                echo '<li><strong>Tên khác:</strong> ' . htmlspecialchars($product->getOtherName() ?? 'N/A') . '</li>';
                echo '<li><strong>Mã vạch:</strong> ' . htmlspecialchars($product->getBarcode() ?? 'N/A') . '</li>';
                echo '<li><strong>Giá nhập:</strong> ' . number_format($product->getImportPrice() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Giá cũ:</strong> ' . number_format($product->getOldPrice() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Giá bán lẻ:</strong> ' . number_format($product->getPrice() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Giá bán buôn:</strong> ' . number_format($product->getWholesalePrice() ?? 0) . ' VNĐ</li>';
                echo '<li><strong>Thuế VAT:</strong> ' . ($product->getVat() ?? 0) . '%</li>';
                echo '<li><strong>Danh mục ID:</strong> ' . htmlspecialchars($product->getCategoryId() ?? 'N/A') . '</li>';
                echo '<li><strong>Thương hiệu ID:</strong> ' . htmlspecialchars($product->getBrandId() ?? 'N/A') . '</li>';
                echo '<li><strong>Tên thương hiệu:</strong> ' . htmlspecialchars($product->getBrandName() ?? 'N/A') . '</li>';
                echo '<li><strong>Loại sản phẩm ID:</strong> ' . htmlspecialchars($product->getTypeId() ?? 'N/A') . '</li>';
                echo '<li><strong>Tên loại:</strong> ' . htmlspecialchars($product->getTypeName() ?? 'N/A') . '</li>';
                echo '<li><strong>Parent ID:</strong> ' . htmlspecialchars($product->getParentId() ?? 'N/A') . '</li>';
                echo '<li><strong>Chiều rộng:</strong> ' . ($product->getWidth() ?? 0) . ' cm</li>';
                echo '<li><strong>Chiều cao:</strong> ' . ($product->getHeight() ?? 0) . ' cm</li>';
                echo '<li><strong>Chiều dài:</strong> ' . ($product->getLength() ?? 0) . ' cm</li>';
                echo '<li><strong>Trọng lượng:</strong> ' . ($product->getShippingWeight() ?? 0) . ' gram</li>';
                echo '<li><strong>Thời hạn BH:</strong> ' . ($product->getWarranty() ?? 0) . ' tháng</li>';
                echo '<li><strong>Địa chỉ BH:</strong> ' . htmlspecialchars($product->getWarrantyAddress() ?? 'N/A') . '</li>';
                echo '<li><strong>SĐT BH:</strong> ' . htmlspecialchars($product->getWarrantyPhone() ?? 'N/A') . '</li>';
                echo '<li><strong>Sản phẩm hot:</strong> ' . ($product->getShowHot() ? '✅ Có' : '❌ Không') . '</li>';
                echo '<li><strong>Sản phẩm mới:</strong> ' . ($product->getShowNew() ? '✅ Có' : '❌ Không') . '</li>';
                echo '<li><strong>Hiển thị trang chủ:</strong> ' . ($product->getShowHome() ? '✅ Có' : '❌ Không') . '</li>';
                echo '<li><strong>Ngày tạo:</strong> ' . htmlspecialchars($product->getCreatedDateTime() ?? 'N/A') . '</li>';
                echo '<li><strong>Xuất xứ:</strong> ' . htmlspecialchars($product->getCountryName() ?? 'N/A') . '</li>';
                echo '<li><strong>Đơn vị tính:</strong> ' . htmlspecialchars($product->getUnit() ?? 'N/A') . '</li>';
                echo '<li><strong>Link preview:</strong> ' . ($product->getPreviewLink() ? '<a href="' . htmlspecialchars($product->getPreviewLink()) . '" target="_blank">🔗 Xem sản phẩm</a>' : 'N/A') . '</li>';
                echo '<li><strong>Trạng thái:</strong> ' . ($product->getStatus() === 'Active' ? '✅ Hoạt động' : '❌ Không hoạt động') . '</li>';
                echo '</ul>';
                echo '</div>';

                // Hiển thị hình ảnh sản phẩm
                if ($product->getImage() || $product->getImages()) {
                    echo '<div class="section">';
                    echo '<h4>🖼️ Hình Ảnh Sản Phẩm</h4>';
                    if ($product->getImage()) {
                        echo '<p><strong>Ảnh chính:</strong> <img src="' . htmlspecialchars($product->getImage()) . '" alt="Ảnh chính" style="max-width: 200px; max-height: 200px;"></p>';
                    }
                    if ($product->getImages() && is_array($product->getImages())) {
                        echo '<p><strong>Ảnh khác:</strong></p>';
                        foreach ($product->getImages() as $image) {
                            echo '<img src="' . htmlspecialchars($image) . '" alt="Ảnh sản phẩm" style="max-width: 150px; max-height: 150px; margin: 5px;">';
                        }
                    }
                    echo '</div>';
                }

                // Hiển thị thông tin tồn kho
                if ($product->getInventory()) {
                    echo '<div class="section">';
                    echo '<h4>📦 Thông Tin Tồn Kho</h4>';
                    $inventory = $product->getInventory();
                    if (is_array($inventory)) {
                        echo '<ul>';
                        echo '<li><strong>Tổng tồn:</strong> ' . ($inventory['remain'] ?? 0) . '</li>';
                        echo '<li><strong>Đang giao:</strong> ' . ($inventory['shipping'] ?? 0) . '</li>';
                        echo '<li><strong>Tạm giữ:</strong> ' . ($inventory['holding'] ?? 0) . '</li>';
                        echo '<li><strong>Lỗi:</strong> ' . ($inventory['damage'] ?? 0) . '</li>';
                        echo '<li><strong>Có thể bán:</strong> ' . ($inventory['available'] ?? 0) . '</li>';
                        echo '<li><strong>Bảo hành:</strong> ' . ($inventory['warranty'] ?? 0) . '</li>';
                        echo '</ul>';
                    }
                    echo '</div>';
                }

            } else {
                echo '<div class="status warning">';
                echo '<h4>📭 Không tìm thấy sản phẩm</h4>';
                echo '<p><strong>Lý do có thể:</strong></p>';
                echo '<ul>';
                echo '<li>ProductModule::detail() chưa implement API call thật</li>';
                echo '<li>API Nhanh.vn trả về empty data</li>';
                echo '<li>Product ID không tồn tại</li>';
                echo '</ul>';
                echo '</div>';
            }

        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '<h4>❌ Lỗi khi lấy chi tiết sản phẩm</h4>';
            echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Stack trace:</strong></p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }

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
            <p><a href="get_products.php" class="btn btn-secondary">📦 Danh sách sản phẩm</a></p>
            <p><a href="callback.php" class="btn btn-secondary">🔄 Test OAuth Callback</a></p>
        </div>
    </div>
</body>
</html>
