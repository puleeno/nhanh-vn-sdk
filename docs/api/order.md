# Order API

## Tổng quan

Order API cung cấp các chức năng để quản lý đơn hàng từ Nhanh.vn, bao gồm tìm kiếm, lọc và phân tích đơn hàng.

## Endpoints

### 1. Tìm kiếm đơn hàng

**Endpoint:** `/api/order/index`

**Method:** `POST`

**Mô tả:** Lấy danh sách đơn hàng theo các tiêu chí tìm kiếm.

**Lưu ý quan trọng:**
- Hệ thống chỉ hỗ trợ lấy đơn hàng trong 10 ngày
- Nếu không truyền `fromDate`, mặc định sẽ lấy 10 ngày gần nhất
- Nếu lọc theo `id`, `customerId` hoặc `customerMobile` thì có thể bỏ qua việc bắt buộc lọc `fromDate`, `toDate`
- Sử dụng `updatedDateTimeFrom` và `updatedDateTimeTo` để lấy đơn hàng có cập nhật mới (cũng bị giới hạn 10 ngày)

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `page` | int | No | Trang hiện tại (mặc định: 1) |
| `limit` | int | No | Số lượng đơn hàng trên 1 trang (mặc định: 100, tối đa: 100) |
| `fromDate` | string | No | Ngày tạo đơn hàng từ (định dạng: Y-m-d) |
| `toDate` | string | No | Ngày tạo đơn hàng đến (định dạng: Y-m-d) |
| `id` | int | No | ID đơn hàng trên Nhanh.vn |
| `customerMobile` | string | No | Số điện thoại khách hàng |
| `customerId` | int | No | ID khách hàng |
| `statuses` | array | No | Mảng trạng thái đơn hàng |
| `fromDeliveryDate` | string | No | Ngày giao hàng từ (định dạng: Y-m-d) |
| `toDeliveryDate` | string | No | Ngày giao hàng đến (định dạng: Y-m-d) |
| `carrierId` | int | No | ID hãng vận chuyển |
| `carrierCode` | string | No | Mã vận đơn hãng vận chuyển |
| `type` | int | No | Loại đơn hàng |
| `customerCityId` | int | No | Mã thành phố khách hàng |
| `customerDistrictId` | int | No | Mã quận/huyện khách hàng |
| `handoverId` | int | No | ID biên bản bàn giao |
| `depotId` | int | No | ID kho hàng |
| `updatedDateTimeFrom` | string | No | Thời gian cập nhật từ (định dạng: Y-m-d H:i:s) |
| `updatedDateTimeTo` | string | No | Thời gian cập nhật đến (định dạng: Y-m-d H:i:s) |
| `dataOptions` | array | No | Lựa chọn dữ liệu cần lấy thêm |

#### Loại đơn hàng (type)

| ID | Tên | Mô tả |
|----|-----|-------|
| 1 | Giao hàng tận nhà | Đơn hàng giao đến địa chỉ khách hàng |
| 2 | Mua tại quầy | Đơn hàng mua trực tiếp tại cửa hàng |
| 3 | Đặt trước | Đơn hàng đặt trước sản phẩm |
| 4 | Dùng thử | Đơn hàng dùng thử sản phẩm |
| 5 | Đổi quà | Đơn hàng đổi quà |
| 10 | Xin báo giá | Đơn hàng xin báo giá |
| 12 | Đổi sản phẩm | Đơn hàng đổi sản phẩm |
| 14 | Khách trả lại hàng | Đơn hàng khách trả lại |
| 15 | Hàng chuyển kho | Đơn hàng chuyển kho |
| 16 | Đơn hoàn một phần | Đơn hàng hoàn một phần |
| 17 | Đền bù mất hàng | Đơn hàng đền bù mất hàng |

#### Kênh bán hàng (saleChannel)

| ID | Tên | Mô tả |
|----|-----|-------|
| 1 | Admin | Quản trị viên |
| 2 | Website | Website chính thức |
| 10 | API | Tích hợp qua API |
| 20 | Facebook | Tạo từ vpage.nhanh.vn |
| 21 | Instagram | Tạo từ vpage.nhanh.vn |
| 41 | Lazada.vn | Sàn thương mại điện tử |
| 42 | Shopee.vn | Sàn thương mại điện tử |
| 43 | Sendo.vn | Sàn thương mại điện tử |
| 45 | Tiki.vn | Sàn thương mại điện tử |
| 46 | Zalo Shop | Zalo Shop |
| 47 | 1Landing.vn | Landing page |
| 48 | Tiktok Shop | Tiktok Shop |
| 49 | Zalo OA | Zalo Official Account |
| 50 | Shopee Chat | Chat Shopee |
| 51 | Lazada Chat | Chat Lazada |
| 52 | Zalo cá nhân | Zalo cá nhân |

