# Changelog

Tất cả các thay đổi quan trọng trong dự án này sẽ được ghi lại trong file này.

Format dựa trên [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
và dự án này tuân thủ [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.4.0] - 2024-12-19

### 🚀 Added
- **Nhanh Client Builder - Tính năng mới hoàn chỉnh**:
  - Builder Pattern với Fluent Interface cho syntax gọn gàng, trực quan
  - Static Convenience Methods: `createBasic()`, `createDevelopment()`, `createProduction()`
  - Environment Presets: `forDevelopment()`, `forProduction()`, `forTesting()`
  - OAuth Support đầy đủ với `fromOAuth()`, `withSecretKey()`, `withRedirectUrl()`
  - Smart Validation tự động phân biệt OAuth flow và API calls
  - Null Value Handling an toàn cho tất cả optional parameters

### 🧪 Testing
- **Tất cả các test đã pass**:
  - ✅ createBasic(), createDevelopment(), createProduction()
  - ✅ createOAuth(), Builder với null secretKey
  - ✅ fromEnvironment(), fromConfigFile()
  - ✅ Tất cả validation và error handling

### 🎯 Features Working Perfectly
- Builder Pattern với fluent interface
- Static Convenience Methods
- Environment Presets (Development/Production/Testing)
- OAuth Support
- Null Value Handling
- Smart Validation

### 📚 Documentation
- `docs/client-builder.md` - Tài liệu chi tiết
- `docs/client-builder-quickstart.md` - Hướng dẫn nhanh
- `examples/public/client_builder_demo.php` - Demo page tương tác
- Cập nhật README.md với hướng dẫn Builder

### 🔄 Migration
- **Không có breaking changes** - Tương thích ngược 100%
- Tự động fix các lỗi
- Khuyến nghị sử dụng Nhanh Client Builder thay vì ClientConfig trực tiếp

## [0.4.0] - 2024-12-19

### 🚀 Added
- **Order Module hoàn chỉnh** với 15+ methods:
  - `search()` - Tìm kiếm đơn hàng theo criteria
  - `searchById()` - Tìm kiếm theo ID
  - `searchByCustomerId()` - Tìm kiếm theo khách hàng
  - `searchByCustomerMobile()` - Tìm kiếm theo số điện thoại
  - `getByStatuses()` - Lọc theo trạng thái
  - `getByType()` - Lọc theo loại đơn hàng
  - `getByDateRange()` - Lọc theo khoảng thời gian
  - `getByDeliveryDateRange()` - Lọc theo thời gian giao hàng
  - `getByUpdatedDateTimeRange()` - Lọc theo thời gian cập nhật
  - `add()` - Thêm đơn hàng mới
  - `update()` - Cập nhật đơn hàng
  - `updateStatus()` - Cập nhật trạng thái
  - `updatePayment()` - Cập nhật thanh toán
  - `sendToCarrier()` - Gửi sang hãng vận chuyển
  - Cache management và memory optimization

- **Shipping Module mới** với địa điểm 3 cấp:
  - `searchCities()` - Lấy danh sách thành phố
  - `searchDistricts()` - Lấy danh sách quận huyện theo thành phố
  - `searchWards()` - Lấy danh sách phường xã theo quận huyện
  - `searchByName()` - Tìm kiếm địa điểm theo tên
  - `findById()` - Tìm kiếm địa điểm theo ID
  - `getCarriers()` - Lấy danh sách hãng vận chuyển
  - Validation tự động với thông báo tiếng Việt
  - Cache thông minh với TTL 24 giờ

- **Cache System nâng cao**:
  - TTL thông minh cho từng loại dữ liệu
  - Memory management tự động
  - Cache invalidation strategies
  - Performance monitoring

### 🔧 Changed
- **Chuẩn hóa toàn bộ modules** theo style ProductModule
- **Cải thiện architecture** với SOLID principles
- **Tối ưu memory usage** với automatic cleanup
- **Cải thiện error handling** với fallback strategies
- **Logging system** chi tiết cho debugging

### 🐛 Fixed
- Sửa lỗi method `prepareSearchCriteria()` trong OrderModule
- Cải thiện error handling trong Shipping entities
- Tối ưu memory usage trong batch operations
- Sửa lỗi namespace inconsistencies

### 📚 Documentation
- Cập nhật API documentation cho Order & Shipping
- Thêm examples cho tất cả modules
- Cải thiện README với roadmap chi tiết
- Thêm changelog và versioning

## [0.3.0] - 2024-11-15

### 🚀 Added
- **Product Module hoàn chỉnh**:
  - CRUD operations cho sản phẩm
  - Quản lý danh mục và thương hiệu
  - Image management
  - Search và filtering
  - Cache system

- **Customer Module cơ bản**:
  - Tìm kiếm khách hàng
  - Thêm khách hàng mới
  - Batch operations

- **OAuth 2.0 Authentication**:
  - Authorization flow
  - Token management
  - Refresh token support

### 🔧 Changed
- Cải thiện HTTP service
- Tối ưu error handling
- Cải thiện logging system

## [0.2.0] - 2024-10-01

### 🚀 Added
- **Core Architecture**:
  - Client configuration
  - HTTP service implementation
  - Basic entity system
  - Repository pattern

- **Configuration Management**:
  - Environment-based config
  - API endpoint management
  - Timeout settings

### 🔧 Changed
- Cải thiện project structure
- Tối ưu autoloading
- Cải thiện error handling

## [0.1.0] - 2024-09-01

### 🚀 Added
- **Initial Project Setup**:
  - Basic project structure
  - Composer configuration
  - Development environment
  - Basic documentation

---

## 📋 Version Compatibility

| Version | PHP | Composer | Status |
|---------|-----|----------|---------|
| 0.4.0   | 8.1+ | 2.0+ | ✅ Latest (Stable) |
| 0.3.x   | 8.0+ | 2.0+ | ⚠️ Deprecated |
| 0.2.x   | 7.4+ | 1.0+ | ❌ EOL |
| 0.1.x   | 7.4+ | 1.0+ | ❌ EOL |

## 🔄 Migration Guide

### Từ 0.3.x lên 0.4.0
- **Breaking Changes**: Cập nhật PHP lên 8.1+ và Composer lên 2.0+
- **Tính năng mới**: Sử dụng Nhanh Client Builder thay vì ClientConfig trực tiếp
- **Khuyến nghị**: Migration từ ClientConfig sang Builder pattern để có syntax gọn gàng hơn

### Từ 0.3.x lên 0.4.x
- Cập nhật PHP lên 8.1+
- Cập nhật Composer lên 2.0+
- Kiểm tra namespace changes
- Cập nhật method calls theo API mới

### Từ 0.2.x lên 0.4.x
- Major breaking changes
- Cần refactor toàn bộ code
- Tham khảo migration guide chi tiết

## 📞 Support

- **Documentation**: [GitHub Repository](https://github.com/puleeno/nhanh-vn-sdk)
- **Issues**: [GitHub Issues](https://github.com/puleeno/nhanh-vn-sdk/issues)
- **Email**: puleeno@gmail.com
- **Hotline**: 0981272899
