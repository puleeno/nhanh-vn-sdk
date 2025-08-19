# Nhanh.vn SDK v2.0 - Advanced Usage Guide

## Tổng quan

**⚠️ Lưu ý:** Đây là tài liệu nâng cao cho SDK v2.0. Để bắt đầu, hãy xem [docs/README.md](../README.md) trước.

Nhanh.vn SDK v2.0 là thư viện PHP hiện đại để tích hợp với Nhanh.vn API. SDK được thiết kế theo các nguyên tắc SOLID, sử dụng design patterns phổ biến và cung cấp API dễ sử dụng.

**🔐 Lưu ý về xác thực:** Nhanh.vn API 2.0 sử dụng flow xác thực riêng của họ (không phải OAuth 2.0 chuẩn). Flow này bao gồm:
1. Tạo URL xác thực với `appId`, `secretKey` và `redirectUrl`
2. User authorize và nhận `access_code`
3. Đổi `access_code` lấy `access_token`
4. Sử dụng `access_token` để gọi các API khác

SDK cung cấp `OAuthExample` class để xử lý flow xác thực này một cách dễ dàng.

## Cài đặt

```bash
composer require puleeno/nhanh-vn-sdk
```

## Khởi tạo

### Cấu hình cơ bản

```php
use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;

$config = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'businessId' => 'YOUR_BUSINESS_ID',
    'accessToken' => 'YOUR_ACCESS_TOKEN',
    'apiVersion' => '2.0',
    'baseUrl' => 'https://pos.open.nhanh.vn',
    'timeout' => 30,
    'retryAttempts' => 3
]);

$client = NhanhVnClient::getInstance($config);
```

### OAuth Flow (Sử dụng OAuthExample)

**⚠️ Lưu ý:** Nhanh.vn API 2.0 không sử dụng OAuth chuẩn mà là flow xác thực riêng của họ. Tên "OAuth" ở đây chỉ là tên gọi, không phải protocol OAuth 2.0 chuẩn.

#### Bước 1: Tạo OAuthExample instance

```php
use Examples\OAuthExample;

$app = new OAuthExample();

// Hiển thị link xác thực
$app->showOAuthLink();

// Lấy URL xác thực
$authUrl = $app->getOAuthUrl();
echo "URL xác thực: " . $authUrl;
```

#### Bước 2: Xử lý Callback xác thực

```php
// Trong file callback.php
$app = new OAuthExample();
$app->handleCallback();
```

#### Bước 3: Khởi tạo Client với Access Token

```php
// Sử dụng boot file để khởi tạo client
require_once __DIR__ . '/boot/client.php';

// Khởi tạo client không có logger
$client = bootNhanhVnClientSilent();

// Hoặc khởi tạo client với Monolog logger
$client = bootNhanhVnClientWithLogger('DEBUG');

// Kiểm tra client đã sẵn sàng
if (isClientReady()) {
    echo "Client đã sẵn sàng!";
} else {
    echo "Client chưa sẵn sàng. Vui lòng chạy flow xác thực trước!";
}
```

#### Bước 4: Lấy thông tin Client

```php
$clientInfo = getClientInfo();
echo "App ID: " . $clientInfo['appId'];
echo "Business ID: " . $clientInfo['businessId'];
echo "API Version: " . $clientInfo['apiVersion'];
echo "Has Access Token: " . ($clientInfo['hasAccessToken'] ? 'Yes' : 'No');
```

### Flow xác thực thực tế của Nhanh.vn

1. **Tạo URL xác thực**: Sử dụng `appId`, `secretKey` và `redirectUrl`
2. **User authorize**: User truy cập URL và cấp quyền
3. **Nhận access_code**: Nhanh.vn trả về `access_code` qua callback
4. **Đổi access_code lấy access_token**: Gọi API để đổi `access_code` thành `access_token`
5. **Sử dụng access_token**: Sử dụng `access_token` để gọi các API khác

