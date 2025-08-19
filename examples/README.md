# Nhanh.vn SDK v2.0 - Examples

## Tổng quan

Thư mục này chứa các ví dụ sử dụng Nhanh.vn SDK v2.0, bao gồm OAuth flow hoàn chỉnh để lấy access token.

## Cài đặt

### 1. Cài đặt dependencies

```bash
cd examples
composer install
```

### 2. Cấu hình OAuth

Chỉnh sửa file `auth.json` với thông tin thực của bạn:

```json
{
    "appId": "YOUR_APP_ID",
    "secretKey": "YOUR_SECRET_KEY",
    "redirectUrl": "http://localhost:8000/callback",
    "environment": "sandbox"
}
```

## Chạy ứng dụng

### 1. Khởi động server

```bash
# Sử dụng composer script
composer serve

# Hoặc sử dụng PHP built-in server trực tiếp
php -S localhost:8000 -t public
```

### 2. Truy cập ứng dụng

Mở browser và truy cập: `http://localhost:8000`

## Luồng hoạt động

### Bước 1: Khởi tạo
- Ứng dụng hiển thị thông tin server và cấu hình
- Hiển thị link OAuth để user click

### Bước 2: OAuth Authorization
- User click vào link OAuth
- Browser mở trang đăng nhập Nhanh.vn
- User đăng nhập và authorize ứng dụng

### Bước 3: Callback
- Nhanh.vn redirect về callback URL với access_code
- Ứng dụng đổi access_code lấy access_token
- Lưu token vào file `tokens.json`

### Bước 4: Sử dụng SDK
- Access token được lưu và có thể sử dụng cho các API call tiếp theo

## Cấu trúc file

```
examples/
├── auth.json              # Cấu hình OAuth
├── composer.json          # Dependencies
├── README.md             # Hướng dẫn này
├── public/               # Web root
│   ├── index.php        # Trang chính
│   ├── callback.php     # OAuth callback handler
│   ├── get_products.php # Product search demo
│   ├── get_product_detail.php # Product detail demo
│   ├── get_categories.php # Categories demo
│   ├── get_products_with_logger.php # Logging demo
│   ├── add_product.php  # Product add demo
│   ├── add_product_images.php # Product external images demo
│   ├── search_customers.php # Customer search demo
│   └── add_customer.php # Customer add demo
├── src/                 # Source code
│   └── OAuthExample.php # Class chính xử lý OAuth
└── tokens.json          # File lưu access token (tự động tạo)
```

## Tính năng

- ✅ Hiển thị thông tin server chi tiết
- ✅ Tạo OAuth URL tự động
- ✅ Xử lý callback từ Nhanh.vn
- ✅ Đổi access_code lấy access_token
- ✅ Lưu token vào file JSON
- ✅ Validation cấu hình
- ✅ Error handling
- ✅ Console output đẹp với emoji
- ✅ Tích hợp với Nhanh.vn SDK

## Troubleshooting

### Lỗi "File cấu hình không tồn tại"
- Kiểm tra file `auth.json` có tồn tại không
- Đảm bảo đang chạy từ thư mục `examples`

### Lỗi "Thiếu trường bắt buộc"
- Kiểm tra tất cả fields trong `auth.json`
- Đảm bảo không có field nào để trống

### Lỗi CURL
- Kiểm tra extension curl có được enable không
- Kiểm tra kết nối internet

### Lỗi OAuth
- Kiểm tra `appId` và `businessId` có đúng không
- Đảm bảo `redirectUrl` khớp với cấu hình trong Nhanh.vn

## Sử dụng trong code

```php
require_once 'vendor/autoload.php';

use Examples\OAuthExample;

$app = new OAuthExample();

// Khởi tạo SDK client với token đã lưu
$client = $app->initializeClient();

if ($client) {
    // Sử dụng SDK
    $products = $client->products()->search('iPhone');
    echo "Tìm thấy " . $products->count() . " sản phẩm\n";
} else {
    echo "Chưa có access token, vui lòng authorize trước\n";
}
```

## Ghi chú

- Access token được lưu trong file `tokens.json`
- Token có hạn 30 ngày (có thể thay đổi theo Nhanh.vn)
- Ứng dụng sử dụng PHP built-in server để demo
- Tất cả output hiển thị trên console
- Có thể mở rộng để thêm web interface
