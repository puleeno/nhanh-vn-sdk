# Shipping API Documentation

## Tổng quan

Shipping API cung cấp các chức năng liên quan đến vận chuyển và địa điểm, bao gồm:
- Lấy danh sách địa điểm (thành phố, quận huyện, phường xã)
- Quản lý hãng vận chuyển
- Tính phí vận chuyển

## Endpoints

### 1. Lấy danh sách địa điểm

**Endpoint:** `/api/shipping/location`

**Method:** `POST`

**Mô tả:** Lấy danh sách thành phố, quận huyện, phường xã từ Nhanh.vn. Các API thêm đơn hàng, tính phí vận chuyển sẽ cần sử dụng đến các dữ liệu này.

**Lưu ý:** Dữ liệu thành phố, quận huyện rất ít khi bị thay đổi (chỉ xảy ra khi có thay đổi tên, chia tách hoặc gộp 1 vài thành phố, quận huyện), bạn có thể cache dữ liệu trên hệ thống của bạn để giảm tải việc phải gọi API liên tục, thời gian cache khuyến cáo là 24h.

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `type` | string | No | Loại địa điểm: `CITY`, `DISTRICT`, `WARD` (mặc định là `CITY`) |
| `parentId` | int | No | Nếu `type = DISTRICT` thì `parentId = id` của thành phố cần lấy ra danh sách quận huyện. Nếu `type = WARD` thì `parentId = id` của quận huyện cần lấy ra. |

#### Request Examples

**Lấy danh sách thành phố:**
```json
{
    "type": "CITY"
}
```

**Lấy danh sách quận huyện của Hà Nội (ID = 2):**
```json
{
    "type": "DISTRICT",
    "parentId": 2
}
```

**Lấy danh sách phường xã của quận Hoàn Kiếm (ID = 3):**
```json
{
    "type": "WARD",
    "parentId": 3
}
```

#### Response Structure

**Success Response (code = 1):**
```json
{
    "code": 1,
    "messages": [],
    "data": [
        {
            "id": 2,
            "name": "Hà Nội"
        },
        {
            "id": 3,
            "name": "Hồ Chí Minh"
        }
    ]
}
```

**District Response:**
```json
{
    "code": 1,
    "messages": [],
    "data": [
        {
            "id": 2,
            "parentId": 2,
            "name": "Quận Hoàn Kiếm"
        },
        {
            "id": 6,
            "parentId": 2,
            "name": "Quận Hai Bà Trưng"
        }
    ]
}
```

**Ward Response:**
```json
{
    "code": 1,
    "messages": [],
    "data": [
        {
            "id": 2,
            "parentId": 3,
            "name": "Phường Bạch Đằng"
        },
        {
            "id": 6,
            "parentId": 3,
            "name": "Phường Bách Khoa"
        }
    ]
}
```

**Error Response (code = 0):**
```json
{
    "code": 0,
    "messages": [
        "ID địa điểm cha không được để trống khi tìm kiếm district"
    ],
    "data": []
}
```

#### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `code` | int | 1 = success, 0 = failed |
| `messages` | array | Mảng thông báo lỗi nếu code = 0 |
| `data` | array | Mảng danh sách địa điểm |

**Data Object Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | int | ID của địa điểm |
| `name` | string | Tên địa điểm |
| `parentId` | int | ID của địa điểm cha (chỉ có với DISTRICT và WARD) |

#### Usage Examples

**Sử dụng SDK:**

```php
// Lấy danh sách thành phố
$cities = $client->shipping()->searchCities();

// Lấy danh sách quận huyện của Hà Nội
$districts = $client->shipping()->searchDistricts(2);

// Lấy danh sách phường xã của quận Hoàn Kiếm
$wards = $client->shipping()->searchWards(3);

// Tìm kiếm theo criteria
$searchCriteria = [
    'type' => 'DISTRICT',
    'parentId' => 2
];
$results = $client->shipping()->search($searchCriteria);

// Tìm kiếm theo tên
$results = $client->shipping()->searchByName("Hà", "CITY");

// Tìm kiếm theo ID
$location = $client->shipping()->findById(2, "CITY");
```

**Validation:**

```php
// Validate dữ liệu tìm kiếm
$isValid = $client->shipping()->validateSearchData([
    'type' => 'DISTRICT',
    'parentId' => 2
]);

// Lấy danh sách lỗi validation
$errors = $client->shipping()->getValidationErrors([
    'type' => 'DISTRICT'
    // Thiếu parentId
]);
```

#### Error Codes

| Error Message | Description | Solution |
|---------------|-------------|----------|
| `Loại địa điểm không hợp lệ. Chỉ chấp nhận: CITY, DISTRICT, WARD` | Parameter `type` không đúng định dạng | Sử dụng một trong các giá trị: `CITY`, `DISTRICT`, `WARD` |
| `ID địa điểm cha không được để trống khi tìm kiếm district` | Thiếu `parentId` khi tìm kiếm `DISTRICT` | Thêm `parentId` với ID của thành phố |
| `ID địa điểm cha không được để trống khi tìm kiếm ward` | Thiếu `parentId` khi tìm kiếm `WARD` | Thêm `parentId` với ID của quận huyện |

#### Best Practices

1. **Cache Management:** Cache dữ liệu địa điểm trong 24 giờ để giảm tải API calls
2. **Validation:** Luôn validate dữ liệu trước khi gửi request
3. **Error Handling:** Xử lý lỗi một cách graceful và hiển thị thông báo phù hợp cho người dùng
4. **Type Safety:** Sử dụng constants cho các giá trị `type` thay vì hardcode strings

#### Related APIs

- [Order API](./order.md) - Sử dụng địa điểm để tạo đơn hàng
- [Shipping Carrier API](./shipping-carrier.md) - Lấy danh sách hãng vận chuyển
- [Shipping Fee API](./shipping-fee.md) - Tính phí vận chuyển dựa trên địa điểm