#### Data Options

| Option | Mô tả |
|--------|-------|
| `giftProducts` | Lấy thông tin quà tặng của sản phẩm |
| `marketingUtm` | Lấy thông tin UTM (utmSource, utmMedium, utmCampaign) |
| `productBatchs` | Lấy thông tin sản phẩm lô (tên lô, ngày hết hạn) |
| `comboItems` | Lấy thông tin sản phẩm combo |

#### Response

```json
{
    "code": 1,
    "messages": [],
    "data": {
        "totalPages": 5,
        "totalRecords": 500,
        "page": 1,
        "orders": {
            "orderId1": {
                "id": 12345,
                "shopOrderId": "WEB001",
                "merchantTrackingNumber": "TN001",
                "handoverId": 1,
                "depotId": 1,
                "depotName": "Kho Hà Nội",
                "typeId": 1,
                "type": "Shipping",
                "moneyDiscount": 50000,
                "moneyDeposit": 0,
                "moneyTransfer": 0,
                "usedPoints": 0,
                "moneyUsedPoints": 0,
                "usedPointAmount": 0,
                "serviceId": 1,
                "carrierId": 1,
                "carrierServiceType": 10,
                "carrierServiceTypeName": "Nhanh",
                "carrierCode": "GHN001",
                "carrierName": "Giao Hang Nhanh",
                "carrierServiceName": "Giao hàng nhanh",
                "shipFee": 30000,
                "codFee": 0,
                "declaredFee": 0,
                "customerShipFee": 30000,
                "returnFee": 0,
                "overWeightShipFee": 0,
                "description": "Ghi chú khách hàng",
                "privateDescription": "Ghi chú nội bộ",
                "customerId": 1001,
                "customerName": "Nguyễn Văn A",
                "customerMobile": "0987654321",
                "customerEmail": "nguyenvana@email.com",
                "customerAddress": "123 Đường ABC, Quận 1",
                "customerCityId": 1,
                "customerCity": "Hà Nội",
                "customerDistrictId": 1,
                "customerDistrict": "Ba Đình",
                "createdById": 1,
                "createdByName": "Nhân viên A",
                "createdDateTime": "2024-01-15 10:30:00",
                "deliveryDate": "2024-01-17",
                "statusCode": "pending",
                "statusName": "Chờ xử lý",
                "calcTotalMoney": 1500000,
                "trafficSourceId": 1,
                "trafficSourceName": "Website",
                "saleId": 1,
                "saleName": "Nhân viên bán hàng A",
                "returnFromOrderId": 0,
                "affiliateCode": "",
                "affiliateBonusCash": 0,
                "affiliateBonusPercent": 0,
                "tags": ["VIP", "Khách mới"],
                "saleChannel": 2,
                "ecomShopId": "SHOP001",
                "couponCode": "GIAM10",
                "products": [
                    {
                        "productId": 1001,
                        "productName": "Sản phẩm A",
                        "productCode": "SP001",
                        "productBarcode": "123456789",
                        "price": 1500000,
                        "quantity": 1,
                        "weight": 500,
                        "imei": "",
                        "vat": 10,
                        "discount": 0,
                        "description": "Mô tả sản phẩm",
                        "giftProducts": [],
                        "batch": [],
                        "comboItems": [],
                        "productMoney": 1500000,
                        "priceOriginal": 1500000,
                        "avgCost": 1000000
                    }
                ],
                "utmSource": "google",
                "utmMedium": "cpc",
                "utmCampaign": "brand",
                "facebook": {
                    "pageId": "",
                    "conversationId": "",
                    "adId": "",
                    "postId": "",
                    "psId": ""
                },
                "updatedAt": 1705296600,
                "packed": {
                    "id": 0,
                    "datetime": ""
                },
                "vat": {
                    "value": 10,
                    "amount": 150000,
                    "type": "percent",
                    "taxCode": "",
                    "taxDate": ""
                }
            }
        }
    }
}
```

## Sử dụng trong SDK

### Khởi tạo client

```php
use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;

$config = new ClientConfig([
    'appId' => 'your_app_id',
    'secretKey' => 'your_secret_key',
    'businessId' => 'your_business_id',
    'accessToken' => 'your_access_token'
]);

$client = NhanhVnClient::getInstance($config);
```

### Tìm kiếm đơn hàng cơ bản

```php
// Lấy tất cả đơn hàng (10 ngày gần nhất)
$orders = $client->orders()->getAll();

// Lấy đơn hàng theo trang
$orders = $client->orders()->getAll(page: 2, limit: 50);

// Lấy thông tin đơn hàng
echo "Tổng số đơn hàng: " . $orders->getTotalRecords() . "\n";
echo "Tổng số trang: " . $orders->getTotalPages() . "\n";
echo "Số đơn hàng trang hiện tại: " . $orders->getCurrentPageOrderCount() . "\n";
```

