# Product API Documentation

## 📋 Tổng quan

Product API cung cấp các chức năng quản lý sản phẩm trên Nhanh.vn, bao gồm tìm kiếm, lấy chi tiết, quản lý danh mục và các thao tác khác.

## 🔍 Product Search

### Endpoint
```
POST /api/product/search
```

### Mô tả
Tìm kiếm sản phẩm theo các tiêu chí với hỗ trợ phân trang và sắp xếp.

### Parameters

#### Cơ bản
| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `page` | int | No | Số trang (mặc định: 1) |
| `limit` | int | No | Số sản phẩm/trang (tối đa: 100) |
| `sort` | array | No | Sắp xếp kết quả |

#### Tìm kiếm
| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `name` | string | No | Tìm theo tên, mã, mã vạch |
| `parentId` | int | No | ID sản phẩm cha (-1: độc lập, -2: cha) |
| `categoryId` | int | No | ID danh mục sản phẩm |
| `status` | string | No | Trạng thái sản phẩm |
| `brandId` | int | No | ID thương hiệu |
| `imei` | string | No | Tìm theo IMEI |

#### Khoảng giá
| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `priceFrom` | float | No | Giá từ (>=) |
| `priceTo` | float | No | Giá đến (<=) |

#### Flags hiển thị
| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `showHot` | int | No | Sản phẩm hot (0/1) |
| `showNew` | int | No | Sản phẩm mới (0/1) |
| `showHome` | int | No | Hiển thị trang chủ (0/1) |

#### Thời gian cập nhật
| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `updatedDateTimeFrom` | string | No | Từ ngày (Y-m-d H:i:s) |
| `updatedDateTimeTo` | string | No | Đến ngày (Y-m-d H:i:s) |

### Ví dụ sử dụng

```php
// Tìm kiếm cơ bản
$products = $client->products()->search([
    'page' => 1,
    'limit' => 50
]);

// Tìm kiếm theo tên
$products = $client->products()->search([
    'name' => 'iPhone',
    'limit' => 20
]);

// Tìm kiếm theo danh mục và giá
$products = $client->products()->search([
    'categoryId' => 123,
    'priceFrom' => 1000000,
    'priceTo' => 5000000,
    'showHot' => 1
]);

// Tìm kiếm sản phẩm mới cập nhật
$products = $client->products()->search([
    'updatedDateTimeFrom' => '2024-01-01 00:00:00',
    'updatedDateTimeTo' => '2024-01-31 23:59:59'
]);
```

### Response Structure

```json
{
    "code": 1,
    "data": {
        "totalPages": 30,
        "products": [
            {
                "idNhanh": 5003206,
                "code": "IPHONE-15",
                "name": "iPhone 15 Pro Max",
                "price": 25000000,
                "wholesalePrice": 23000000,
                "importPrice": 20000000,
                "status": "Active",
                "categoryId": 123,
                "brandId": 456,
                "inventory": {
                    "remain": 50,
                    "available": 45,
                    "shipping": 3,
                    "holding": 2
                }
            }
        ]
    }
}
```

## 🔍 Product Detail

### Endpoint
```
POST /api/product/detail
```

### Mô tả
Lấy thông tin chi tiết của sản phẩm theo ID, bao gồm tất cả thuộc tính và thông tin tồn kho.

### Parameters

| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `productId` | int | Yes | ID sản phẩm trên Nhanh.vn |

### Ví dụ sử dụng

```php
$product = $client->products()->detail(5003206);

if ($product) {
    echo "Tên: " . $product->getName();
    echo "Mã: " . $product->getCode();
    echo "Giá: " . $product->getFormattedPrice();
    echo "Tồn kho: " . $product->getAvailableQuantity();
    echo "Trạng thái: " . $product->getStatus();
}
```

### Response Fields

#### Thông tin cơ bản
| Field | Type | Mô tả |
|-------|------|--------|
| `idNhanh` | bigint | ID sản phẩm trên Nhanh.vn |
| `code` | string | Mã sản phẩm |
| `name` | string | Tên sản phẩm |
| `otherName` | string | Tên khác của sản phẩm |
| `barcode` | string | Mã vạch sản phẩm |
| `status` | string | Trạng thái sản phẩm |

#### Thông tin giá
| Field | Type | Mô tả |
|-------|------|--------|
| `importPrice` | double | Giá nhập |
| `oldPrice` | double | Giá cũ |
| `price` | double | Giá bán lẻ |
| `wholesalePrice` | double | Giá bán buôn |
| `vat` | int | % thuế VAT |

#### Phân loại
| Field | Type | Mô tả |
|-------|------|--------|
| `categoryId` | int | ID danh mục sản phẩm |
| `brandId` | int | ID thương hiệu |
| `brandName` | string | Tên thương hiệu |
| `typeId` | int | ID loại sản phẩm |
| `typeName` | string | Tên loại sản phẩm |
| `parentId` | bigint | ID sản phẩm cha |

#### Kích thước & Trọng lượng
| Field | Type | Mô tả |
|-------|------|--------|
| `width` | int | Chiều rộng (cm) |
| `height` | int | Chiều cao (cm) |
| `length` | int | Chiều dài (cm) |
| `shippingWeight` | int | Trọng lượng (gram) |

#### Bảo hành
| Field | Type | Mô tả |
|-------|------|--------|
| `warranty` | int | Số tháng bảo hành |
| `warrantyAddress` | string | Địa chỉ bảo hành |
| `warrantyPhone` | string | Số điện thoại bảo hành |
| `warrantyContent` | string | Nội dung bảo hành |

#### Hiển thị
| Field | Type | Mô tả |
|-------|------|--------|
| `showHot` | int | Sản phẩm hot (0/1) |
| `showNew` | int | Sản phẩm mới (0/1) |
| `showHome` | int | Hiển thị trang chủ (0/1) |

