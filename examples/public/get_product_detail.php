<?php
require_once __DIR__ . '/../boot/client.php';

use Puleeno\NhanhVn\Entities\Product\Product;

// Khởi tạo client với logger để debug
$client = bootNhanhVnClientWithLogger('DEBUG');

// Kiểm tra access token
if (!$client->getConfig()->getAccessToken()) {
    echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">';
    echo '<strong>❌ Lỗi:</strong> Chưa có access token. Vui lòng chạy OAuth flow trước.';
    echo '<br><a href="oauth.php" style="color: #721c24;">🔐 Lấy Access Token</a>';
    echo '</div>';
    exit;
}

$product = null;
$error = null;
$productId = null;

// Xử lý form submit
if ($_POST && isset($_POST['product_id']) && !empty($_POST['product_id'])) {
    $productId = (int) $_POST['product_id'];
    
    try {
        echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">';
        echo '<strong>🔄 Đang gọi ProductModule::detail()...</strong><br>';
        echo 'Product ID: ' . $productId;
        echo '</div>';
        
        $product = $client->products()->detail($productId);
        
        if ($product) {
            echo '<div style="background: #d1ecf1; color: #0c5460; padding: 15px; margin: 20px; border: 1px solid #bee5eb; border-radius: 5px;">';
            echo '<strong>✅ Thành công!</strong> Đã lấy được chi tiết sản phẩm.';
            echo '</div>';
        } else {
            $error = 'Không tìm thấy sản phẩm với ID: ' . $productId;
        }
        
    } catch (Exception $e) {
        $error = '❌ Lỗi khi lấy chi tiết sản phẩm: ' . $e->getMessage();
        echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">';
        echo '<strong>Error:</strong> ' . $error;
        echo '<br><br><strong>Stack trace:</strong><br>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lấy Chi Tiết Sản Phẩm - Nhanh.vn SDK</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
        }
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="number"]:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        button {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #2980b9;
        }
        .product-detail {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin-top: 30px;
        }
        .product-detail h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .product-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .info-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .info-section h3 {
            color: #495057;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-item {
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
            display: inline-block;
            width: 150px;
        }
        .info-value {
            color: #212529;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .navigation {
            text-align: center;
            margin: 30px 0;
        }
        .navigation a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .navigation a:hover {
            background: #5a6268;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin: 20px 0;
        }
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .inventory-item {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .inventory-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .inventory-value {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Lấy Chi Tiết Sản Phẩm - Nhanh.vn SDK</h1>
        
        <div class="navigation">
            <a href="index.php">🏠 Trang chủ</a>
            <a href="get_products.php">📦 Danh sách sản phẩm</a>
            <a href="get_categories.php">📂 Danh mục sản phẩm</a>
            <a href="oauth.php">🔐 OAuth</a>
        </div>

        <form method="POST" style="background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #dee2e6;">
            <div class="form-group">
                <label for="product_id">Product ID:</label>
                <input type="number" id="product_id" name="product_id" 
                       value="<?php echo htmlspecialchars($productId ?? ''); ?>" 
                       placeholder="Nhập Product ID (VD: 5003116)" required>
            </div>
            <button type="submit">🔍 Lấy Chi Tiết Sản Phẩm</button>
        </form>

        <?php if ($error): ?>
            <div class="error">
                <strong>❌ Lỗi:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($product): ?>
            <div class="product-detail">
                <h2>📋 Chi Tiết Sản Phẩm</h2>
                
                <div class="product-info">
                    <!-- Thông tin cơ bản -->
                    <div class="info-section">
                        <h3>📝 Thông Tin Cơ Bản</h3>
                        
                        <div class="info-item">
                            <span class="info-label">ID Nhanh:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getIdNhanh() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Mã sản phẩm:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getCode() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Tên sản phẩm:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getName() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Tên khác:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getOtherName() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Mã vạch:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getBarcode() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Trạng thái:</span>
                            <span class="info-value <?php echo ($product->getStatus() === 'Active') ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo ($product->getStatus() === 'Active') ? '✅ Hoạt động' : '❌ Không hoạt động'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Thông tin giá -->
                    <div class="info-section">
                        <h3>💰 Thông Tin Giá</h3>
                        
                        <div class="info-item">
                            <span class="info-label">Giá nhập:</span>
                            <span class="info-value"><?php echo number_format($product->getImportPrice() ?? 0, 0, ',', '.') . ' VNĐ'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Giá cũ:</span>
                            <span class="info-value"><?php echo number_format($product->getOldPrice() ?? 0, 0, ',', '.') . ' VNĐ'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Giá bán lẻ:</span>
                            <span class="info-value"><?php echo number_format($product->getPrice() ?? 0, 0, ',', '.') . ' VNĐ'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Giá bán buôn:</span>
                            <span class="info-value"><?php echo number_format($product->getWholesalePrice() ?? 0, 0, ',', '.') . ' VNĐ'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Thuế VAT:</span>
                            <span class="info-value"><?php echo ($product->getVat() ?? 0) . '%'; ?></span>
                        </div>
                    </div>

                    <!-- Thông tin danh mục và thương hiệu -->
                    <div class="info-section">
                        <h3>🏷️ Phân Loại</h3>
                        
                        <div class="info-item">
                            <span class="info-label">Danh mục ID:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getCategoryId() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Thương hiệu ID:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getBrandId() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Tên thương hiệu:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getBrandName() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Loại sản phẩm ID:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getTypeId() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Tên loại:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getTypeName() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Parent ID:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getParentId() ?? 'N/A'); ?></span>
                        </div>
                    </div>

                    <!-- Thông tin kích thước và trọng lượng -->
                    <div class="info-section">
                        <h3>📏 Kích Thước & Trọng Lượng</h3>
                        
                        <div class="info-item">
                            <span class="info-label">Chiều rộng:</span>
                            <span class="info-value"><?php echo ($product->getWidth() ?? 0) . ' cm'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Chiều cao:</span>
                            <span class="info-value"><?php echo ($product->getHeight() ?? 0) . ' cm'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Chiều dài:</span>
                            <span class="info-value"><?php echo ($product->getLength() ?? 0) . ' cm'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Trọng lượng:</span>
                            <span class="info-value"><?php echo ($product->getShippingWeight() ?? 0) . ' gram'; ?></span>
                        </div>
                    </div>

                    <!-- Thông tin bảo hành -->
                    <div class="info-section">
                        <h3>🛡️ Bảo Hành</h3>
                        
                        <div class="info-item">
                            <span class="info-label">Thời hạn BH:</span>
                            <span class="info-value"><?php echo ($product->getWarranty() ?? 0) . ' tháng'; ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Địa chỉ BH:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getWarrantyAddress() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">SĐT BH:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getWarrantyPhone() ?? 'N/A'); ?></span>
                        </div>
                    </div>

                    <!-- Thông tin hiển thị -->
                    <div class="info-section">
                        <h3>⭐ Hiển Thị</h3>
                        
                        <div class="info-item">
                            <span class="info-label">Sản phẩm hot:</span>
                            <span class="info-value"><?php echo ($product->getShowHot() ? '✅ Có' : '❌ Không'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Sản phẩm mới:</span>
                            <span class="info-value"><?php echo ($product->getShowNew() ? '✅ Có' : '❌ Không'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Hiển thị trang chủ:</span>
                            <span class="info-value"><?php echo ($product->getShowHome() ? '✅ Có' : '❌ Không'); ?></span>
                        </div>
                    </div>

                    <!-- Thông tin khác -->
                    <div class="info-section">
                        <h3>📅 Thông Tin Khác</h3>
                        
                        <div class="info-item">
                            <span class="info-label">Ngày tạo:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getCreatedDateTime() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Xuất xứ:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getCountryName() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Đơn vị tính:</span>
                            <span class="info-value"><?php echo htmlspecialchars($product->getUnit() ?? 'N/A'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Link preview:</span>
                            <span class="info-value">
                                <?php if ($product->getPreviewLink()): ?>
                                    <a href="<?php echo htmlspecialchars($product->getPreviewLink()); ?>" target="_blank">🔗 Xem sản phẩm</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Hình ảnh sản phẩm -->
                <?php if ($product->getImage() || $product->getImages()): ?>
                    <div class="info-section" style="margin-top: 20px;">
                        <h3>🖼️ Hình Ảnh Sản Phẩm</h3>
                        
                        <div class="image-gallery">
                            <?php if ($product->getImage()): ?>
                                <img src="<?php echo htmlspecialchars($product->getImage()); ?>" alt="Ảnh chính" class="product-image">
                            <?php endif; ?>
                            
                            <?php if ($product->getImages() && is_array($product->getImages())): ?>
                                <?php foreach ($product->getImages() as $image): ?>
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Ảnh sản phẩm" class="product-image">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Thông tin tồn kho -->
                <?php if ($product->getInventory()): ?>
                    <div class="info-section" style="margin-top: 20px;">
                        <h3>📦 Thông Tin Tồn Kho</h3>
                        
                        <?php 
                        $inventory = $product->getInventory();
                        if (is_array($inventory)):
                        ?>
                            <div class="inventory-grid">
                                <div class="inventory-item">
                                    <div class="inventory-label">Tổng tồn</div>
                                    <div class="inventory-value"><?php echo $inventory['remain'] ?? 0; ?></div>
                                </div>
                                <div class="inventory-item">
                                    <div class="inventory-label">Đang giao</div>
                                    <div class="inventory-value"><?php echo $inventory['shipping'] ?? 0; ?></div>
                                </div>
                                <div class="inventory-item">
                                    <div class="inventory-label">Tạm giữ</div>
                                    <div class="inventory-value"><?php echo $inventory['holding'] ?? 0; ?></div>
                                </div>
                                <div class="inventory-item">
                                    <div class="inventory-label">Lỗi</div>
                                    <div class="inventory-value"><?php echo $inventory['damage'] ?? 0; ?></div>
                                </div>
                                <div class="inventory-item">
                                    <div class="inventory-label">Có thể bán</div>
                                    <div class="inventory-value"><?php echo $inventory['available'] ?? 0; ?></div>
                                </div>
                                <div class="inventory-item">
                                    <div class="inventory-label">Bảo hành</div>
                                    <div class="inventory-value"><?php echo $inventory['warranty'] ?? 0; ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
