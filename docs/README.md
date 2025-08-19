# Nhanh.vn SDK v2.0

PHP SDK tiêu chuẩn để tích hợp với Nhanh.vn API, cung cấp interface dễ sử dụng và đầy đủ tính năng.

## 📋 Thông tin cơ bản

- **Package**: `puleeno/nhanh-vn-sdk`
- **Author**: Puleeno Nguyen (puleeno@gmail.com)
- **Version**: 2.0
- **PHP Requirement**: >= 8.1
- **License**: MIT

## 🚀 Tính năng chính

### ✅ Đã hoàn thành
- **OAuth Flow**: Xác thực và lấy access token từ Nhanh.vn
- **Product Search**: Tìm kiếm sản phẩm với nhiều tiêu chí
- **Product Detail**: Lấy chi tiết sản phẩm theo ID
- **Product Categories**: Lấy danh mục sản phẩm
- **Product Add API**: Thêm/cập nhật sản phẩm với validation toàn diện
- **Product External Images API**: Thêm ảnh sản phẩm từ CDN bên ngoài
- **Batch Operations**: Hỗ trợ thêm tối đa 300 sản phẩm cùng lúc
- **Batch Image Operations**: Hỗ trợ thêm ảnh cho tối đa 10 sản phẩm cùng lúc
- **Memory Management**: Tự động giải phóng memory sau khi xử lý
- **Monolog Integration**: Hệ thống logging chuyên nghiệp
- **Error Handling**: Xử lý lỗi chi tiết với custom exceptions

### 🔄 Đang phát triển
- Product CRUD operations (Create, Update, Delete)
- Inventory management
- Order management
- Customer management

## 📦 Cài đặt

### Composer
```bash
composer require puleeno/nhanh-vn-sdk
```

### Dependencies
```json
{
    "require": {
        "php": ">=8.1",
        "guzzlehttp/guzzle": "^7.0",
        "nesbot/carbon": "^2.0",
        "illuminate/collections": "^10.0",
        "monolog/monolog": "^3.0"
    }
}
```

## 🔧 Cấu hình

### Khởi tạo SDK
```php
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Client\NhanhVnClient;

$config = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'secretKey' => 'YOUR_SECRET_KEY',
    'businessId' => 'YOUR_BUSINESS_ID',
    'accessToken' => 'YOUR_ACCESS_TOKEN',
    'version' => '2.0',
    'domain' => 'https://pos.open.nhanh.vn'
]);

$client = NhanhVnClient::getInstance($config);
```

### Cấu hình bắt buộc
- **API Request**: `version`, `appId`, `businessId`, `accessToken`
- **Get Access Code**: `appId`, `secretKey`, `redirectUrl`
- **Get Access Token**: `secretKey`, `version`, `appId`, `accessCode`

## 🔐 OAuth Flow

### Bước 1: Lấy Access Code
```php
$oauthUrl = $client->oauth()->getOAuthUrl();
// Chuyển hướng user đến $oauthUrl
```

### Bước 2: Lấy Access Token
```php
$tokens = $client->oauth()->getAccessToken($accessCode);
// $tokens chứa: accessToken, businessId, expiredAt, permissions, depotIds
```

## 📦 Product Management

### Tìm kiếm sản phẩm
```php
$products = $client->products()->search([
    'page' => 1,
    'limit' => 50,
    'name' => 'iPhone',
    'categoryId' => 123,
    'priceFrom' => 1000000,
    'priceTo' => 5000000
]);

foreach ($products as $product) {
    echo $product->getName() . ' - ' . $product->getFormattedPrice();
}
```

### Lấy chi tiết sản phẩm
```php
$product = $client->products()->detail(5003206);

if ($product) {
    echo "Tên: " . $product->getName();
    echo "Giá: " . $product->getFormattedPrice();
    echo "Tồn kho: " . $product->getAvailableQuantity();
    echo "Trạng thái: " . $product->getStatus();
}
```

### Lấy danh mục sản phẩm
```php
$categories = $client->products()->getCategories();

foreach ($categories as $category) {
    echo $category->getName() . ' - ' . $category->getDescription();
}
```

## 🏗️ Kiến trúc

### Core Components
```
src/
├── Client/
│   └── NhanhVnClient.php          # Singleton client chính
├── Config/
│   └── ClientConfig.php            # Cấu hình SDK
├── Services/
│   ├── HttpService.php             # HTTP client cho API calls
│   ├── OAuthService.php            # Xử lý OAuth flow
│   ├── CacheService.php            # Quản lý cache
│   └── Logger/
│       ├── LoggerInterface.php     # Interface cho logging
│       ├── MonologAdapter.php      # Adapter cho Monolog
│       └── NullLogger.php          # No-op logger
├── Modules/
│   ├── ProductModule.php           # Quản lý sản phẩm
│   └── OAuthModule.php             # Quản lý OAuth
├── Managers/
│   └── ProductManager.php          # Business logic sản phẩm
└── Entities/
    └── Product/
        ├── Product.php             # Entity sản phẩm
        └── ProductCategory.php     # Entity danh mục
```

### Design Patterns
- **Singleton**: `NhanhVnClient` - đảm bảo chỉ có 1 instance
- **Repository**: `ProductRepository` - abstract data access
- **Service Layer**: `HttpService`, `OAuthService` - business logic
- **Manager Layer**: `ProductManager` - orchestrate operations
- **Module Pattern**: `ProductModule`, `OAuthModule` - organize functionality
- **Adapter Pattern**: `MonologAdapter` - conform Monolog to custom interface

