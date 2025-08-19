<?php
/**
 * Example: Thêm đơn hàng mới vào Nhanh.vn
 *
 * File này demo cách sử dụng API thêm đơn hàng với các tùy chọn khác nhau:
 * - Đơn hàng vận chuyển với bảng giá của Nhanh.vn
 * - Đơn hàng vận chuyển với bảng giá riêng
 * - Đơn hàng tại cửa hàng
 * - Đơn hàng đặt trước
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>➕ Thêm đơn hàng mới vào Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>➕ Thêm đơn hàng mới vào Nhanh.vn API</h1>
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

use Puleeno\NhanhVn\Entities\Order\OrderAddRequest;

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
    $orderResult = null;
    $validationErrors = [];

    if ($_POST) {
        echo '<div class="section">';
        echo '<h3>📝 Xử lý thêm đơn hàng</h3>';

        try {
            // Tạo OrderAddRequest từ form data
            $orderRequest = new OrderAddRequest();

            // Thông tin cơ bản
            $orderRequest->set('id', $_POST['orderId'] ?? '');
            $orderRequest->set('depotId', !empty($_POST['depotId']) ? (int)$_POST['depotId'] : null);
            $orderRequest->set('type', $_POST['type'] ?? 'Shipping');
            $orderRequest->set('customerName', $_POST['customerName'] ?? '');
            $orderRequest->set('customerMobile', $_POST['customerMobile'] ?? '');
            $orderRequest->set('customerEmail', $_POST['customerEmail'] ?? '');
            $orderRequest->set('customerAddress', $_POST['customerAddress'] ?? '');
            $orderRequest->set('customerCityName', $_POST['customerCityName'] ?? '');
            $orderRequest->set('customerDistrictName', $_POST['customerDistrictName'] ?? '');
            $orderRequest->set('customerWardLocationName', $_POST['customerWardLocationName'] ?? '');

            // Thông tin thanh toán
            $orderRequest->set('moneyDiscount', !empty($_POST['moneyDiscount']) ? (float)$_POST['moneyDiscount'] : null);
            $orderRequest->set('moneyTransfer', !empty($_POST['moneyTransfer']) ? (float)$_POST['moneyTransfer'] : null);
            $orderRequest->set('moneyDeposit', !empty($_POST['moneyDeposit']) ? (float)$_POST['moneyDeposit'] : null);
            $orderRequest->set('paymentMethod', $_POST['paymentMethod'] ?? null);
            $orderRequest->set('paymentCode', $_POST['paymentCode'] ?? null);

            // Thông tin vận chuyển
            $orderRequest->set('sendCarrierType', !empty($_POST['sendCarrierType']) ? (int)$_POST['sendCarrierType'] : null);

            if ($_POST['sendCarrierType'] == 1) {
                // Sử dụng bảng giá của Nhanh.vn
                $orderRequest->set('carrierId', !empty($_POST['carrierId']) ? (int)$_POST['carrierId'] : null);
                $orderRequest->set('carrierServiceId', !empty($_POST['carrierServiceId']) ? (int)$_POST['carrierServiceId'] : null);
            } elseif ($_POST['sendCarrierType'] == 2) {
                // Sử dụng bảng giá riêng
                $orderRequest->set('carrierAccountId', !empty($_POST['carrierAccountId']) ? (int)$_POST['carrierAccountId'] : null);
                $orderRequest->set('carrierShopId', !empty($_POST['carrierShopId']) ? (int)$_POST['carrierShopId'] : null);
                $orderRequest->set('carrierServiceCode', $_POST['carrierServiceCode'] ?? '');
            }

            $orderRequest->set('customerShipFee', !empty($_POST['customerShipFee']) ? (int)$_POST['customerShipFee'] : null);
            $orderRequest->set('deliveryDate', $_POST['deliveryDate'] ?? null);

            // Thông tin khác
            $orderRequest->set('status', $_POST['status'] ?? 'New');
            $orderRequest->set('description', $_POST['description'] ?? '');
            $orderRequest->set('privateDescription', $_POST['privateDescription'] ?? '');
            $orderRequest->set('trafficSource', $_POST['trafficSource'] ?? null);
            $orderRequest->set('couponCode', $_POST['couponCode'] ?? null);
            $orderRequest->set('autoSend', !empty($_POST['autoSend']) ? (int)$_POST['autoSend'] : null);
            $orderRequest->set('isPartDelivery', !empty($_POST['isPartDelivery']) ? (int)$_POST['isPartDelivery'] : null);

            // Danh sách sản phẩm
            if (!empty($_POST['productList'])) {
                $productList = [];
                foreach ($_POST['productList'] as $product) {
                    if (!empty($product['id']) && !empty($product['name']) && !empty($product['quantity']) && !empty($product['price'])) {
                        $productList[] = [
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'quantity' => (int)$product['quantity'],
                            'price' => (float)$product['price'],
                            'gifts' => !empty($product['gifts']) ? $product['gifts'] : null
                        ];
                    }
                }
                $orderRequest->set('productList', $productList);
            }

            // Kiểm tra validation
            if (!$orderRequest->validateForAdd()) {
                $validationErrors = $orderRequest->getErrors();
                $message = 'Dữ liệu không hợp lệ. Vui lòng kiểm tra các lỗi bên dưới.';

                echo '<div class="status error">';
                echo '<h4>❌ Validation failed</h4>';
                echo '<p>' . htmlspecialchars($message) . '</p>';
                echo '</div>';
            } else {
                // Gọi API thêm đơn hàng
                echo '<div class="debug-info">';
                echo '<h4>🔄 Đang gọi OrderModule::add()...</h4>';
                echo '</div>';

                $orderResult = $client->orders()->add($orderRequest);

                if ($orderResult->isSuccess()) {
                    $message = 'Thêm đơn hàng thành công!';

                    echo '<div class="status success">';
                    echo '<h4>✅ Thêm đơn hàng thành công!</h4>';
                    echo '<p><strong>Mã đơn hàng Nhanh.vn:</strong> ' . htmlspecialchars($orderResult->getNhanhOrderCode()) . '</p>';
                    echo '<p><strong>ID đơn hàng Nhanh.vn:</strong> ' . htmlspecialchars($orderResult->getNhanhOrderId()) . '</p>';
                    echo '<p><strong>Trạng thái:</strong> ' . htmlspecialchars($orderResult->getStatus()) . '</p>';
                    echo '<p><strong>Thời gian tạo:</strong> ' . htmlspecialchars($orderResult->getCreatedAt()) . '</p>';
                    echo '</div>';
                } else {
                    $message = 'Thêm đơn hàng thất bại: ' . $orderResult->getMessage();

                    echo '<div class="status error">';
                    echo '<h4>❌ Thêm đơn hàng thất bại</h4>';
                    echo '<p><strong>Lỗi:</strong> ' . htmlspecialchars($orderResult->getMessage()) . '</p>';
                    echo '</div>';
                }
            }

        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '<h4>❌ Lỗi khi thêm đơn hàng</h4>';
            echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Stack trace:</strong></p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }

        echo '</div>';
    }

    // Hiển thị form thêm đơn hàng
    echo '<div class="section">';
    echo '<h3>📝 Form thêm đơn hàng mới</h3>';

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
            <h4>📋 Thông tin cơ bản</h4>

            <div class="form-group">
                <label for="orderId">ID đơn hàng *</label>
                <input type="text" id="orderId" name="orderId" value="<?php echo htmlspecialchars($_POST['orderId'] ?? 'ORD_' . time()); ?>" required>
            </div>

            <div class="form-group">
                <label for="depotId">ID kho hàng</label>
                <input type="number" id="depotId" name="depotId" value="<?php echo htmlspecialchars($_POST['depotId'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="type">Loại đơn hàng</label>
                <select id="type" name="type">
                    <option value="Shipping" <?php echo ($_POST['type'] ?? 'Shipping') === 'Shipping' ? 'selected' : ''; ?>>Vận chuyển</option>
                    <option value="Shopping" <?php echo ($_POST['type'] ?? '') === 'Shopping' ? 'selected' : ''; ?>>Tại cửa hàng</option>
                    <option value="PreOrder" <?php echo ($_POST['type'] ?? '') === 'PreOrder' ? 'selected' : ''; ?>>Đặt trước</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h4>👤 Thông tin khách hàng</h4>

            <div class="form-group">
                <label for="customerName">Tên khách hàng *</label>
                <input type="text" id="customerName" name="customerName" value="<?php echo htmlspecialchars($_POST['customerName'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="customerMobile">Số điện thoại *</label>
                <input type="text" id="customerMobile" name="customerMobile" value="<?php echo htmlspecialchars($_POST['customerMobile'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="customerEmail">Email</label>
                <input type="email" id="customerEmail" name="customerEmail" value="<?php echo htmlspecialchars($_POST['customerEmail'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="customerAddress">Địa chỉ</label>
                <input type="text" id="customerAddress" name="customerAddress" value="<?php echo htmlspecialchars($_POST['customerAddress'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="customerCityName">Thành phố</label>
                <input type="text" id="customerCityName" name="customerCityName" value="<?php echo htmlspecialchars($_POST['customerCityName'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="customerDistrictName">Quận/Huyện</label>
                <input type="text" id="customerDistrictName" name="customerDistrictName" value="<?php echo htmlspecialchars($_POST['customerDistrictName'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="customerWardLocationName">Phường/Xã</label>
                <input type="text" id="customerWardLocationName" name="customerWardLocationName" value="<?php echo htmlspecialchars($_POST['customerWardLocationName'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section">
            <h4>💰 Thông tin thanh toán</h4>

            <div class="form-group">
                <label for="moneyDiscount">Tiền chiết khấu</label>
                <input type="number" id="moneyDiscount" name="moneyDiscount" step="0.01" value="<?php echo htmlspecialchars($_POST['moneyDiscount'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="moneyTransfer">Tiền chuyển khoản</label>
                <input type="number" id="moneyTransfer" name="moneyTransfer" step="0.01" value="<?php echo htmlspecialchars($_POST['moneyTransfer'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="moneyDeposit">Tiền đặt cọc</label>
                <input type="number" id="moneyDeposit" name="moneyDeposit" step="0.01" value="<?php echo htmlspecialchars($_POST['moneyDeposit'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="paymentMethod">Phương thức thanh toán</label>
                <select id="paymentMethod" name="paymentMethod">
                    <option value="">Chọn phương thức</option>
                    <option value="COD" <?php echo ($_POST['paymentMethod'] ?? '') === 'COD' ? 'selected' : ''; ?>>Tiền mặt khi nhận hàng</option>
                    <option value="Store" <?php echo ($_POST['paymentMethod'] ?? '') === 'Store' ? 'selected' : ''; ?>>Tại cửa hàng</option>
                    <option value="Gateway" <?php echo ($_POST['paymentMethod'] ?? '') === 'Gateway' ? 'selected' : ''; ?>>Cổng thanh toán</option>
                    <option value="Online" <?php echo ($_POST['paymentMethod'] ?? '') === 'Online' ? 'selected' : ''; ?>>Trực tuyến</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h4>🚚 Thông tin vận chuyển</h4>

            <div class="form-group">
                <label for="sendCarrierType">Loại vận chuyển</label>
                <select id="sendCarrierType" name="sendCarrierType" onchange="toggleCarrierFields()">
                    <option value="">Chọn loại</option>
                    <option value="1" <?php echo ($_POST['sendCarrierType'] ?? '') === '1' ? 'selected' : ''; ?>>Bảng giá của Nhanh.vn</option>
                    <option value="2" <?php echo ($_POST['sendCarrierType'] ?? '') === '2' ? 'selected' : ''; ?>>Bảng giá riêng</option>
                </select>
            </div>

            <div id="nhanhCarrierFields" class="carrier-fields" style="display: none;">
                <div class="form-group">
                    <label for="carrierId">ID hãng vận chuyển</label>
                    <input type="number" id="carrierId" name="carrierId" value="<?php echo htmlspecialchars($_POST['carrierId'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="carrierServiceId">ID dịch vụ vận chuyển</label>
                    <input type="number" id="carrierServiceId" name="carrierServiceId" value="<?php echo htmlspecialchars($_POST['carrierServiceId'] ?? ''); ?>">
                </div>
            </div>

            <div id="selfConnectCarrierFields" class="carrier-fields" style="display: none;">
                <div class="form-group">
                    <label for="carrierAccountId">ID tài khoản vận chuyển</label>
                    <input type="number" id="carrierAccountId" name="carrierAccountId" value="<?php echo htmlspecialchars($_POST['carrierAccountId'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="carrierShopId">ID shop vận chuyển</label>
                    <input type="number" id="carrierShopId" name="carrierShopId" value="<?php echo htmlspecialchars($_POST['carrierShopId'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="carrierServiceCode">Mã dịch vụ vận chuyển</label>
                    <input type="text" id="carrierServiceCode" name="carrierServiceCode" value="<?php echo htmlspecialchars($_POST['carrierServiceCode'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="customerShipFee">Phí vận chuyển</label>
                <input type="number" id="customerShipFee" name="customerShipFee" value="<?php echo htmlspecialchars($_POST['customerShipFee'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="deliveryDate">Ngày giao hàng</label>
                <input type="date" id="deliveryDate" name="deliveryDate" value="<?php echo htmlspecialchars($_POST['deliveryDate'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="autoSend">Tự động gửi sang hãng vận chuyển</label>
                <select id="autoSend" name="autoSend">
                    <option value="">Chọn</option>
                    <option value="0" <?php echo ($_POST['autoSend'] ?? '') === '0' ? 'selected' : ''; ?>>Không</option>
                    <option value="1" <?php echo ($_POST['autoSend'] ?? '') === '1' ? 'selected' : ''; ?>>Có</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h4>📦 Danh sách sản phẩm</h4>

            <div id="productList">
                <div class="product-item">
                    <div class="form-group">
                        <label>ID sản phẩm *</label>
                        <input type="text" name="productList[0][id]" required>
                    </div>
                    <div class="form-group">
                        <label>Tên sản phẩm *</label>
                        <input type="text" name="productList[0][name]" required>
                    </div>
                    <div class="form-group">
                        <label>Số lượng *</label>
                        <input type="number" name="productList[0][quantity]" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Giá *</label>
                        <input type="number" name="productList[0][price]" step="0.01" min="0" required>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addProduct()" class="btn btn-secondary">+ Thêm sản phẩm</button>
        </div>

        <div class="form-section">
            <h4>📝 Thông tin khác</h4>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <option value="New" <?php echo ($_POST['status'] ?? 'New') === 'New' ? 'selected' : ''; ?>>Mới</option>
                    <option value="Confirming" <?php echo ($_POST['status'] ?? '') === 'Confirming' ? 'selected' : ''; ?>>Đang xác nhận</option>
                    <option value="Confirmed" <?php echo ($_POST['status'] ?? '') === 'Confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="privateDescription">Mô tả riêng</label>
                <textarea id="privateDescription" name="privateDescription" rows="3"><?php echo htmlspecialchars($_POST['privateDescription'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="trafficSource">Nguồn traffic</label>
                <input type="text" id="trafficSource" name="trafficSource" value="<?php echo htmlspecialchars($_POST['trafficSource'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="couponCode">Mã giảm giá</label>
                <input type="text" id="couponCode" name="couponCode" value="<?php echo htmlspecialchars($_POST['couponCode'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="isPartDelivery">Giao hàng từng phần</label>
                <select id="isPartDelivery" name="isPartDelivery">
                    <option value="">Chọn</option>
                    <option value="0" <?php echo ($_POST['isPartDelivery'] ?? '') === '0' ? 'selected' : ''; ?>>Không</option>
                    <option value="1" <?php echo ($_POST['isPartDelivery'] ?? '') === '1' ? 'selected' : ''; ?>>Có</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Thêm đơn hàng</button>
            <button type="reset" class="btn btn-secondary">Làm mới</button>
        </div>
    </form>

    <?php
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
            <p><a href="get_orders.php" class="btn btn-secondary">📦 Xem danh sách đơn hàng</a></p>
        </div>
    </div>

    <script>
        let productIndex = 1;

        function addProduct() {
            const productList = document.getElementById('productList');
            const newProduct = document.createElement('div');
            newProduct.className = 'product-item';
            newProduct.innerHTML = `
                <div class="form-group">
                    <label>ID sản phẩm *</label>
                    <input type="text" name="productList[${productIndex}][id]" required>
                </div>
                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="productList[${productIndex}][name]" required>
                </div>
                <div class="form-group">
                    <label>Số lượng *</label>
                    <input type="number" name="productList[${productIndex}][quantity]" min="1" required>
                </div>
                <div class="form-group">
                    <label>Giá *</label>
                    <input type="number" name="productList[${productIndex}][price]" step="0.01" min="0" required>
                </div>
                <button type="button" onclick="removeProduct(this)" class="btn-remove">Xóa</button>
            `;
            productList.appendChild(newProduct);
            productIndex++;
        }

        function removeProduct(button) {
            button.parentElement.remove();
        }

        function toggleCarrierFields() {
            const sendCarrierType = document.getElementById('sendCarrierType').value;
            const nhanhFields = document.getElementById('nhanhCarrierFields');
            const selfConnectFields = document.getElementById('selfConnectCarrierFields');

            nhanhFields.style.display = 'none';
            selfConnectFields.style.display = 'none';

            if (sendCarrierType === '1') {
                nhanhFields.style.display = 'block';
            } else if (sendCarrierType === '2') {
                selfConnectFields.style.display = 'block';
            }
        }

        // Khởi tạo trạng thái ban đầu
        document.addEventListener('DOMContentLoaded', function() {
            toggleCarrierFields();
        });
    </script>
</body>
</html>
