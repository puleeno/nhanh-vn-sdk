# Nhanh.vn SDK v0.4.0

Một SDK PHP toàn diện để tích hợp với Nhanh.vn API, tuân theo các nguyên tắc SOLID và các mẫu thiết kế hiện đại. Phiên bản này giới thiệu Quản lý Đơn hàng hoàn chỉnh và API Vị trí Vận chuyển mới.

## 📸 Ví dụ & Demo

![Nhanh.vn SDK Examples](images/examples.png)

**Bộ sưu tập Ví dụ Toàn diện** - SDK cung cấp một bộ ví dụ hoạt động hoàn chỉnh thể hiện tất cả các tính năng chính và tích hợp API. Mỗi ví dụ được thiết kế để sẵn sàng cho production và tuân theo các thực hành tốt nhất về xử lý lỗi, xác thực và tối ưu hóa hiệu suất.

### 🎯 Những gì bạn sẽ tìm thấy trong Examples

- **🔐 Ví dụ Xác thực** - OAuth flow, quản lý token và truy cập API an toàn
- **📦 Quản lý Sản phẩm** - Thao tác CRUD, quản lý danh mục, xử lý hình ảnh và thao tác hàng loạt
- **👥 Thao tác Khách hàng** - Tìm kiếm, thêm, cập nhật và quản lý khách hàng hàng loạt
- **📋 Xử lý Đơn hàng** - Vòng đời đơn hàng hoàn chỉnh từ tạo đến hoàn thành
- **🚚 Vận chuyển & Hậu cần** - Dịch vụ vị trí, quản lý đơn vị vận chuyển và tính toán vận chuyển
- **⚡ Tính năng Nâng cao** - Mẫu Client builder, chiến lược cache và tối ưu hóa hiệu suất

### 🚀 Bắt đầu với Examples

Tất cả các ví dụ được đặt trong thư mục `examples/public/` và có thể chạy trực tiếp trong trình duyệt của bạn. Mỗi ví dụ bao gồm:

- **Mã hoạt động hoàn chỉnh** với xử lý lỗi phù hợp
- **Hỗ trợ ngôn ngữ tiếng Việt** cho giao diện người dùng
- **Thiết kế responsive** hoạt động trên tất cả các thiết bị
- **Bình luận chi tiết** giải thích từng bước
- **Thực hành tốt nhất** cho triển khai production

### 📱 Giao diện Demo Tương tác

Các ví dụ bao gồm giao diện web hiện đại, responsive thể hiện:

- **Điều hướng dạng lưới** để dễ dàng truy cập tất cả các ví dụ
- **Tổ chức theo danh mục** theo chức năng (Sản phẩm, Đơn hàng, Khách hàng, Vận chuyển)
- **Phản hồi trực quan** với hiệu ứng hover và animation
- **Thiết kế thân thiện với mobile** để kiểm tra trên bất kỳ thiết bị nào
- **Liên kết trực tiếp** đến tài liệu và mã nguồn liên quan

### 🔧 Điểm nổi bật Kỹ thuật

Examples thể hiện các tính năng SDK nâng cao:

- **Quản lý Bộ nhớ** - Dọn dẹp và tối ưu hóa tự động
- **Chiến lược Cache** - Triển khai cache thông minh cho dữ liệu vị trí
- **Xử lý Lỗi** - Quản lý exception toàn diện
- **Xác thực** - Xác thực đầu vào với thông báo lỗi tiếng Việt
- **Ghi log** - Tích hợp với Monolog để debug
- **Hiệu suất** - Tối ưu hóa API calls và xử lý response

## 🚀 Phiên bản Nhanh.vn API được Hỗ trợ

### ✅ Hiện tại được Hỗ trợ
- **Nhanh.vn API v2.0** - Hỗ trợ đầy đủ với tất cả các module đã triển khai

### 🔄 Sắp tới
- **Nhanh.vn API v3.0** - Phiên bản beta, dự kiến cho SDK v1.0.0
  - Hiệu suất nâng cao và tính năng mới
  - Tương thích ngược với v2.0
  - Module mới: Analytics, Báo cáo Nâng cao, Webhooks

## 📋 Trạng thái Triển khai API (v2.0)

### ✅ Core Modules (100% Hoàn thành)
- **Products Module** - Thao tác CRUD hoàn chỉnh, danh mục, thương hiệu, hình ảnh
- **Orders Module** - Quản lý đơn hàng hoàn chỉnh, tìm kiếm, cập nhật, quản lý trạng thái
- **Customers Module** - Tìm kiếm khách hàng hoàn chỉnh, thêm, thao tác hàng loạt
- **Shipping Module** - Hệ thống vị trí hoàn chỉnh (Tỉnh/Thành, Quận/Huyện, Phường/Xã), đơn vị vận chuyển

