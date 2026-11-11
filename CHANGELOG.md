# Changelog

Tất cả các thay đổi quan trọng trong dự án này sẽ được ghi lại trong file này.

Format dựa trên [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
và dự án này tuân thủ [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-11-11

### Added
- Hỗ trợ **Nhanh.vn Open API v3** (mặc định `apiVersion=3.0`).
- OAuth v3: authorize URL `https://nhanh.vn/oauth?appId&service&verifyCode&returnLink` (service `pos|ship`, `verifyCode` 12-64 `[a-zA-Z0-9]`).
- Đổi token v3: `POST /v3.0/app/getaccesstoken?appId=...` với header `Authorization: <secretKey>` và body `{accessCode, verifyCode}`; chuẩn hóa `data.accessToken/businessId`.
- `HttpService` v3: `POST /v3.0/...` (JSON), query `appId,businessId`, header `Authorization: <accessToken>`; parse lỗi `code/errorCode/messages` (string/array/field map); helper `isV3()`, hỗ trợ inject Guzzle client cho test.
- `ClientConfig`: `oauthService`, `verifyCode`, `getOAuthUrl(?verifyCode, ?service)`, getters/setters; mặc định domain `https://pos.open.nhanh.vn`.
- Module v3: Customer `/v3.0/customer/list|add`, Shipping `/v3.0/shipping/location|carrier|fee` (type 1/2), Product `/v3.0/product/list|detail|add|category`, Order `/v3.0/order/list|add|edit`.
- Unit tests (PHPUnit 10) + `phpunit.xml`: ClientConfig, HttpService, OAuthService, OAuth/Customer/Shipping/Order/Product modules, NhanhClientBuilder defaults.

### Changed
- `NhanhClientBuilder`/env default `NHANH_API_VERSION` -> `3.0`.
- `OAuthModule`/`NhanhVnClient`/`OAuthService`: ký hiệu `getOAuthUrl(returnLink, verifyCode, service)` và `exchangeAccessCode(accessCode, verifyCode)`.
- `ProductModule::search`/`getCategories` và các module list xử lý response v3 (`data[]`, `paginator.next`) tương thích flow hiện có.
- Tài liệu: `README.md`, `UPGRADE.md` cập nhật hướng dẫn sử dụng v3.

### Notes
- Nhiều endpoint POS nâng cao chưa có wrapper module riêng — có thể gọi qua `HttpService::callApi('/v3.0/...', $payload)` theo spec.
- Entities/Managers/Repositories/Services legacy chưa chuẩn hóa hoàn toàn theo schema v3.

## [0.4.0] - 2024-12-19

### Added
- **Nhanh Client Builder** (Builder Pattern, fluent interface, presets, OAuth support, validation).
- **Order Module** (search/add/update, status/payment/carrier helpers, cache).
- **Shipping Module** (location 3 cấp, carriers, fee) và cache/memory improvements.
- Chuẩn hóa modules theo kiến trúc ProductModule, tài liệu và examples mở rộng.

### Fixed
- Memory/cache handling, validation và error handling ở nhiều module.

## [0.3.0] - 2024-11-15

### Added
- Product/Customer modules cơ bản, OAuth 2.0, core architecture (Client/Config/HTTP/Entities/Repositories).

## [0.2.0] - 2024-10-01

### Added
- Core architecture, configuration, HTTP service, repository pattern.

## [0.1.0] - 2024-09-01

### Added
- Khởi tạo project, cấu trúc cơ bản, composer, tài liệu ban đầu.
