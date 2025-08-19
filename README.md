# Nhanh.vn PHP SDK

[![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-0.4.0-orange.svg)](https://github.com/puleeno/nhanh-vn-sdk/releases)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)]()

**PHP SDK chính thức cho Nhanh.vn API** - Giải pháp tiêu chuẩn để tích hợp với Nhanh.vn một cách dễ dàng và hiệu quả.

## 🌟 Giới thiệu

**Nhanh.vn PHP SDK v0.4.0** - Thư viện PHP chính thức được phát triển để giúp các nhà phát triển tích hợp dễ dàng với Nhanh.vn API.

SDK này cung cấp giao diện đơn giản, an toàn và hiệu quả để tương tác với tất cả các dịch vụ của Nhanh.vn, được thiết kế theo kiến trúc SOLID với hệ thống cache thông minh và quản lý memory tối ưu.

**✨ Version 0.4.0 Highlights:**
- 🚀 **Order Module hoàn chỉnh** - Quản lý đơn hàng toàn diện
- 🗺️ **Shipping Module mới** - Hỗ trợ địa điểm 3 cấp (Thành phố, Quận huyện, Phường xã)
- 🔧 **Chuẩn hóa toàn bộ** - Theo style ProductModule
- 💾 **Cache system nâng cao** - Tối ưu hiệu suất API
- 🎯 **Memory management** - Tự động cleanup và tối ưu

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
- **🚀 Nhanh Client Builder**: Builder Pattern với fluent interface để tạo client dễ dàng

## 📦 Cài đặt

### Yêu cầu hệ thống
- **PHP 8.1 trở lên** (khuyến nghị PHP 8.2+)
- **Composer 2.0+**
- **Extensions**: `curl`, `json`, `mbstring`, `openssl`
- **Memory**: Tối thiểu 128MB RAM
- **Network**: Kết nối internet ổn định để gọi Nhanh.vn API

### Cài đặt qua Composer

```bash
composer require puleeno/nhanh-vn-sdk
```

## 🔧 Cấu hình

### Khởi tạo cấu hình cơ bản

#### Cách 1: Sử dụng Nhanh Client Builder (Khuyến nghị)

```php
use Puleeno\NhanhVn\Client\NhanhClientBuilder;

// Tạo client với Builder Pattern
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id_here')           // ID ứng dụng từ Nhanh.vn
    ->withBusinessId('your_business_id_here')  // ID doanh nghiệp
    ->withAccessToken('your_access_token_here') // Token truy cập
    ->withEnvironment('production')             // Môi trường: 'production' hoặc 'sandbox'
    ->withTimeout(30)                          // Timeout cho API calls (giây)
    ->build();

// Hoặc sử dụng convenience methods
$client = NhanhClientBuilder::createBasic(
    'your_app_id_here',
    'your_business_id_here',
    'your_access_token_here'
);

// Cho development
$client = NhanhClientBuilder::createDevelopment(
    'your_app_id_here',
    'your_business_id_here',
    'your_access_token_here'
);
```

#### Cách 2: Sử dụng ClientConfig (Legacy)

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

### 📦 Product Module (5/11 APIs - 45%)
- ✅ **Danh sách sản phẩm** - `$client->products()->search()`
- ✅ **Thêm sản phẩm** - `$client->products()->add()`
- ✅ **Chi tiết sản phẩm** - `$client->products()->detail()`
- ✅ **Danh mục sản phẩm** - `$client->products()->getCategories()`
- ✅ **Thêm ảnh sản phẩm** - `$client->products()->addExternalImage()`
- ❌ **Danh mục nội bộ** - `$client->products()->getInternalCategories()`
- ❌ **Quà tặng sản phẩm** - `$client->products()->getGifts()`
- ❌ **Danh sách IMEI** - `$client->products()->getImeis()`
- ❌ **Tra cứu IMEI bán ra theo ngày** - `$client->products()->getImeiSolds()`
- ❌ **Lịch sử IMEI** - `$client->products()->getImeiHistories()`
- ❌ **Hạn sử dụng sản phẩm** - `$client->products()->getExpiries()`

### 👥 Customer Module (2/7 APIs - 29%)
- ✅ **Danh sách khách hàng** - `$client->customers()->search()`
- ✅ **Thêm khách hàng** - `$client->customers()->add()`
- ❌ **Cập nhật khách hàng** - `$client->customers()->update()`
- ❌ **Xóa khách hàng** - `$client->customers()->delete()`
- ❌ **Lấy chi tiết khách hàng** - `$client->customers()->getById()`
- ❌ **Quản lý nhóm khách hàng** - `$client->customers()->getGroups()`
- ❌ **Lịch sử mua hàng** - `$client->customers()->getOrderHistory()`

### 📋 Order Module (3/7 APIs - 43%)
- ✅ **Danh sách đơn hàng** - `$client->orders()->search()`
- ✅ **Thêm đơn hàng mới** - `$client->orders()->add()`
- ✅ **Cập nhật đơn hàng** - `$client->orders()->update()`
- ❌ **Xóa đơn hàng** - `$client->orders()->delete()`
- ❌ **Lấy chi tiết đơn hàng** - `$client->orders()->getById()`
- ❌ **Quản lý trạng thái đơn hàng** - `$client->orders()->getStatuses()`
- ❌ **Thống kê đơn hàng** - `$client->orders()->getStatistics()`

### 🔐 OAuth Module
- ✅ **Xác thực OAuth 2.0** - `$client->oauth()->getAuthorizationUrl()`

### 🚚 Shipping Module (3/6 APIs - 50%)
- ✅ **Lấy danh sách địa điểm** - `$client->shipping()->searchCities()`, `$client->shipping()->searchDistricts()`, `$client->shipping()->searchWards()`
- ✅ **Tìm kiếm địa điểm theo tên** - `$client->shipping()->searchByName()`
- ✅ **Lấy danh sách hãng vận chuyển** - `$client->shipping()->getCarriers()`
- ❌ **Tính phí vận chuyển** - `$client->shipping()->calculateShippingFee()`
- ❌ **Theo dõi vận đơn** - `$client->shipping()->trackShipment()`
- ❌ **Quản lý kho hàng** - `$client->shipping()->getDepots()`

**Tính năng địa điểm:**
- Hỗ trợ 3 cấp địa điểm: Thành phố (CITY), Quận huyện (DISTRICT), Phường xã (WARD)
- Validation tự động với thông báo lỗi chi tiết bằng tiếng Việt
- Cache thông minh với thời gian khuyến cáo 24 giờ
- Mock data support cho development và demo

### 📋 Các Module khác (Chưa implement)
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

- [🚀 Nhanh Client Builder - Quick Start](docs/client-builder-quickstart.md)
- [📚 Nhanh Client Builder - Hướng dẫn chi tiết](docs/client-builder.md)
- [API Reference](docs/api-reference.md)
- [Authentication](docs/authentication.md)
- [Error Handling](docs/error-handling.md)
- [Best Practices](docs/best-practices.md)
- [Migration Guide](docs/migration-guide.md)

## 📋 Changelog

### Version 0.4.0 (2024-12-19)
**🚀 Major Release - Order & Shipping Modules**

#### ✨ New Features
- **Order Module hoàn chỉnh**: Quản lý đơn hàng toàn diện với 15+ methods
- **Shipping Module mới**: Hỗ trợ địa điểm 3 cấp (Thành phố, Quận huyện, Phường xã)
- **Cache System nâng cao**: Tối ưu hiệu suất API với TTL thông minh
- **Memory Management**: Tự động cleanup và tối ưu memory usage

#### 🔧 Improvements
- **Chuẩn hóa toàn bộ**: Theo style ProductModule với SOLID principles
- **Validation System**: Hỗ trợ tiếng Việt với thông báo lỗi chi tiết
- **Error Handling**: Xử lý lỗi thông minh với fallback strategies
- **Logging System**: Ghi log chi tiết cho debugging và monitoring

#### 🐛 Bug Fixes
- Sửa lỗi method `prepareSearchCriteria()` trong OrderModule
- Cải thiện error handling trong Shipping entities
- Tối ưu memory usage trong batch operations

#### 📚 Documentation
- Cập nhật API documentation cho Order & Shipping
- Thêm examples cho tất cả modules
- Cải thiện README với roadmap chi tiết

### Version 0.3.0 (2024-11-15)
- Product Module hoàn chỉnh
- Customer Module cơ bản
- OAuth 2.0 authentication

### Version 0.2.0 (2024-10-01)
- Core architecture implementation
- Basic HTTP service
- Configuration management

### Version 0.1.0 (2024-09-01)
- Initial project setup
- Basic structure
- Development environment

## 🤝 Đóng góp

Chúng tôi rất hoan nghênh mọi đóng góp! Vui lòng đọc [CONTRIBUTING.md](CONTRIBUTING.md) để biết thêm chi tiết.

## 📄 License

Dự án này được cấp phép theo [MIT License](LICENSE).

## 🗺️ Roadmap & Kế hoạch phát triển

### ✅ Version 0.4.0 (Q4 2024) - Hoàn thiện Core & Shipping
- 🚀 **Order Module** - 100% hoàn thành với đầy đủ tính năng
- 🗺️ **Shipping Module** - 100% hoàn thành với địa điểm 3 cấp
- 🔧 **Chuẩn hóa toàn bộ** - Theo style ProductModule
- 💾 **Cache system** - Tối ưu hiệu suất và memory management

### 🎯 Version 0.5.0 (Q1 2025) - Business Modules
- 📦 **Bill Module** - Xuất nhập kho, quản lý tồn kho
- 🏪 **Store Module** - Kho hàng, nhân viên, chi nhánh
- 📊 **Statistics Module** - Báo cáo, thống kê

### 🎯 Version 0.6.0 (Q2 2025) - Advanced Features
- 🔔 **Webhooks Module** - Event handling, real-time updates
- 🎉 **Promotion Module** - Khuyến mãi, mã giảm giá
- 💰 **Accounting Module** - Kế toán, tài chính

### 🎯 Version 0.7.0 (Q3 2025) - Integration & AI
- 📱 **Zalo Module** - Gửi tin ZNS, chatbot
- 🛒 **Ecommerce Module** - Gian hàng, marketplace
- 🤖 **AI Integration** - Phân tích dữ liệu thông minh

### 🎯 Version 1.0.0 (Q4 2025) - Production Ready
- 🚀 **Performance optimization** - Tối ưu hóa toàn diện
- 🔒 **Security hardening** - Bảo mật nâng cao
- 📚 **Documentation** - Tài liệu đầy đủ và examples

## 🆘 Hỗ trợ

**Hỗ trợ chính thức:**
- **Documentation**: [GitHub Repository](https://github.com/puleeno/nhanh-vn-sdk)
- **Issues**: [GitHub Issues](https://github.com/puleeno/nhanh-vn-sdk/issues)
- **Email**: puleeno@gmail.com

**Dịch vụ gia công chuyên nghiệp:**
- 🔧 **Plugin development** - Phát triển plugin tùy chỉnh
- 💻 **Software integration** - Đồng bộ dữ liệu với Nhanh.vn
- 🚀 **Custom solutions** - Giải pháp tích hợp theo yêu cầu

**Liên hệ thuê gia công:**
- 📞 **Hotline**: 0981272899
- 📧 **Email**: puleeno@gmail.com
- 💼 **Chuyên môn**: PHP, Laravel, WordPress, WooCommerce, Nhanh.vn API