#### Thông tin khác
| Field | Type | Mô tả |
|-------|------|--------|
| `createdDateTime` | datetime | Ngày tạo sản phẩm |
| `countryName` | string | Xuất xứ |
| `unit` | string | Đơn vị tính |
| `previewLink` | string | Link xem sản phẩm |

#### Hình ảnh
| Field | Type | Mô tả |
|-------|------|--------|
| `image` | string | Ảnh đại diện |
| `images` | array | Danh sách ảnh khác |

#### Tồn kho
| Field | Type | Mô tả |
|-------|------|--------|
| `inventory` | object | Thông tin tồn kho chi tiết |

## 📂 Product Categories

### Endpoint
```
POST /product/category
```

### Mô tả
Lấy danh sách tất cả danh mục sản phẩm trên Nhanh.vn.

### Parameters
Không có parameters bắt buộc.

### Ví dụ sử dụng

```php
$categories = $client->products()->getCategories();

foreach ($categories as $category) {
    echo "Tên: " . $category->getName();
    echo "Mô tả: " . $category->getDescription();
    echo "Số sản phẩm: " . $category->getProductCount();
    echo "Level: " . $category->getLevel();
    echo "Parent ID: " . $category->getParentId();
}
```

### Response Fields

| Field | Type | Mô tả |
|-------|------|--------|
| `id` | int | ID danh mục |
| `name` | string | Tên danh mục |
| `description` | string | Mô tả danh mục |
| `productCount` | int | Số sản phẩm trong danh mục |
| `level` | int | Cấp độ danh mục |
| `parentId` | int | ID danh mục cha |
| `order` | int | Thứ tự hiển thị |
| `status` | string | Trạng thái danh mục |
| `slug` | string | URL slug |
| `metaTitle` | string | Meta title SEO |
| `metaDescription` | string | Meta description SEO |
| `metaKeywords` | string | Meta keywords SEO |

## 🏗️ Architecture

### ProductModule Class

```php
class ProductModule
{
    /**
     * Tìm kiếm sản phẩm theo các tiêu chí
     *
     * @param array $criteria Các tiêu chí tìm kiếm
     * @return Collection Collection các sản phẩm tìm được
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
     */
    public function search(array $criteria = []): Collection;

    /**
     * Lấy chi tiết sản phẩm theo ID
     *
     * @param int $productId ID sản phẩm trên Nhanh.vn
     * @return Product|null Sản phẩm chi tiết hoặc null nếu không tìm thấy
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy chi tiết
     */
    public function detail(int $productId): ?Product;

    /**
     * Lấy danh mục sản phẩm từ API Nhanh.vn
     *
     * @return array Mảng các danh mục sản phẩm
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy danh mục
     */
    public function getCategories(): array;
}
```

### Memory Management

Module tự động quản lý memory thông qua helper methods:

```php
// Tạo entities từ API response với memory management
$products = $this->createEntitiesFromApiResponse($response, 'createProduct', 'products');

// Tạo entities từ cached data với memory management
$categories = $this->createEntitiesWithMemoryManagement($cachedData, 'createProductCategories');
```

## 🚨 Error Handling

### ApiException
```php
try {
    $product = $client->products()->detail($productId);
} catch (ApiException $e) {
    echo "API Error: " . $e->getMessage();
    echo "HTTP Status: " . $e->getHttpStatusCode();
    echo "Response Body: " . $e->getResponseBody();
    echo "Error Code: " . $e->getErrorCode();
    echo "Error Data: " . json_encode($e->getErrorData());
}
```

### Error Response Structure
```json
{
    "code": 0,
    "messages": ["Invalid product ID"],
    "errorCode": "INVALID_PRODUCT",
    "errorData": {
        "productId": 5003206,
        "reason": "Product not found"
    }
}
```

## 🔍 Logging

### Debug Logging
```php
// Cấu hình logger
$client->setLogger($monologAdapter);

// Logs sẽ được ghi tự động:
// - Search criteria
// - API calls
// - Response parsing
// - Entity creation
// - Memory management
```

### Log Levels
- **DEBUG**: Chi tiết API calls và responses
- **INFO**: Thông tin hoạt động bình thường
- **WARNING**: Cảnh báo về data không hợp lệ
- **ERROR**: Lỗi xảy ra trong quá trình xử lý

## 💡 Best Practices

### 1. Sử dụng Collection methods
```php
$products = $client->products()->search(['limit' => 100]);

// Filter sản phẩm có sẵn
$availableProducts = $products->filter(function($product) {
    return $product->getAvailableQuantity() > 0;
});

// Map để lấy thông tin cần thiết
$productInfo = $products->map(function($product) {
    return [
        'id' => $product->getIdNhanh(),
        'name' => $product->getName(),
        'price' => $product->getFormattedPrice()
    ];
});
```

### 2. Error handling
```php
try {
    $product = $client->products()->detail($productId);
    if (!$product) {
        echo "Không tìm thấy sản phẩm";
        return;
    }
    // Xử lý sản phẩm
} catch (ApiException $e) {
    // Xử lý lỗi API
    logError($e);
} catch (Exception $e) {
    // Xử lý lỗi chung
    logError($e);
}
```

### 3. Memory optimization
```php
// Sử dụng helper methods để tự động quản lý memory
$products = $this->createEntitiesFromApiResponse($response, 'createProduct');

// Không cần unset() thủ công - đã được xử lý tự động
```

## 📚 Related Documentation

- [OAuth API](oauth.md) - Xác thực và authorization
- [Error Handling](errors.md) - Xử lý lỗi và exceptions
- [Logging](logging.md) - Cấu hình và sử dụng logging
- [Memory Management](memory.md) - Quản lý memory trong SDK
