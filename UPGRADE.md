# Upgrade Guide - Nhanh.vn PHP SDK

## Nâng cấp lên 1.0.0 (Nhanh API v3)

### Tổng quan thay đổi

- **Mặc định API v3.0** (`ClientConfig`, `NhanhClientBuilder`, env `NHANH_API_VERSION` mặc định `3.0`).
- **OAuth v3**: authorize URL bắt buộc `appId, service, verifyCode, returnLink` (không dùng `version`, `businessId` trên URL). Đổi token qua `POST /v3.0/app/getaccesstoken?appId=...` (header `Authorization: <secretKey>`, body `{accessCode, verifyCode}`).
- **HTTP transport v3**: `POST {base}{/v3.0/...}?appId=...&businessId=...`, header `Authorization: <accessToken>`, `Content-Type: application/json`, body JSON. `HttpService::callApi()` ném `ApiException` khi `code != 1`.
- **Modules migrated**: OAuth, Product (`/v3.0/product/list|detail|add|category`), Order (`/v3.0/order/list|add|edit`), Customer (`/v3.0/customer/list|add`), Shipping (`/v3.0/shipping/location|carrier|fee`).

### Bước 1: Cập nhật dependency/cấu hình

```php
$client = NhanhClientBuilder::create()
    ->withAppId('APP_ID')
    ->withBusinessId('BUSINESS_ID')
    ->withAccessToken('ACCESS_TOKEN')
    ->withApiVersion('3.0')
    ->build();
```

Đảm bảo `returnLink` OAuth là HTTPS và trùng khớp tuyệt đối với Redirect URL đã đăng ký trên app Nhanh.

### Bước 2: Cập nhật OAuth flow

```php
$verifyCode = bin2hex(random_bytes(16)); // 12-64 [a-zA-Z0-9]
save_verify_code($verifyCode);
$authUrl = $client->getOAuthUrl($returnLink, $verifyCode, 'pos'); // service: pos|ship
redirect($authUrl);

// callback: kiểm tra verifyCode khớp
$token = $client->exchangeAccessCode($_GET['accessCode'], $verifyCode);
```

`getOAuthUrl` và `exchangeAccessCode` đã hỗ trợ tham số `verifyCode` (v3).

### Bước 3: Request/response v3

- **List endpoints**: gửi `{filters, paginator, dataOptions}`; response `code=1`, `data` (mảng), `paginator.next` (nếu còn trang).
- **Add endpoints**: một số API nhận **mảng** object (vd `customer/add`, `product/add`).
- **Gọi endpoint bất kỳ**:

```php
$res = $client->getHttpService()->callApi('/v3.0/inventory/add', [
    'filters' => [...],
    'products' => [...],
]);
```

### Breaking/behavior notes

- URL authorize **không còn** `version`/`businessId`; thiếu `service` hoặc `verifyCode` sẽ bị Nhanh từ chối (`ERR_INVALID_DATA`).
- `accessToken` v3 gắn với `appId+businessId`; tạo token mới sẽ khiến token cũ hết hiệu lực sau ~15 phút (theo spec).
- `HttpService` gửi JSON + header auth theo v3 (không còn multipart `/api/...` cho v3). Endpoint không có prefix `/v3.0/` sẽ fallback legacy `/api/...` (tương thích hạn chế).
- `CustomerModule::add()` v3 gửi body là mảng; `addBatch()` giới hạn 100 (spec). `search()` nhận `filters/paginator` hoặc map một số key cũ.
- `ShippingModule` dùng `/v3.0/shipping/location|carrier|fee` (fee `filters.type` 1=Nhanh, 2=tài khoản riêng). `searchCities/searchDistricts/searchWards` có thêm `locationVersion` (mặc định `v1`).
- Entity/Manager/Repository/Service legacy chưa được chuẩn hóa hoàn toàn theo schema v3 — kiểm thử và map bổ sung theo endpoint thực tế.
- SDK chỉnh sửa trong `vendor/` có thể bị `composer` ghi đè; nên commit/pin bản v3.

### Từ 0.3.x lên 0.4.0 (legacy)

Xem lịch sử git tag `v0.4.0` cho hướng dẫn cũ (namespace `Puleeno\NhanhVn`, PHP 8.1+, Builder pattern).
