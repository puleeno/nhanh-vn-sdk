# Order Module APIs

## Danh sách API đã implement

### ✅ Tìm kiếm đơn hàng
- **Endpoint**: `/api/order/index`
- **Method**: `search()`, `getAll()`, `getByPage()`
- **Chức năng**: Tìm kiếm đơn hàng với các bộ lọc khác nhau
- **Hỗ trợ**: Tối đa 100 đơn hàng mỗi page, giới hạn 10 ngày
- **Entities**: `OrderSearchRequest`, `OrderSearchResponse`
- **Validation**: Kiểm tra các tham số tìm kiếm
- **Example**: `examples/public/get_orders.php`

### ✅ Thêm đơn hàng mới
- **Endpoint**: `/api/order/add`
- **Method**: `add()`
- **Chức năng**: Tạo mới đơn hàng với đầy đủ tùy chọn vận chuyển và thanh toán
- **Hỗ trợ**: Đơn hàng vận chuyển, tại cửa hàng, đặt trước
- **Entities**: `OrderAddRequest`, `OrderAddResponse`
- **Validation**: Kiểm tra các trường bắt buộc và business rules
- **Example**: `examples/public/add_order.php`

### ✅ Cập nhật đơn hàng
- **Endpoint**: `/api/order/update`
- **Method**: `update()`, `updateStatus()`, `updatePayment()`, `sendToCarrier()`
- **Chức năng**: Cập nhật trạng thái, thanh toán, gửi sang hãng vận chuyển
- **Hỗ trợ**: Status updates, payment confirmation, carrier integration
- **Entities**: `OrderUpdateRequest`, `OrderUpdateResponse`
- **Validation**: Kiểm tra trạng thái hợp lệ và điều kiện thay đổi
- **Example**: `examples/public/update_order.php`

## Danh sách API chưa implement

### ❌ Xóa đơn hàng
- **Endpoint**: `/api/order/delete`
- **Method**: `delete()`
- **Chức năng**: Xóa đơn hàng khỏi hệ thống
- **Status**: Cần implement

### ❌ Lấy chi tiết đơn hàng
- **Endpoint**: `/api/order/detail`
- **Method**: `getById()`, `getByCode()`
- **Chức năng**: Lấy thông tin chi tiết đơn hàng
- **Status**: Cần implement

### ❌ Quản lý trạng thái đơn hàng
- **Endpoint**: `/api/order/status`
- **Method**: `getStatuses()`, `updateStatus()`
- **Chức năng**: Quản lý workflow trạng thái đơn hàng
- **Status**: Cần implement

### ❌ Thống kê đơn hàng
- **Endpoint**: `/api/order/statistics`
- **Method**: `getStatistics()`
- **Chức năng**: Lấy thống kê về đơn hàng
- **Status**: Cần implement

## Tổng quan implementation

### Đã hoàn thành
- ✅ **3/7 APIs** đã được implement
- ✅ **Core architecture** với DTO pattern
- ✅ **Validation logic** cho request/response
- ✅ **Error handling** với custom exceptions
- ✅ **Logging** integration
- ✅ **Memory management** với automatic cleanup
- ✅ **Documentation** và examples

### Cần implement
- ❌ **4/7 APIs** còn lại
- ❌ **Actual API calls** đến Nhanh.vn endpoints
- ❌ **Response parsing** cho các API mới
- ❌ **Additional validation** rules nếu cần

## Cấu trúc code

### Entities
- `Order` - Entity chính cho đơn hàng
- `OrderSearchRequest` - DTO cho request tìm kiếm
- `OrderSearchResponse` - Entity cho response tìm kiếm
- `OrderAddRequest` - DTO cho request thêm đơn hàng
- `OrderAddResponse` - Entity cho response thêm đơn hàng
- `OrderUpdateRequest` - DTO cho request cập nhật đơn hàng
- `OrderUpdateResponse` - Entity cho response cập nhật đơn hàng

### Services
- `OrderService::searchOrders()` - Logic tìm kiếm đơn hàng
- `OrderService::addOrder()` - Logic thêm đơn hàng mới
- `OrderService::updateOrder()` - Logic cập nhật đơn hàng

### Managers
- `OrderManager::searchOrders()` - Orchestration cho tìm kiếm
- `OrderManager::addOrder()` - Orchestration cho thêm đơn hàng
- `OrderManager::updateOrder()` - Orchestration cho cập nhật đơn hàng

### Modules
- `OrderModule::search()` - API tìm kiếm đơn hàng
- `OrderModule::add()` - API thêm đơn hàng mới
- `OrderModule::update()` - API cập nhật đơn hàng
- `OrderModule::updateStatus()` - API cập nhật trạng thái
- `OrderModule::updatePayment()` - API cập nhật thanh toán
- `OrderModule::sendToCarrier()` - API gửi sang hãng vận chuyển

## Tính năng đặc biệt

### Memory Management
- **Automatic cleanup**: Sử dụng `unset()` để giải phóng memory
- **Helper methods**: `createEntitiesWithMemoryManagement()`, `createEntitiesFromApiResponse()`
- **Batch processing**: Xử lý dữ liệu theo batch để tối ưu memory

### Validation
- **Required fields**: orderId, customerId, products
- **Business rules**: Trạng thái hợp lệ, điều kiện thay đổi
- **Date validation**: Giới hạn 10 ngày cho tìm kiếm
- **Status validation**: Workflow trạng thái đơn hàng

### Logging
- **Debug level**: Method calls và parameters
- **Info level**: API calls và responses
- **Warning level**: Validation failures
- **Error level**: Exceptions và errors

## Next Steps

1. **Implement remaining APIs** theo thứ tự ưu tiên
2. **Add comprehensive tests** cho tất cả methods
3. **Enhance error handling** với specific error codes
4. **Add rate limiting** cho API calls
5. **Implement caching** cho responses
6. **Add monitoring** và metrics
7. **Enhance validation** rules
8. **Add bulk operations** cho performance