### 🔄 Module Ưu tiên Tiếp theo
- **Bill Module** - Quản lý kho, thao tác tồn kho
- **Store Module** - Kho hàng, nhân viên, quản lý chi nhánh
- **Statistics Module** - Báo cáo và phân tích

## Tính năng

- **Tích hợp OAuth 2.0** - Xác thực an toàn với Nhanh.vn
- **API Quản lý Sản phẩm** - Thao tác CRUD sản phẩm hoàn chỉnh
- **API Hình ảnh Sản phẩm Bên ngoài** - Quản lý hình ảnh sản phẩm từ CDN bên ngoài
- **Thao tác Hình ảnh Hàng loạt** - Xử lý nhiều hình ảnh sản phẩm hiệu quả
- **API Quản lý Khách hàng** - Tìm kiếm và quản lý dữ liệu khách hàng
- **API Quản lý Đơn hàng** - Thao tác CRUD đơn hàng hoàn chỉnh và quản lý trạng thái
- **API Vị trí Vận chuyển** - Hệ thống vị trí 3 cấp (Tỉnh/Thành, Quận/Huyện, Phường/Xã)
- **Kiến trúc Module** - Tách biệt rõ ràng các mối quan tâm
- **Xác thực Toàn diện** - Xác thực đầu vào và xử lý lỗi với thông báo tiếng Việt
- **Tích hợp Ghi log** - Ghi log tích hợp với hỗ trợ Monolog
- **Xử lý Exception** - Exception tùy chỉnh cho các loại lỗi khác nhau
- **Mẫu DTO** - Data Transfer Objects để xử lý request/response
- **Cache Thông minh** - Hệ thống cache thông minh với tối ưu hóa TTL
- **Quản lý Bộ nhớ** - Dọn dẹp và tối ưu hóa tự động

## 🚀 Điều hướng Nhanh

