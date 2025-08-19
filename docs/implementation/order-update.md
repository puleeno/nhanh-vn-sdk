# Order Update API Implementation

## Tổng quan

API cập nhật đơn hàng (`/api/order/update`) được implement đầy đủ trong Nhanh.vn SDK với các tính năng:

- Cập nhật trạng thái đơn hàng
- Cập nhật thông tin thanh toán
- Gửi đơn hàng sang hãng vận chuyển
- Validation dữ liệu đầu vào
- Xử lý lỗi và response

## 🏗️ Kiến trúc Implementation

### 1. Entities

#### OrderUpdateRequest
```php
use Puleeno\NhanhVn\Entities\Order\OrderUpdateRequest;

// Tạo request cập nhật trạng thái
$request = OrderUpdateRequest::createStatusUpdate(
    "125123098",           // ID đơn hàng Nhanh.vn
    "Confirmed",           // Trạng thái mới
    "Đã xác nhận đơn hàng", // Ghi chú khách hàng
    "Ghi chú nội bộ"       // Ghi chú nội bộ
);

// Tạo request cập nhật thanh toán
$request = OrderUpdateRequest::createPaymentUpdate(
    "125123098",           // ID đơn hàng Nhanh.vn
    500000,                // Số tiền chuyển khoản
    "TXN_123456",          // Mã giao dịch
    "VNPay",               // Tên cổng thanh toán
    123                    // ID tài khoản nhận tiền (tùy chọn)
);

// Tạo request gửi sang hãng vận chuyển
$request = OrderUpdateRequest::createShippingUpdate(
    "125123098",           // ID đơn hàng Nhanh.vn
    30000                  // Phí ship báo khách (tùy chọn)
);
```

#### OrderUpdateResponse
```php
use Puleeno\NhanhVn\Entities\Order\OrderUpdateResponse;

// Kiểm tra kết quả
if ($response->isSuccess()) {
    echo "Thành công! ID: " . $response->getOrderId();

    if ($response->hasCarrierCode()) {
        echo "Mã vận đơn: " . $response->getCarrierCode();
    }

    if ($response->hasShipFee()) {
        echo "Phí ship: " . number_format($response->getShipFee()) . " VNĐ";
        echo "Phí ship thực tế: " . number_format($response->getActualShipFee()) . " VNĐ";
    }

    if ($response->hasDiscounts()) {
        echo "Tổng giảm giá: " . number_format($response->getTotalDiscounts()) . " VNĐ";
    }
} else {
    echo "Lỗi: " . $response->getAllMessagesAsString();
}
```

### 2. Module Methods

#### Cập nhật cơ bản
```php
// Cập nhật từ OrderUpdateRequest entity
$response = $client->orders()->update($request);

// Cập nhật từ array data
$updateData = [
    'orderId' => '125123098',
    'status' => 'Success',
    'moneyTransfer' => 500000,
    'paymentCode' => 'TXN_123456'
];
$response = $client->orders()->updateFromArray($updateData);
```

#### Cập nhật trạng thái
```php
$response = $client->orders()->updateStatus(
    "125123098",           // ID đơn hàng Nhanh.vn
    "Confirmed",           // Trạng thái mới
    "Đã xác nhận đơn hàng", // Ghi chú khách hàng
    "Ghi chú nội bộ"       // Ghi chú nội bộ
);
```

#### Cập nhật thanh toán
```php
$response = $client->orders()->updatePayment(
    "125123098",           // ID đơn hàng Nhanh.vn
    500000,                // Số tiền chuyển khoản
    "TXN_123456",          // Mã giao dịch
    "VNPay",               // Tên cổng thanh toán
    123                    // ID tài khoản nhận tiền (tùy chọn)
);
```

#### Gửi sang hãng vận chuyển
```php
$response = $client->orders()->sendToCarrier(
    "125123098",           // ID đơn hàng Nhanh.vn
    30000                  // Phí ship báo khách (tùy chọn)
);
```

## 🔍 Validation Rules

### 1. Định danh đơn hàng
- Phải cung cấp ít nhất một trong hai giá trị: `id` hoặc `orderId`
- Hệ thống sẽ ưu tiên thông tin `orderId` trên Nhanh.vn

### 2. Trạng thái (status)
Chỉ chấp nhận các giá trị:
- `Success` - Thành công
- `Confirmed` - Đã xác nhận
- `Canceled` - Khách hủy (chỉ đổi được khi đơn hàng đang ở trạng thái Mới, Đang xác nhận, Đã xác nhận)
- `Aborted` - Hệ thống hủy (chỉ đổi được khi đơn hàng đang ở trạng thái Mới, Đang xác nhận, Đã xác nhận)

