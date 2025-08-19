# Nhanh.vn PHP SDK

[![PHP Version](https://img.shields.io/badge/php-7.4%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)]()

**PHP SDK chính thức cho Nhanh.vn API** - Giải pháp tiêu chuẩn để tích hợp với Nhanh.vn một cách dễ dàng và hiệu quả.

## 🌟 Giới thiệu

Nhanh.vn PHP SDK là thư viện PHP chính thức được phát triển để giúp các nhà phát triển tích hợp dễ dàng với Nhanh.vn API. SDK này cung cấp giao diện đơn giản, an toàn và hiệu quả để tương tác với tất cả các dịch vụ của Nhanh.vn.

## 🚀 Tính năng chính

- **🏗️ Kiến trúc module hóa**: Thiết kế theo nguyên tắc SOLID với các module riêng biệt cho từng chức năng
- **🔐 Xác thực OAuth 2.0**: Hỗ trợ đầy đủ luồng xác thực OAuth với Nhanh.vn
- **📦 Quản lý sản phẩm**: API đầy đủ cho việc quản lý sản phẩm, danh mục, thương hiệu
- **👥 Quản lý khách hàng**: Tìm kiếm và quản lý thông tin khách hàng
- **📋 Quản lý đơn hàng**: Tìm kiếm, lọc, thêm mới, cập nhật và phân tích đơn hàng
- **📊 Cache thông minh**: Hệ thống cache tự động để tối ưu hiệu suất
- **📝 Logging chi tiết**: Ghi log đầy đủ cho việc debug và theo dõi
- **🔄 Xử lý lỗi tự động**: Xử lý và phục hồi lỗi một cách thông minh
- **⚡ Hiệu suất cao**: Tối ưu hóa memory và tốc độ xử lý

## 📦 Cài đặt

### Yêu cầu hệ thống
- PHP 7.4 trở lên
- Composer
- Extension: `curl`, `json`, `mbstring`

### Cài đặt qua Composer

```bash
composer require puleeno/nhanh-vn-sdk
```

## 🔧 Cấu hình

### Khởi tạo cấu hình cơ bản

```php
use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;

// Tạo cấu hình client
$config = new ClientConfig([
    'appId' => 'your_app_id_here',           // ID ứng dụng từ Nhanh.vn
    'businessId' => 'your_business_id_here',  // ID doanh nghiệp
    'accessToken' => 'your_access_token_here', // Token truy cập
    'environment' => 'production',             // Môi trường: 'production' hoặc 'sandbox'
    'timeout' => 30                           // Timeout cho API calls (giây)
]);

// Khởi tạo client singleton
$client = NhanhVnClient::getInstance($config);
```

## 📚 Ví dụ sử dụng

### 🔐 Xác thực OAuth

```php
// Lấy access token từ Nhanh.vn
$oauthModule = $client->oauth();

// Thực hiện OAuth flow
$authUrl = $oauthModule->getAuthorizationUrl([
    'scope' => 'read write',
    'state' => 'random_state_string'
]);

// Xử lý callback và lấy access token
$token = $oauthModule->handleCallback($code);
```

### 📦 Quản lý sản phẩm

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

// Thêm sản phẩm mới
$addResponse = $client->products()->add([
    'id' => 'PROD001',
    'name' => 'iPhone 15 Pro',
    'price' => 25000000,
    'categoryId' => 1,
    'description' => 'Điện thoại thông minh cao cấp'
]);

// Thêm ảnh sản phẩm
$imageResponse = $client->products()->addExternalImage([
    'productId' => 12345,
    'externalImages' => [
        'https://example.com/image1.jpg',
        'https://example.com/image2.jpg'
    ]
]);
```

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

### 👥 Quản lý khách hàng

```php
// Tìm kiếm tất cả khách hàng
$customers = $client->customers()->getAll(1, 20);

// Tìm kiếm khách hàng theo ID
$customer = $client->customers()->searchById(12345);

// Tìm kiếm khách hàng theo số điện thoại
$customer = $client->customers()->searchByMobile('0987654321');

// Lấy khách hàng theo loại (lẻ, sỉ, đại lý)
$retailCustomers = $client->customers()->getRetailCustomers(1, 10);
$wholesaleCustomers = $client->customers()->getWholesaleCustomers(1, 10);

// Lấy khách hàng theo khoảng thời gian cập nhật
$customers = $client->customers()->getByDateRange(
    '2024-01-01 00:00:00',
    '2024-12-31 23:59:59',
    1,
    20
);
```

## 🏗️ Cấu trúc dự án

```
src/
├── Client/           # Client chính (NhanhVnClient)
├── Config/           # Cấu hình client
├── Contracts/        # Interfaces và contracts
├── Entities/         # Data entities (Product, Category, Brand, etc.)
│   └── Product/      # Product-related entities
│   └── Customer/     # Customer-related entities
├── Exceptions/       # Custom exceptions
├── Managers/         # Business logic managers
├── Modules/          # Feature modules (ProductModule, CustomerModule)
├── Repositories/     # Data access layer
└── Services/         # Business services

tests/                # Unit tests và integration tests
examples/             # Ví dụ sử dụng
docs/                 # Tài liệu API chi tiết
```

## 📋 Checklist API Implementation

### 📦 Product Module
- ✅ **Danh sách sản phẩm** - `$client->products()->search()`
- ✅ **Thêm sản phẩm** - `$client->products()->add()`
- ✅ **Chi tiết sản phẩm** - `$client->products()->detail()`
- ✅ **Danh mục sản phẩm** - `$client->products()->getCategories()`
- ✅ **Thêm ảnh sản phẩm** - `$client->products()->addExternalImage()`
- ⏳ **Danh mục nội bộ** - `$client->products()->getInternalCategories()`
- ⏳ **Quà tặng sản phẩm** - `$client->products()->getGifts()`
- ⏳ **Danh sách IMEI** - `$client->products()->getImeis()`
- ⏳ **Tra cứu IMEI bán ra theo ngày** - `$client->products()->getImeiSolds()`
- ⏳ **Lịch sử IMEI** - `$client->products()->getImeiHistories()`
- ⏳ **Hạn sử dụng sản phẩm** - `$client->products()->getExpiries()`

### 👥 Customer Module
- ✅ **Danh sách khách hàng** - `$client->customers()->search()`
- ✅ **Thêm khách hàng** - `$client->customers()->add()`
- ✅ **Thêm nhiều khách hàng** - `$client->customers()->addBatch()`

### 📋 Order Module
- ✅ **Danh sách đơn hàng** - `$client->orders()->search()`
- ✅ **Tìm kiếm theo ID** - `$client->orders()->searchById()`
- ✅ **Tìm kiếm theo khách hàng** - `$client->orders()->searchByCustomerId()`
- ✅ **Tìm kiếm theo số điện thoại** - `$client->orders()->searchByCustomerMobile()`
- ✅ **Lọc theo trạng thái** - `$client->orders()->getByStatuses()`
- ✅ **Lọc theo loại đơn hàng** - `$client->orders()->getByType()`
- ✅ **Lọc theo khoảng thời gian** - `$client->orders()->getByDateRange()`
- ✅ **Lọc theo thời gian giao hàng** - `$client->orders()->getByDeliveryDateRange()`
- ✅ **Lọc theo thời gian cập nhật** - `$client->orders()->getByUpdatedDateTimeRange()`
- ✅ **Thêm đơn hàng mới** - `$client->orders()->add()`
- ✅ **Cập nhật đơn hàng** - `$client->orders()->update()`
- ✅ **Cập nhật trạng thái** - `$client->orders()->updateStatus()`
- ✅ **Cập nhật thanh toán** - `$client->orders()->updatePayment()`
- ✅ **Gửi sang hãng vận chuyển** - `$client->orders()->sendToCarrier()`

### 🔐 OAuth Module
- ✅ **Xác thực OAuth 2.0** - `$client->oauth()->getAuthorizationUrl()`

### 📋 Các Module khác (Chưa implement)
- ⏳ **Shipping Module** - Vận chuyển
- ⏳ **Bill Module** - Xuất nhập kho
- ⏳ **Website Module** - Tin tức, subscriber
- ⏳ **Supplier Module** - Nhà cung cấp
- ⏳ **Promotion Module** - Khuyến mãi
- ⏳ **Store Module** - Kho hàng, nhân viên
- ⏳ **Accounting Module** - Kế toán
- ⏳ **Zalo Module** - Gửi tin ZNS
- ⏳ **Ecommerce Module** - Gian hàng
- ⏳ **Webhooks Module** - Webhook events

**Chú thích:**
- ✅ **Đã implement hoàn chỉnh** - Có đầy đủ Entity, Repository, Service, Manager, Module
- ⏳ **Đang phát triển** - Một phần đã implement hoặc đang trong quá trình phát triển

## 🏛️ Kiến trúc & Thiết kế

### Nguyên tắc thiết kế
- **SOLID Principles**: Tuân thủ đầy đủ 5 nguyên tắc SOLID
- **Dependency Injection**: Sử dụng DI container để quản lý dependencies
- **Repository Pattern**: Tách biệt logic truy cập dữ liệu
- **Service Layer**: Xử lý business logic trong tầng service
- **Manager Pattern**: Điều phối giữa các tầng khác nhau
- **Module Pattern**: Tổ chức code theo chức năng

### Cấu trúc Module
Mỗi module đều tuân theo cấu trúc chuẩn:
```
Module/
├── Entity/           # Data models
├── Repository/       # Data access
├── Service/          # Business logic
├── Manager/          # Orchestration
└── Module/           # Public interface
```

### Memory Management
- **Automatic cleanup**: Tự động giải phóng memory sau mỗi operation
- **Batch processing**: Xử lý dữ liệu theo batch để tối ưu memory
- **Cache strategy**: Sử dụng cache thông minh để giảm API calls

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

## 🗺️ Roadmap & Kế hoạch phát triển

### Q1 2024 - Hoàn thiện Core Modules
- ✅ Product Module (90% hoàn thành)
- ✅ Customer Module (80% hoàn thành)
- ✅ OAuth Module (100% hoàn thành)
- ✅ Order Module (100% hoàn thành)

### Q2 2024 - Business Modules
- 🎯 Shipping Module - Vận chuyển
- 🎯 Bill Module - Xuất nhập kho

### Q3 2024 - Advanced Features
- 🎯 Webhooks Module - Event handling
- 🎯 Promotion Module - Khuyến mãi
- 🎯 Accounting Module - Kế toán

### Q4 2024 - Integration & Optimization
- 🎯 Zalo Module - Gửi tin ZNS
- 🎯 Ecommerce Module - Gian hàng
- 🎯 Performance optimization

## 🆘 Hỗ trợ

- **Documentation**: [docs.nhanh.vn/sdk](https://docs.nhanh.vn/sdk)
- **Issues**: [GitHub Issues](https://github.com/nhanh-vn/php-sdk/issues)
- **Email**: sdk@nhanh.vn
- **Community**: [Discord](https://discord.gg/nhanh-vn)