### Tìm kiếm theo tiêu chí

```php
// Tìm kiếm theo ID đơn hàng
$orders = $client->orders()->searchById(12345);

// Tìm kiếm theo số điện thoại khách hàng
$orders = $client->orders()->searchByCustomerMobile('0987654321');

// Tìm kiếm theo ID khách hàng
$orders = $client->orders()->searchByCustomerId(1001);

// Tìm kiếm theo trạng thái
$orders = $client->orders()->getByStatuses(['pending', 'processing']);

// Tìm kiếm theo loại đơn hàng
$orders = $client->orders()->getByType(1); // Giao hàng tận nhà

// Tìm kiếm theo khoảng thời gian
$orders = $client->orders()->getByDateRange('2024-01-01', '2024-01-10');

// Tìm kiếm theo khoảng thời gian giao hàng
$orders = $client->orders()->getByDeliveryDateRange('2024-01-15', '2024-01-20');

// Tìm kiếm theo thời gian cập nhật
$orders = $client->orders()->getByUpdatedDateTimeRange(
    '2024-01-15 00:00:00',
    '2024-01-15 23:59:59'
);
```

### Tìm kiếm nâng cao

```php
// Tìm kiếm với nhiều tiêu chí
$orders = $client->orders()->search([
    'fromDate' => '2024-01-01',
    'toDate' => '2024-01-10',
    'statuses' => ['pending', 'processing'],
    'type' => 1,
    'carrierId' => 1,
    'dataOptions' => ['giftProducts', 'marketingUtm'],
    'page' => 1,
    'limit' => 50
]);
```

### Lọc và phân tích dữ liệu

```php
// Lọc theo trạng thái
$pendingOrders = $orders->filterByStatus('pending');

// Lọc theo loại
$shippingOrders = $orders->filterByType(1);

// Lọc theo khách hàng
$customerOrders = $orders->filterByCustomer(1001);

// Lọc theo khoảng giá
$highValueOrders = $orders->filterByAmountRange(1000000, 5000000);

// Lọc theo ngày tạo
$recentOrders = $orders->filterByCreatedDate('2024-01-15', '2024-01-16');

// Sắp xếp theo tiêu chí
$sortedOrders = $orders->sortOrders('createdDateTime', false); // Giảm dần

// Lấy thống kê
$statusStats = $orders->getStatusStatistics();
$typeStats = $orders->getTypeStatistics();
$channelStats = $orders->getSaleChannelStatistics();

// Tính tổng doanh thu và phí vận chuyển
$totalRevenue = $orders->getTotalRevenue();
$totalShippingFee = $orders->getTotalShippingFee();
```

### Quản lý cache

```php
// Lấy trạng thái cache
$cacheStatus = $client->orders()->getCacheStatus();

// Kiểm tra cache có sẵn không
$hasCache = $client->orders()->isCacheAvailable();

// Xóa cache
$client->orders()->clearCache();
```

### Validation

```php
// Validate dữ liệu tìm kiếm
$searchParams = [
    'fromDate' => '2024-01-01',
    'toDate' => '2024-01-15' // Vượt quá 10 ngày
];

$isValid = $client->orders()->validateSearchRequest($searchParams);

if (!$isValid) {
    $errors = $client->orders()->getSearchRequestErrors($searchParams);
    print_r($errors);
}
```

## Lưu ý quan trọng

1. **Giới hạn thời gian:** Tất cả các khoảng thời gian tìm kiếm đều bị giới hạn trong 10 ngày
2. **Phân trang:** Mặc định 100 đơn hàng/trang, tối đa 100
3. **Cache:** Kết quả tìm kiếm được cache để tăng hiệu suất
4. **Validation:** Tự động validate dữ liệu đầu vào và trả về lỗi chi tiết
5. **Memory management:** Tự động quản lý memory để tránh memory leak

## Error Handling

```php
try {
    $orders = $client->orders()->search($searchParams);
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
    
    // Log lỗi
    $logger->error("Order search failed", [
        'error' => $e->getMessage(),
        'params' => $searchParams
    ]);
}
```

## Performance Tips

1. **Sử dụng cache:** Kết quả tìm kiếm được cache tự động
2. **Giới hạn thời gian:** Chỉ tìm kiếm trong khoảng thời gian cần thiết
3. **Phân trang:** Sử dụng phân trang để tránh load quá nhiều dữ liệu
4. **Data options:** Chỉ lấy những dữ liệu cần thiết thông qua `dataOptions`
5. **Batch processing:** Xử lý dữ liệu theo batch để tối ưu memory