### 3. Số tiền
- `moneyTransfer` phải là số không âm
- `customerShipFee` phải là số không âm

### 4. AutoSend
- `autoSend` phải là 0 hoặc 1
- Khi `autoSend = 1`, hệ thống sẽ tự động gửi đơn hàng sang hãng vận chuyển

## 📊 Response Handling

### 1. Success Response
```json
{
    "code": 1,
    "messages": [],
    "data": {
        "orderId": 125123098,
        "status": "Shipping",
        "shipFee": 30000,
        "codFee": 13000,
        "shipFeeDiscount": 0,
        "codFeeDiscount": 0,
        "carrierCode": "GHN123456789"
    }
}
```

### 2. Error Response
```json
{
    "code": 0,
    "messages": [
        "Không tìm thấy thông tin đơn hàng",
        "Trạng thái không hợp lệ"
    ]
}
```

## 🚀 Sử dụng trong thực tế

### 1. Cập nhật khi khách chuyển khoản
```php
// Khi website nhận được callback từ cổng thanh toán
$response = $client->orders()->updatePayment(
    $orderId,
    $amount,
    $transactionCode,
    $gatewayName
);

if ($response->isSuccess()) {
    // Cập nhật trạng thái đơn hàng thành công
    $client->orders()->updateStatus($orderId, "Success");

    // Gửi đơn hàng sang hãng vận chuyển
    $client->orders()->sendToCarrier($orderId);
}
```

### 2. Xử lý hủy đơn hàng
```php
// Khi khách hàng hủy đơn hàng
$response = $client->orders()->updateStatus(
    $orderId,
    "Canceled",
    "Khách hàng yêu cầu hủy đơn hàng",
    "Đơn hàng bị hủy bởi khách hàng"
);
```

### 3. Gửi đơn hàng sang hãng vận chuyển
```php
// Khi xác nhận đơn hàng và sẵn sàng giao
$response = $client->orders()->sendToCarrier($orderId, $shipFee);

if ($response->isSuccess() && $response->hasCarrierCode()) {
    // Lưu mã vận đơn vào database
    $carrierCode = $response->getCarrierCode();
    $order->update(['carrier_code' => $carrierCode]);

    // Gửi thông báo cho khách hàng
    $this->sendShippingNotification($order, $carrierCode);
}
```

## ⚠️ Lưu ý quan trọng

### 1. Giới hạn trạng thái
- Chỉ có thể chuyển sang trạng thái `Canceled` hoặc `Aborted` khi đơn hàng đang ở trạng thái Mới, Đang xác nhận, Đã xác nhận
- Các trạng thái khác có thể thay đổi tự do

### 2. AutoSend
- Khi `autoSend = 1`, hệ thống sẽ tự động gửi đơn hàng sang hãng vận chuyển
- Nếu gửi thành công, response sẽ có `carrierCode`
- Nếu gửi thất bại, response sẽ có thông báo lỗi

### 3. Phí vận chuyển
- `shipFee` và `codFee` chỉ có khi đơn hàng sử dụng dịch vụ vận chuyển
- `shipFeeDiscount` và `codFeeDiscount` là phí được chiết khấu
- Phí thực tế = Phí gốc - Phí giảm giá

### 4. Cache Management
- Sau khi cập nhật thành công, cache sẽ được clear để đảm bảo dữ liệu mới nhất
- Sử dụng `$client->orders()->clearCache()` để clear cache thủ công nếu cần

## 🔧 Debug & Troubleshooting

### 1. Logging
```php
// Enable debug logging
$client->setLogger(new MonologAdapter($logger));

// Logs sẽ hiển thị:
// - Request data
// - API call details
// - Response parsing
// - Error handling
```

### 2. Validation Errors
```php
// Kiểm tra lỗi validation
if (!$request->isValid()) {
    $errors = $request->getErrors();
    foreach ($errors as $field => $message) {
        echo "Field: $field, Error: $message\n";
    }
}
```

### 3. Response Debug
```php
// Debug response data
$summary = $response->getSummary();
echo "Response Summary: " . json_encode($summary, JSON_PRETTY_PRINT);
```

## 📚 Tài liệu tham khảo

- [Order API Documentation](../api/order.md)
- [OrderUpdateRequest Entity](../../src/Entities/Order/OrderUpdateRequest.php)
- [OrderUpdateResponse Entity](../../src/Entities/Order/OrderUpdateResponse.php)
- [OrderModule](../../src/Modules/OrderModule.php)
- [Example Implementation](../../examples/public/update_order.php)
