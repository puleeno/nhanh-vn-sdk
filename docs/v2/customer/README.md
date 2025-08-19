# Customer Module APIs

## Danh sách API đã implement

### ✅ Danh sách khách hàng
- **Endpoint**: `/api/customer/search`
- **Method**: `search()`, `getAll()`, `getByType()`, `getByDateRange()`
- **Chức năng**: Tìm kiếm khách hàng với các bộ lọc khác nhau
- **Hỗ trợ**: Tối đa 50 khách hàng mỗi page
- **Entities**: `CustomerSearchRequest`, `CustomerSearchResponse`
- **Validation**: Kiểm tra các tham số tìm kiếm
- **Example**: `examples/public/search_customers.php`

### ✅ Thêm khách hàng
- **Endpoint**: `/api/customer/add`
- **Method**: `add()` và `addBatch()`
- **Chức năng**: Tạo mới hoặc cập nhật khách hàng trên Nhanh.vn
- **Hỗ trợ**: Tối đa 50 khách hàng mỗi request
- **Entities**: `CustomerAddRequest`, `CustomerAddResponse`
- **Validation**: Kiểm tra các trường bắt buộc (name, mobile)
- **Example**: `examples/public/add_customer.php`

## Danh sách API chưa implement

### ❌ Cập nhật khách hàng
- **Endpoint**: `/api/customer/update`
- **Method**: `update()`
- **Chức năng**: Cập nhật thông tin khách hàng
- **Status**: Cần implement

### ❌ Xóa khách hàng
- **Endpoint**: `/api/customer/delete`
- **Method**: `delete()`
- **Chức năng**: Xóa khách hàng khỏi hệ thống
- **Status**: Cần implement

### ❌ Lấy chi tiết khách hàng
- **Endpoint**: `/api/customer/detail`
- **Method**: `getById()`, `getByMobile()`
- **Chức năng**: Lấy thông tin chi tiết khách hàng
- **Status**: Cần implement

### ❌ Quản lý nhóm khách hàng
- **Endpoint**: `/api/customer/group`
- **Method**: `getGroups()`, `addToGroup()`, `removeFromGroup()`
- **Chức năng**: Quản lý nhóm khách hàng
- **Status**: Cần implement

### ❌ Lịch sử mua hàng
- **Endpoint**: `/api/customer/orderhistory`
- **Method**: `getOrderHistory()`
- **Chức năng**: Lấy lịch sử mua hàng của khách hàng
- **Status**: Cần implement

### ❌ Thống kê khách hàng
- **Endpoint**: `/api/customer/statistics`
- **Method**: `getStatistics()`
- **Chức năng**: Lấy thống kê về khách hàng
- **Status**: Cần implement

## Tổng quan implementation

### Đã hoàn thành
- ✅ **2/7 APIs** đã được implement
- ✅ **Core architecture** với DTO pattern
- ✅ **Validation logic** cho request/response
- ✅ **Error handling** với custom exceptions
- ✅ **Logging** integration
- ✅ **Memory management** với automatic cleanup
- ✅ **Documentation** và examples

### Tính năng đặc biệt
- **Memory Management**: Tự động cleanup với `unset()`
- **Validation**: Hỗ trợ tiếng Việt với thông báo lỗi chi tiết
- **Cache System**: TTL thông minh cho từng loại dữ liệu
- **Batch Operations**: Hỗ trợ tối đa 50 khách hàng mỗi request

### Cần implement
- ❌ **5/7 APIs** còn lại
- ❌ **Actual API calls** đến Nhanh.vn endpoints
- ❌ **Response parsing** cho các API mới
- ❌ **Additional validation** rules nếu cần

## Cấu trúc code

### Entities
- `Customer` - Entity chính cho khách hàng
- `CustomerSearchRequest` - DTO cho request tìm kiếm
- `CustomerSearchResponse` - Entity cho response tìm kiếm
- `CustomerAddRequest` - DTO cho request thêm khách hàng
- `CustomerAddResponse` - Entity cho response thêm khách hàng

### Services
- `CustomerService::searchCustomers()` - Logic tìm kiếm khách hàng
- `CustomerService::addCustomer()` - Logic thêm khách hàng đơn lẻ
- `CustomerService::addCustomers()` - Logic thêm khách hàng batch

### Managers
- `CustomerManager::searchCustomers()` - Orchestration cho tìm kiếm
- `CustomerManager::addCustomer()` - Orchestration cho thêm khách hàng
- `CustomerManager::addCustomers()` - Orchestration cho thêm batch

### Modules
- `CustomerModule::search()` - API tìm kiếm khách hàng
- `CustomerModule::add()` - API thêm khách hàng đơn lẻ
- `CustomerModule::addBatch()` - API thêm khách hàng batch

## Tính năng đặc biệt

### Memory Management
- **Automatic cleanup**: Sử dụng `unset()` để giải phóng memory
- **Helper methods**: `createEntitiesWithMemoryManagement()`, `createEntitiesFromApiResponse()`
- **Batch processing**: Xử lý dữ liệu theo batch để tối ưu memory

### Validation
- **Required fields**: name, mobile
- **Format validation**: email, mobile, birthday
- **Range validation**: points (không âm)
- **Type validation**: customer type, gender

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