- **[Examples Dashboard](examples/public/index.php)** - Giao diện ví dụ tương tác
- **[Tham chiếu API](v2/README.md)** - Tài liệu API hoàn chỉnh
- **[Bắt đầu Nhanh](#quick-start)** - Chạy trong vài phút
- **[Kiến trúc](#architecture)** - Hiểu cấu trúc SDK

## Tài liệu API
- [Tài liệu Product API](v2/product/README.md) - Thao tác sản phẩm hoàn chỉnh
- [Hình ảnh Sản phẩm Bên ngoài](v2/product/README.md#thêm-ảnh-sản-phẩm) - Quản lý hình ảnh

### 👥 Quản lý Khách hàng
- [Tài liệu Customer API](v2/customer/README.md) - Tìm kiếm và quản lý khách hàng
- [Chi tiết Customer API](v2/customer/customer.md) - Tham chiếu API chi tiết

### 📋 Quản lý Đơn hàng
- [Tài liệu Order API](v2/order/README.md) - Thao tác đơn hàng hoàn chỉnh
- [Order Update API](v2/order/order-update.md) - Triển khai cập nhật đơn hàng
- [Chi tiết Order API](v2/order/order.md) - Tham chiếu API chi tiết

### 🚚 Vận chuyển & Vị trí
- [Tài liệu Shipping API](v2/shipping/shipping.md) - Quản lý vị trí và thông tin đơn vị vận chuyển

### 🔐 Xác thực & Kiến trúc
- [Tài liệu OAuth](v2/api/oauth.md) - Flow xác thực và quản lý token
- [Sơ đồ Flow](v2/flow-diagram.md) - Kiến trúc hệ thống và luồng dữ liệu

## Bắt đầu Nhanh

### Cài đặt

```bash
composer require puleeno/nhanh-vn-sdk
```

### Sử dụng Cơ bản

```php
use Puleeno\NhanhVn\Client\NhanhVnClient;

// Khởi tạo client
$client = NhanhVnClient::getInstance();

// Sử dụng product module
$products = $client->product()->getAll();

// Sử dụng customer module
$customers = $client->customer()->search(['type' => 1]);
```

## Kiến trúc

SDK tuân theo mẫu kiến trúc phân lớp:

- **Client Layer** - Điểm vào chính và cấu hình
- **Module Layer** - Thao tác nghiệp vụ cấp cao
- **Manager Layer** - Điều phối logic nghiệp vụ
- **Service Layer** - Triển khai logic nghiệp vụ cốt lõi
- **Repository Layer** - Truy cập dữ liệu và tạo entity
- **Entity Layer** - Mô hình dữ liệu và xác thực

## Examples

### 🎯 Giao diện Examples Tương tác

Trải nghiệm khả năng SDK thông qua bộ sưu tập examples toàn diện của chúng tôi:

- **[Examples Dashboard](examples/public/index.php)** - Giao diện dạng lưới hiện đại thể hiện tất cả tính năng
- **[Live Demo](examples/public/)** - Chạy examples trực tiếp trong trình duyệt

### 📚 Examples Cá nhân

#### 🔐 Xác thực & Thiết lập
- [OAuth Flow](examples/public/oauth.php) - Quy trình xác thực OAuth hoàn chỉnh
- [Test Boot File](examples/public/test_boot.php) - Kiểm tra khởi tạo client
- [OAuth Callback](examples/public/callback.php) - Xử lý callback OAuth

#### 📦 Quản lý Sản phẩm
- [Product CRUD](examples/public/get_products.php) - Liệt kê, tìm kiếm và quản lý sản phẩm
- [Product với Logger](examples/public/get_products_with_logger.php) - Tích hợp ghi log nâng cao
- [Categories](examples/public/get_categories.php) - Quản lý danh mục sản phẩm
- [Product Details](examples/public/get_product_detail.php) - Thông tin sản phẩm chi tiết
- [Add Products](examples/public/add_product.php) - Tạo sản phẩm mới
- [Product Images](examples/public/add_product_images.php) - Quản lý hình ảnh bên ngoài

#### 👥 Thao tác Khách hàng
- [Customer Search](examples/public/search_customers.php) - Tìm kiếm khách hàng nâng cao và lọc
- [Add Customers](examples/public/add_customer.php) - Tạo và thao tác khách hàng hàng loạt

#### 📋 Quản lý Đơn hàng
- [Order Search](examples/public/get_orders.php) - Tìm kiếm, lọc và phân trang đơn hàng
- [Add Orders](examples/public/add_order.php) - Tạo đơn hàng mới với tùy chọn vận chuyển
- [Update Orders](examples/public/update_order.php) - Cập nhật trạng thái, thanh toán và vận chuyển

#### 🚚 Vận chuyển & Vị trí
- [Location Services](examples/public/get_locations.php) - Quản lý Tỉnh/Thành, Quận/Huyện, Phường/Xã
- [Shipping Carriers](examples/public/get_shipping_carriers.php) - Thông tin đơn vị vận chuyển và dịch vụ
- [Shipping Calculator](examples/public/calculate_shipping_fee.php) - Tính toán chi phí vận chuyển

#### ⚡ Tính năng Nâng cao
- [Client Builder](examples/public/client_builder_demo.php) - Mẫu tạo client hiện đại
- [Legacy Orders](examples/public/orders.php) - Ví dụ API đơn hàng legacy

### 🚀 Bắt đầu

1. **Clone repository** và điều hướng đến thư mục examples
2. **Cấu hình thông tin đăng nhập** trong `examples/boot/client.php`
3. **Bắt đầu với OAuth** - Chạy `examples/public/oauth.php` trước
4. **Khám phá tính năng** - Sử dụng examples dashboard để điều hướng dễ dàng
5. **Tùy chỉnh mã** - Mỗi example đều sẵn sàng cho production và có bình luận tốt

## Tương thích Phiên bản

| Phiên bản SDK | Nhanh.vn API | Phiên bản PHP | Trạng thái |
|---------------|---------------|---------------|------------|
| v0.4.0 | v2.0 | 8.1+ | ✅ Ổn định |
| v0.3.x | v2.0 | 8.0+ | ⚠️ Đã lỗi thời |
| v0.2.x | v1.0 | 7.4+ | ❌ EOL |
| v0.1.x | v1.0 | 7.4+ | ❌ EOL |

## Lộ trình Di chuyển

### Từ v0.3.x đến v0.4.0
- Yêu cầu PHP 8.1+
- Thay đổi namespace: `NhanhVn\Sdk` → `Puleeno\NhanhVn`
- API Order Update và Shipping Location mới
- Xác thực và xử lý lỗi nâng cao

### Tương lai: v0.4.0 đến v1.0.0
- Hỗ trợ Nhanh.vn API v3.0
- Hiệu suất nâng cao và tính năng mới
- Duy trì tương thích ngược
- Module analytics và báo cáo mới

## Đóng góp

Vui lòng đọc [CONTRIBUTING.md](CONTRIBUTING.md) để biết chi tiết về quy tắc ứng xử và quy trình gửi pull request.

## Giấy phép

Dự án này được cấp phép theo MIT License - xem file [LICENSE](LICENSE) để biết chi tiết.
