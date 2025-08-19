# Nhanh.vn SDK v2.0 - Hướng dẫn sử dụng

## Tổng quan

Nhanh.vn SDK v2.0 là thư viện PHP hiện đại để tích hợp với Nhanh.vn API. SDK được thiết kế theo các nguyên tắc SOLID, sử dụng design patterns phổ biến và cung cấp API dễ sử dụng.

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
    'environment' => 'production', // hoặc 'sandbox'
    'timeout' => 30,
    'retryAttempts' => 3
]);

$client = NhanhVnClient::getInstance($config);
```

### OAuth Flow

#### Bước 1: Lấy Access Code

```php
// Tạo URL OAuth để user authorize
$oauthUrl = $client->getOAuthUrl('https://your-app.com/callback');

// Redirect user đến URL này
header('Location: ' . $oauthUrl);
exit;
```

#### Bước 2: Đổi Access Code lấy Access Token

```php
// Sau khi user authorize, bạn nhận được access_code từ callback
$accessCode = $_GET['access_code'] ?? null;

if ($accessCode) {
    try {
        $accessToken = $client->exchangeAccessCode($accessCode);

        // Lưu access token vào database hoặc session
        $_SESSION['nhanhvn_access_token'] = $accessToken;

        echo "Xác thực thành công! Access Token: " . $accessToken;
    } catch (Exception $e) {
        echo "Lỗi xác thực: " . $e->getMessage();
    }
}
```

## Sử dụng API

### Module Sản phẩm (Products)

#### Tìm kiếm sản phẩm

```php
try {
    // Tìm kiếm cơ bản
    $products = $client->products()->search('iPhone');

    // Tìm kiếm nâng cao
    $products = $client->products()->search('iPhone', [
        'categoryId' => 1,
        'brandId' => 2,
        'minPrice' => 1000000,
        'maxPrice' => 50000000,
        'page' => 1,
        'limit' => 20
    ]);

    // Xử lý kết quả
    foreach ($products as $product) {
        echo "Tên: " . $product->getName() . "\n";
        echo "Giá: " . number_format($product->getPrice()) . " VNĐ\n";
        echo "Danh mục: " . $product->getCategory()->getName() . "\n";
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

### 1. Singleton Pattern

```php
// ĐÚNG - Sử dụng singleton
$client = NhanhVnClient::getInstance($config);

// SAI - Không tạo instance mới
$client = new NhanhVnClient($config);
```

### 2. Error Handling

```php
// Luôn wrap API calls trong try-catch
try {
    $result = $client->products()->search('test');
} catch (Exception $e) {
    // Log lỗi
    error_log("Nhanh.vn API Error: " . $e->getMessage());

    // Xử lý lỗi phù hợp
    if ($e instanceof RateLimitException) {
        // Implement retry logic
    }
}
```

### 3. Caching

```php
// Kiểm tra cache trước khi gọi API
if ($client->products()->isCacheAvailable()) {
    $categories = $client->products()->getCategories();
} else {
    // Cache không có sẵn, gọi API trực tiếp
    $categories = $client->products()->getCategories();
}
```

### 4. Rate Limiting

```php
// Implement exponential backoff
$baseDelay = 1;
$maxDelay = 60;

try {
    $result = $client->products()->search('test');
} catch (RateLimitException $e) {
    $delay = min($baseDelay * pow(2, $retryCount), $maxDelay);
    sleep($delay);
    // Retry logic
}
```

## API Endpoints

### OAuth
- `GET /oauth` - Lấy access code
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

### Customers
- `POST /api/customer/search` - Tìm kiếm khách hàng với các tiêu chí khác nhau

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

## Changelog

### v2.0.0
- Thiết kế lại hoàn toàn với kiến trúc modular
- Sử dụng design patterns: Manager, Repository, Service, Module
- Entity-based data handling với immutable objects
- Tích hợp Laravel Collections và Carbon
- Hệ thống cache thông minh
- Error handling toàn diện với custom exceptions
- OAuth flow hoàn chỉnh
- **Product Add API**: Hỗ trợ thêm/cập nhật sản phẩm với validation toàn diện
- **Batch Operations**: Hỗ trợ thêm tối đa 300 sản phẩm cùng lúc
- **ProductAddRequest/Response Entities**: DTO pattern cho API requests/responses
- **Customer Module**: Tìm kiếm và quản lý khách hàng với validation toàn diện
- **Customer Search API**: Hỗ trợ tìm kiếm theo ID, mobile, type, date range
- Documentation đầy đủ với examples
