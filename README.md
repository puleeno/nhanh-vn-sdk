# Nhanh.vn PHP SDK

[![PHP Version](https://img.shields.io/badge/php-7.4%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)]()

PHP SDK chính thức cho Nhanh.vn API - Giải pháp tiêu chuẩn để tích hợp với Nhanh.vn một cách dễ dàng và hiệu quả.

## 🚀 Tính năng

- **API Client thống nhất**: Giao diện nhất quán cho tất cả các API endpoints
- **Xử lý lỗi thông minh**: Xử lý lỗi tự động với thông báo rõ ràng
- **Type Safety**: Hỗ trợ đầy đủ TypeScript types và PHP type hints
- **Rate Limiting**: Tự động xử lý giới hạn tần suất API calls
- **Retry Logic**: Tự động thử lại khi gặp lỗi tạm thời
- **Logging**: Ghi log chi tiết cho debugging
- **Testing**: Unit tests và integration tests đầy đủ

## 📦 Cài đặt

```bash
composer require puleeno/nhanh-vn-sdk
```

## 🔧 Cấu hình

```php
use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;

$config = new ClientConfig([
    'appId' => 'your_app_id_here',
    'businessId' => 'your_business_id_here',
    'accessToken' => 'your_access_token_here',
    'environment' => 'production', // hoặc 'sandbox'
    'timeout' => 30
]);

$client = NhanhVnClient::getInstance($config);
```

## 📚 Ví dụ sử dụng

### Đơn hàng

```php
// Tạo đơn hàng mới
$order = $client->orders()->create([
    'customer_name' => 'Nguyễn Văn A',
    'customer_phone' => '0123456789',
    'customer_address' => '123 Đường ABC, Quận 1, TP.HCM',
    'items' => [
        [
            'product_id' => 'PROD001',
            'quantity' => 2,
            'price' => 150000
        ]
    ]
]);

// Lấy danh sách đơn hàng
$orders = $client->orders()->list([
    'page' => 1,
    'limit' => 20,
    'status' => 'pending'
]);

// Cập nhật trạng thái đơn hàng
$client->orders()->updateStatus('ORDER001', 'shipped');
```

### Sản phẩm

```php
// Tìm kiếm sản phẩm
$products = $client->products()->search([
    'keyword' => 'iPhone',
    'categoryId' => 1,
    'minPrice' => 1000000,
    'maxPrice' => 50000000,
    'page' => 1,
    'perPage' => 20
]);

// Lấy sản phẩm theo danh mục
$categoryProducts = $client->products()->getByCategory(1, [
    'page' => 1,
    'perPage' => 20,
    'sortBy' => 'price',
    'sortOrder' => 'asc'
]);

// Lấy sản phẩm nổi bật
$hotProducts = $client->products()->getHot(10);

// Lấy sản phẩm mới
$newProducts = $client->products()->getNew(10);

// Lấy sản phẩm trang chủ
$homeProducts = $client->products()->getHome(20);

// Lấy thống kê
$stats = $client->products()->getStatistics();

// Quản lý cache
$cacheStatus = $client->products()->getCacheStatus();
$client->products()->clearCache();
```

### Khách hàng

```php
// Tìm kiếm khách hàng
$customers = $client->customers()->search([
    'phone' => '0123456789'
]);

// Tạo khách hàng mới
$customer = $client->customers()->create([
    'name' => 'Nguyễn Văn B',
    'phone' => '0987654321',
    'email' => 'customer@example.com'
]);
```

## 🏗️ Cấu trúc dự án

```
src/
├── Client/           # Client chính (NhanhVnClient)
├── Config/           # Cấu hình client
├── Contracts/        # Interfaces và contracts
├── Entities/         # Data entities (Product, Category, Brand, etc.)
│   └── Product/      # Product-related entities
├── Exceptions/       # Custom exceptions
├── Managers/         # Business logic managers
├── Modules/          # Feature modules (ProductModule)
├── Repositories/     # Data access layer
└── Services/         # Business services

tests/                # Unit tests và integration tests
examples/             # Ví dụ sử dụng
docs/                 # Tài liệu API chi tiết
```

## 🧪 Testing

```bash
# Chạy tất cả tests
composer test

# Chạy tests với coverage
composer test-coverage

# Kiểm tra code style
composer cs-check

# Tự động fix code style
composer cs-fix

# Static analysis
composer stan
```

## 📖 Tài liệu

- [API Reference](docs/api-reference.md)
- [Authentication](docs/authentication.md)
- [Error Handling](docs/error-handling.md)
- [Best Practices](docs/best-practices.md)
- [Migration Guide](docs/migration-guide.md)

## 🤝 Đóng góp

Chúng tôi rất hoan nghênh mọi đóng góp! Vui lòng đọc [CONTRIBUTING.md](CONTRIBUTING.md) để biết thêm chi tiết.

## 📄 License

Dự án này được cấp phép theo [MIT License](LICENSE).

## 🆘 Hỗ trợ

- **Documentation**: [docs.nhanh.vn/sdk](https://docs.nhanh.vn/sdk)
- **Issues**: [GitHub Issues](https://github.com/nhanh-vn/php-sdk/issues)
- **Email**: sdk@nhanh.vn
- **Community**: [Discord](https://discord.gg/nhanh-vn)