### Sử dụng NhanhClientBuilder (Khuyến nghị)

```php
use Puleeno\NhanhVn\Client\NhanhClientBuilder;

// Tạo client cơ bản
$client = NhanhClientBuilder::create()
    ->withAppId('YOUR_APP_ID')
    ->withBusinessId('YOUR_BUSINESS_ID')
    ->withAccessToken('YOUR_ACCESS_TOKEN')
    ->build();

// Tạo client với logging
$client = NhanhClientBuilder::create()
    ->withAppId('YOUR_APP_ID')
    ->withBusinessId('YOUR_BUSINESS_ID')
    ->withAccessToken('YOUR_ACCESS_TOKEN')
    ->withLogger()
    ->withLogLevel('DEBUG')
    ->withLogFile('logs/nhanh-vn-sdk.log')
    ->withConsoleLogging()
    ->build();

// Sử dụng presets
$client = NhanhClientBuilder::createDevelopment(
    'YOUR_APP_ID',
    'YOUR_BUSINESS_ID',
    'YOUR_ACCESS_TOKEN'
);

$client = NhanhClientBuilder::createProduction(
    'YOUR_APP_ID',
    'YOUR_BUSINESS_ID',
    'YOUR_ACCESS_TOKEN'
);
```

## Sử dụng API

### Module Sản phẩm (Products)

#### Tìm kiếm sản phẩm