## 📝 API Endpoints

### Product APIs
- `POST /api/product/search` - Tìm kiếm sản phẩm
- `POST /api/product/detail` - Lấy chi tiết sản phẩm
- `POST /product/category` - Lấy danh mục sản phẩm

### OAuth APIs
- `GET /oauth` - Lấy access code
- `POST /api/oauth/access_token` - Lấy access token

## 🔍 Logging

### Cấu hình Monolog
```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Puleeno\NhanhVn\Services\Logger\MonologAdapter;

$logger = new Logger('nhanh-vn-sdk');
$logger->pushHandler(new StreamHandler('logs/sdk.log', Logger::DEBUG));

$client->setLogger(new MonologAdapter($logger));
```

### Log Levels
- **DEBUG**: Chi tiết API calls và responses
- **INFO**: Thông tin hoạt động bình thường
- **WARNING**: Cảnh báo về data không hợp lệ
- **ERROR**: Lỗi xảy ra trong quá trình xử lý

## 🧪 Examples

### Khởi tạo với boot file
```php
// examples/boot/client.php
require_once __DIR__ . '/../boot/client.php';

$client = bootNhanhVnClientWithLogger('DEBUG');
$products = $client->products()->search();
```

### Xử lý lỗi
```php
try {
    $product = $client->products()->detail($productId);
} catch (ApiException $e) {
    echo "API Error: " . $e->getMessage();
    echo "HTTP Status: " . $e->getHttpStatusCode();
    echo "Response: " . $e->getResponseBody();
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage();
}
```

## 🚨 Error Handling

### Custom Exceptions
- `ApiException`: Lỗi từ Nhanh.vn API
- `RateLimitException`: Vượt quá giới hạn API calls
- `UnauthorizedException`: Lỗi xác thực
- `InvalidDataException`: Dữ liệu không hợp lệ
- `ConfigurationException`: Lỗi cấu hình
- `NetworkException`: Lỗi kết nối mạng

### Error Response Structure
```php
{
    "code": 0,
    "messages": ["Error message"],
    "errorCode": "ERROR_CODE",
    "errorData": {...}
}
```

## 💾 Memory Management

### Tự động giải phóng memory
```php
// Helper methods tự động unset() sau khi xử lý
$products = $this->createEntitiesFromApiResponse($response, 'createProduct');
$categories = $this->createEntitiesWithMemoryManagement($cachedData, 'createProductCategories');
```

### Best Practices
- Sử dụng helper methods để quản lý memory
- Unset variables sau khi xử lý xong
- Tránh giữ references không cần thiết

## 🔧 Development

### Chạy examples
```bash
cd examples
php -S localhost:8000 -t public
```

### Available Examples
- `oauth.php` - OAuth flow demo
- `get_products.php` - Product search demo
- `get_product_detail.php` - Product detail demo
- `get_categories.php` - Categories demo
- `get_products_with_logger.php` - Logging demo
- `add_product.php` - Product add demo
- `add_product_images.php` - Product external images demo

### Testing
```bash
composer test
```

## 📚 API Documentation

### Product Search Parameters
- `page`: Số trang (mặc định: 1)
- `limit`: Số sản phẩm/trang (tối đa: 100)
- `name`: Tìm theo tên/mã/mã vạch
- `categoryId`: Tìm theo danh mục
- `brandId`: Tìm theo thương hiệu
- `priceFrom/priceTo`: Khoảng giá
- `status`: Trạng thái sản phẩm
- `showHot/showNew/showHome`: Flags hiển thị

### Product Response Fields
- `idNhanh`: ID sản phẩm trên Nhanh.vn
- `code`: Mã sản phẩm
- `name`: Tên sản phẩm
- `price`: Giá bán lẻ
- `wholesalePrice`: Giá bán buôn
- `importPrice`: Giá nhập
- `status`: Trạng thái (Active/Inactive/OutOfStock)
- `inventory`: Thông tin tồn kho
- `images`: Hình ảnh sản phẩm
- `categoryId`: ID danh mục
- `brandId`: ID thương hiệu

### Product External Images API
- **Endpoint**: `/api/product/externalimage`
- **Giới hạn**: Tối đa 10 sản phẩm mỗi request, mỗi sản phẩm tối đa 20 ảnh
- **Mode**: `update` (mặc định) hoặc `deleteall`
- **Lưu ý**: Nhanh.vn sẽ không tải ảnh về mà dùng trực tiếp URL từ CDN

## 🤝 Contributing

1. Fork repository
2. Tạo feature branch
3. Commit changes với conventional commits
4. Push to branch
5. Tạo Pull Request

### Code Standards
- Tuân thủ PSR-12
- Sử dụng PHP DocBlock
- Unit tests cho tất cả methods
- Type hints cho parameters và return values

## 📄 License

MIT License - xem file [LICENSE](LICENSE) để biết thêm chi tiết.

## 📞 Support

- **Email**: puleeno@gmail.com
- **Issues**: GitHub Issues
- **Documentation**: [docs/](docs/) folder

---

**Nhanh.vn SDK v2.0** - Giải pháp tích hợp API hoàn chỉnh cho Nhanh.vn