```php
try {
    // Tìm kiếm cơ bản
    $searchCriteria = [
        'page' => 1,
        'limit' => 10,
        'status' => 'Active'
    ];

    $products = $client->products()->search($searchCriteria);

    // Xử lý kết quả
    foreach ($products as $product) {
        echo "Tên: " . $product->getName() . "\n";
        echo "Giá: " . number_format($product->getPrice()) . " VNĐ\n";
        echo "Tồn kho: " . $product->getAvailableQuantity() . " / " . $product->getTotalQuantity() . "\n";
        echo "---\n";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

#### Lấy chi tiết sản phẩm

```php
try {
    $productId = 12345;
    $product = $client->products()->detail($productId);

    echo "Tên: " . $product->getName() . "\n";
    echo "Mô tả: " . $product->getDescription() . "\n";
    echo "Giá: " . number_format($product->getPrice()) . " VNĐ\n";
    echo "Tồn kho: " . $product->getInventory()->getQuantity() . "\n";
    echo "Danh mục: " . $product->getCategory()->getName() . "\n";

    // Thông tin thuộc tính
    foreach ($product->getAttributes() as $attribute) {
        echo $attribute->getName() . ": " . $attribute->getValue() . "\n";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

#### Thêm sản phẩm mới

```php
try {
    $productData = [
        'id' => 'PROD_' . time(), // ID hệ thống riêng (bắt buộc)
        'name' => 'iPhone 15 Pro Max', // Tên sản phẩm (bắt buộc)
        'price' => 45000000, // Giá sản phẩm (bắt buộc)
        'code' => 'IPHONE15PM-256',
        'barcode' => '1234567890123',
        'description' => 'Điện thoại iPhone mới nhất',
        'categoryId' => 1,
        'brandId' => 2,
        'importPrice' => 40000000,
        'wholesalePrice' => 42000000,
        'shippingWeight' => 221, // Cân nặng vận chuyển (gram)
        'vat' => 10, // Thuế VAT (%)
        'status' => 'Active',
        'externalImages' => [
            'https://example.com/iphone15-1.jpg',
            'https://example.com/iphone15-2.jpg'
        ]
    ];

    // Validate data trước khi gửi
    if ($client->products()->validateProductAddRequest($productData)) {
        $response = $client->products()->add($productData);

        echo "Đã tạo sản phẩm mới thành công!\n";
        echo "ID hệ thống: " . $productData['id'] . "\n";
        echo "ID Nhanh.vn: " . $response->getNhanhId($productData['id']) . "\n";
        echo "Barcode: " . $response->getBarcode($productData['id']) . "\n";
    } else {
        echo "Dữ liệu sản phẩm không hợp lệ\n";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

#### Thêm nhiều sản phẩm cùng lúc (Batch)

```php
try {
    $batchProducts = [
        [
            'id' => 'PROD_1',
            'name' => 'MacBook Pro 14" M3 Pro',
            'price' => 55000000,
            'code' => 'MBP14-M3PRO'
        ],
        [
            'id' => 'PROD_2',
            'name' => 'iPad Air 5 64GB',
            'price' => 18000000,
            'code' => 'IPADAIR5-64'
        ]
    ];

    // Validate batch data
    $errors = $client->products()->validateProductAddRequests($batchProducts);
    if (empty($errors)) {
        $response = $client->products()->addBatch($batchProducts);

        echo "Batch thêm sản phẩm thành công!\n";
        echo "Tổng số: " . $response->getTotalProducts() . "\n";
        echo "Thành công: " . $response->getSuccessCount() . "\n";
        echo "Tỷ lệ thành công: " . $response->getSuccessRate() . "%\n";
    } else {
        echo "Có lỗi validation trong batch data\n";
    }

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

#### Lấy sản phẩm theo danh mục

```php
try {
    $categoryId = 1;
    $products = $client->products()->getByCategory($categoryId, [
        'page' => 1,
        'limit' => 50,
        'sortBy' => 'price',
        'sortOrder' => 'asc'
    ]);

    echo "Tìm thấy " . $products->count() . " sản phẩm trong danh mục\n";

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

#### Sản phẩm nổi bật và mới

```php
try {
    // Sản phẩm nổi bật
    $hotProducts = $client->products()->getHot(10);

    // Sản phẩm mới
    $newProducts = $client->products()->getNew(10);

    // Sản phẩm trang chủ
    $homeProducts = $client->products()->getHome();

    echo "Sản phẩm nổi bật: " . $hotProducts->count() . "\n";
    echo "Sản phẩm mới: " . $newProducts->count() . "\n";

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

### Quản lý Cache

#### Kiểm tra trạng thái cache

```php
try {
    $cacheStatus = $client->products()->getCacheStatus();

    echo "Cache có sẵn: " . ($cacheStatus['available'] ? 'Có' : 'Không') . "\n";
    echo "Số lượng danh mục đã cache: " . $cacheStatus['categories'] . "\n";
    echo "Số lượng thương hiệu đã cache: " . $cacheStatus['brands'] . "\n";
    echo "Thời gian cache còn lại: " . $cacheStatus['ttl'] . " giây\n";

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

#### Xóa cache

```php
try {
    $client->products()->clearCache();
    echo "Đã xóa toàn bộ cache\n";

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

### Xử lý lỗi

#### Các loại Exception

```php
use Puleeno\NhanhVn\Exceptions\{
    ConfigurationException,
    ApiException,
    RateLimitException,
    UnauthorizedException,
    InvalidDataException
};

try {
    $products = $client->products()->search('test');
} catch (ConfigurationException $e) {
    echo "Lỗi cấu hình: " . $e->getMessage();
} catch (RateLimitException $e) {
    echo "Vượt quá giới hạn API. Thử lại sau " . $e->getLockedSeconds() . " giây";
    echo "Mở khóa lúc: " . $e->getUnlockedAt()->format('Y-m-d H:i:s');
} catch (UnauthorizedException $e) {
    echo "Không có quyền truy cập. Vui lòng kiểm tra access token";
} catch (InvalidDataException $e) {
    echo "Dữ liệu không hợp lệ: " . $e->getMessage();
} catch (ApiException $e) {
    echo "Lỗi API: " . $e->getMessage();
    echo "Mã lỗi: " . $e->getCode();
    echo "Thông báo: " . implode(', ', $e->getMessages());
}
```

#### Retry Logic

```php
$maxRetries = 3;
$retryCount = 0;

while ($retryCount < $maxRetries) {
    try {
        $products = $client->products()->search('iPhone');
        break; // Thành công, thoát vòng lặp
    } catch (RateLimitException $e) {
        $retryCount++;
        if ($retryCount >= $maxRetries) {
            throw $e; // Hết số lần thử
        }

        $waitTime = $e->getLockedSeconds();
        echo "Đợi $waitTime giây trước khi thử lại...\n";
        sleep($waitTime);
    } catch (Exception $e) {
        throw $e; // Lỗi khác, không retry
    }
}
```

## Cấu trúc dữ liệu

### Product Entity

```php
$product = $client->products()->detail(12345);

// Thông tin cơ bản
$product->getId();           // int
$product->getName();         // string
$product->getDescription();  // string
$product->getPrice();        // float
$product->getSku();          // string

// Thông tin danh mục
$category = $product->getCategory();
$category->getId();          // int
$category->getName();        // string
$category->getSlug();        // string
```

### ProductAddRequest Entity

```php
$request = new ProductAddRequest([
    'id' => 'PROD_123',
    'name' => 'iPhone 15 Pro Max',
    'price' => 45000000,
    'code' => 'IPHONE15PM-256',
    'barcode' => '1234567890123',
    'description' => 'Điện thoại iPhone mới nhất',
    'categoryId' => 1,
    'brandId' => 2,
    'importPrice' => 40000000,
    'wholesalePrice' => 42000000,
    'shippingWeight' => 221,
    'vat' => 10,
    'status' => 'Active',
    'externalImages' => ['https://example.com/image1.jpg']
]);

// Validation
if ($request->isValid()) {
    echo "Dữ liệu hợp lệ\n";
} else {
    echo "Lỗi: " . json_encode($request->getErrors());
}

// Business logic
$request->isNew();           // bool - Là sản phẩm mới
$request->isUpdate();        // bool - Là cập nhật sản phẩm
$request->hasDiscount();     // bool - Có giảm giá
$request->getDiscountAmount(); // float - Số tiền giảm giá
$request->getDiscountPercentage(); // float - Phần trăm giảm giá

// Convert to API format
$apiData = $request->toApiFormat();
```

### ProductAddResponse Entity

```php
$response = $client->products()->add($productData);

// Basic info
$response->getTotalProducts();    // int - Tổng số sản phẩm
$response->getSuccessCount();     // int - Số sản phẩm thành công
$response->getFailedCount();      // int - Số sản phẩm thất bại
$response->getSuccessRate();      // float - Tỷ lệ thành công (%)

// Status checks
$response->isAllSuccess();        // bool - Tất cả đều thành công
$response->hasFailures();         // bool - Có sản phẩm thất bại

// ID mappings
$response->getNhanhId('PROD_123');     // int - ID Nhanh.vn
$response->getBarcode('PROD_123');     // string - Barcode
$response->hasSystemId('PROD_123');    // bool - Kiểm tra ID tồn tại

// Summary
$summary = $response->getSummary();
// [
//     'total_products' => 3,
//     'success_count' => 3,
//     'failed_count' => 0,
//     'success_rate' => 100.0,
//     'is_all_success' => true,
//     'has_failures' => false
// ]
```

// Thông tin tồn kho
$inventory = $product->getInventory();
$inventory->getQuantity();   // int
$inventory->getReserved();   // int
$inventory->getAvailable();  // int

// Thuộc tính sản phẩm
foreach ($product->getAttributes() as $attribute) {
    $attribute->getName();   // string
    $attribute->getValue();  // string
    $attribute->getType();   // string
}
```

### Collections

```php
$products = $client->products()->search('iPhone');

// Đếm số lượng
$count = $products->count();

// Lọc theo điều kiện
$expensiveProducts = $products->filter(function ($product) {
    return $product->getPrice() > 20000000;
});

// Sắp xếp
$sortedProducts = $products->sortBy('price');

// Lấy sản phẩm đầu tiên
$firstProduct = $products->first();

// Chuyển đổi thành array
$productsArray = $products->toArray();
```

## Best Practices

### 1. Sử dụng OAuthExample cho xác thực

```php
// ĐÚNG - Sử dụng OAuthExample class
use Examples\OAuthExample;

$app = new OAuthExample();
$app->showOAuthLink(); // Hiển thị link xác thực
$app->handleCallback(); // Xử lý callback

// Sử dụng boot file để khởi tạo client
require_once __DIR__ . '/boot/client.php';
$client = bootNhanhVnClientSilent();

// SAI - Không tự implement flow xác thực
// $client->getOAuthUrl() // Không tồn tại method này
```

### 2. Singleton Pattern

```php
// ĐÚNG - Sử dụng singleton
$client = NhanhVnClient::getInstance($config);

// SAI - Không tạo instance mới
$client = new NhanhVnClient($config);
```

### 3. Error Handling

```php
// Luôn wrap API calls trong try-catch
try {
    $result = $client->products()->search($searchCriteria);
} catch (Exception $e) {
    // Log lỗi
    error_log("Nhanh.vn API Error: " . $e->getMessage());

    // Xử lý lỗi phù hợp
    if ($e instanceof RateLimitException) {
        // Implement retry logic
    }
}
```

### 4. Caching

```php
// Kiểm tra cache trước khi gọi API
if ($client->products()->isCacheAvailable()) {
    $categories = $client->products()->getCategories();
} else {
    // Cache không có sẵn, gọi API trực tiếp
    $categories = $client->products()->getCategories();
}
```

### 5. Rate Limiting

```php
// Implement exponential backoff
$baseDelay = 1;
$maxDelay = 60;

try {
    $result = $client->products()->search($searchCriteria);
} catch (RateLimitException $e) {
    $delay = min($baseDelay * pow(2, $retryCount), $maxDelay);
    sleep($delay);
    // Retry logic
}
```

### 6. Sử dụng NhanhClientBuilder

```php
// Khuyến nghị sử dụng Builder pattern
$client = NhanhClientBuilder::create()
    ->withAppId('YOUR_APP_ID')
    ->withBusinessId('YOUR_BUSINESS_ID')
    ->withAccessToken('YOUR_ACCESS_TOKEN')
    ->withLogger()
    ->withLogLevel('DEBUG')
    ->build();

// Hoặc sử dụng presets
$client = NhanhClientBuilder::createDevelopment(
    'YOUR_APP_ID',
    'YOUR_BUSINESS_ID',
    'YOUR_ACCESS_TOKEN'
);
```

## API Endpoints

### Xác thực (Authentication)
- `GET /oauth` - Lấy access code (không phải OAuth chuẩn)
- `POST /api/oauth/access_token` - Đổi access code lấy access token

### Products
- `POST /api/product/search` - Tìm kiếm sản phẩm
- `POST /api/product/detail` - Lấy chi tiết sản phẩm
- `POST /api/product/add` - Thêm/cập nhật sản phẩm (hỗ trợ batch tối đa 300 sản phẩm)
- `POST /api/product/update` - Cập nhật sản phẩm
- `POST /api/product/delete` - Xóa sản phẩm
- `POST /api/product/category` - Quản lý danh mục
- `POST /api/product/internalcategory` - Quản lý danh mục nội bộ
- `POST /api/product/gift` - Quản lý quà tặng
- `POST /api/product/externalimage` - Thêm ảnh sản phẩm từ CDN bên ngoài

### Customers
- `POST /api/customer/search` - Tìm kiếm khách hàng với các tiêu chí khác nhau
- `POST /api/customer/add` - Thêm khách hàng mới (hỗ trợ batch)

### Orders
- `POST /api/order/add` - Thêm đơn hàng mới với đầy đủ tùy chọn vận chuyển và thanh toán
- `POST /api/order/update` - Cập nhật đơn hàng
- `POST /api/order/search` - Tìm kiếm đơn hàng

### Shipping
- `GET /api/shipping/carrier` - Lấy danh sách hãng vận chuyển và dịch vụ vận chuyển
- `POST /api/shipping/fee` - Tính phí vận chuyển
- `POST /api/shipping/location` - Quản lý địa điểm (thành phố, quận huyện, phường xã)

### Inventory
- `POST /api/product/expire` - Quản lý hạn sử dụng
- `POST /api/product/imei` - Quản lý IMEI
- `POST /api/product/imeihistory` - Lịch sử IMEI
- `POST /api/product/imeisold` - IMEI đã bán

## Giới hạn API

- **Rate Limit**: 150 requests / 30 giây
- **Scope**: `appId + businessId + API URL`
- **Timeout**: Mặc định 30 giây
- **Retry**: Tối đa 3 lần

## Hỗ trợ

- **Documentation**: [GitHub Repository](https://github.com/puleeno/nhanh-vn-sdk)
- **Issues**: [GitHub Issues](https://github.com/puleeno/nhanh-vn-sdk/issues)
- **Email**: puleeno@gmail.com

## Tài liệu chi tiết

- **[OAuth Flow](oauth-flow.md)** - Hướng dẫn chi tiết về flow xác thực
- **[Client Builder](client-builder.md)** - Sử dụng NhanhClientBuilder
- **[Product Management](product/README.md)** - Quản lý sản phẩm
- **[Customer Management](customer/README.md)** - Quản lý khách hàng
- **[Order Management](order/README.md)** - Quản lý đơn hàng
- **[Shipping Management](shipping/README.md)** - Quản lý vận chuyển

## Changelog

### v2.0.0
- Thiết kế lại hoàn toàn với kiến trúc modular
- Sử dụng design patterns: Manager, Repository, Service, Module
- Entity-based data handling với immutable objects
- Tích hợp Laravel Collections và Carbon
- Hệ thống cache thông minh
- Error handling toàn diện với custom exceptions
- **Flow xác thực riêng**: Sử dụng access_code và access_token (không phải OAuth chuẩn)
- **Product Add API**: Hỗ trợ thêm/cập nhật sản phẩm với validation toàn diện
- **Batch Operations**: Hỗ trợ thêm tối đa 300 sản phẩm cùng lúc
- **ProductAddRequest/Response Entities**: DTO pattern cho API requests/responses
- **Customer Module**: Tìm kiếm và quản lý khách hàng với validation toàn diện
- **Customer Search API**: Hỗ trợ tìm kiếm theo ID, mobile, type, date range
- **Customer Add API**: Hỗ trợ thêm khách hàng đơn lẻ và batch
- **Order Module**: Thêm đơn hàng mới với validation toàn diện và hỗ trợ đầy đủ tùy chọn vận chuyển
- **Order Add API**: Hỗ trợ đơn hàng vận chuyển, tại cửa hàng, đặt trước với business rules validation
- **Order Update API**: Cập nhật đơn hàng với validation toàn diện
- **Order Search API**: Tìm kiếm đơn hàng với các bộ lọc và phân trang
- **Shipping Module**: Quản lý hãng vận chuyển và dịch vụ vận chuyển với cache management thông minh
- **Shipping Carrier API**: Lấy danh sách hãng vận chuyển (Vietnam Post, Giaohangnhanh, J&T Express, Viettel Post, EMS, Ninjavan, Best Express...) với cache 24h
- **Shipping Fee API**: Tính phí vận chuyển cho đơn hàng
- **Location API**: Quản lý địa điểm (thành phố, quận huyện, phường xã)
- **Product External Image API**: Thêm ảnh sản phẩm từ CDN bên ngoài
- **Boot File System**: Hệ thống khởi tạo client thông minh với OAuthExample
- **Monolog Integration**: Tích hợp logging toàn diện với Monolog
- **Client Builder Pattern**: NhanhClientBuilder với fluent interface
- Documentation đầy đủ với examples thực tế
