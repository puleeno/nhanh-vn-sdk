<?php

namespace Puleeno\NhanhVn\Repositories;

use Puleeno\NhanhVn\Entities\Order\Order;
use Puleeno\NhanhVn\Entities\Order\OrderSearchRequest;
use Puleeno\NhanhVn\Entities\Order\OrderSearchResponse;
use Puleeno\NhanhVn\Entities\Order\OrderUpdateRequest;
use Puleeno\NhanhVn\Entities\Order\OrderUpdateResponse;

/**
 * Order Repository - Quản lý việc tạo Order entities
 *
 * Repository này chịu trách nhiệm tạo các Order entities
 * từ dữ liệu thô và quản lý việc chuyển đổi dữ liệu
 */
class OrderRepository
{
    /**
     * Tạo OrderSearchRequest từ dữ liệu tìm kiếm
     *
     * @param array $searchData Dữ liệu tìm kiếm
     * @return OrderSearchRequest Request đã được tạo
     */
    public function createOrderSearchRequest(array $searchData): OrderSearchRequest
    {
        return new OrderSearchRequest($searchData);
    }

    /**
     * Tạo OrderSearchResponse từ dữ liệu API
     *
     * @param array $responseData Dữ liệu response từ API
     * @return OrderSearchResponse Response đã được tạo
     */
    public function createOrderSearchResponse(array $responseData): OrderSearchResponse
    {
        return new OrderSearchResponse($responseData);
    }

    /**
     * Tạo OrderUpdateRequest từ dữ liệu cập nhật
     *
     * @param array $updateData Dữ liệu cập nhật đơn hàng
     * @return OrderUpdateRequest Request đã được tạo
     */
    public function createOrderUpdateRequest(array $updateData): OrderUpdateRequest
    {
        return new OrderUpdateRequest($updateData);
    }

    /**
     * Tạo OrderUpdateResponse từ dữ liệu API
     *
     * @param array $responseData Dữ liệu response từ API
     * @return OrderUpdateResponse Response đã được tạo
     */
    public function createOrderUpdateResponse(array $responseData): OrderUpdateResponse
    {
        return OrderUpdateResponse::createFromApiResponse($responseData);
    }

    /**
     * Tạo Order entity từ dữ liệu
     *
     * @param array $orderData Dữ liệu đơn hàng
     * @return Order Order entity đã được tạo
     */
    public function createOrder(array $orderData): Order
    {
        return new Order($orderData);
    }

    /**
     * Tạo nhiều Order entities từ dữ liệu
     *
     * @param array $ordersData Mảng dữ liệu đơn hàng
     * @return array Mảng Order entities đã được tạo
     */
    public function createOrders(array $ordersData): array
    {
        $orders = [];

        foreach ($ordersData as $orderData) {
            try {
                $orders[] = $this->createOrder($orderData);
            } catch (\Exception $e) {
                // Skip invalid order data
                // TODO: Add proper logging
            }
        }

        return $orders;
    }

    /**
     * Tạo OrderSearchResponse trống
     *
     * @return OrderSearchResponse Response trống
     */
    public function createEmptySearchResponse(): OrderSearchResponse
    {
        return new OrderSearchResponse([
            'totalPages' => 0,
            'totalRecords' => 0,
            'page' => 1,
            'orders' => []
        ]);
    }

    /**
     * Validate dữ liệu tìm kiếm đơn hàng
     *
     * @param array $searchData Dữ liệu cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateSearchData(array $searchData): bool
    {
        try {
            $request = $this->createOrderSearchRequest($searchData);
            return $request->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate dữ liệu cập nhật đơn hàng
     *
     * @param array $updateData Dữ liệu cần validate
     * @return bool True nếu hợp lệ, false nếu không hợp lệ
     */
    public function validateUpdateData(array $updateData): bool
    {
        try {
            $request = $this->createOrderUpdateRequest($updateData);
            return $request->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Lấy danh sách lỗi validation
     *
     * @param array $searchData Dữ liệu cần validate
     * @return array Mảng chứa các lỗi validation
     */
    public function getSearchValidationErrors(array $searchData): array
    {
        try {
            $request = $this->createOrderSearchRequest($searchData);
            return $request->getErrors();
        } catch (\Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách lỗi validation cho cập nhật đơn hàng
     *
     * @param array $updateData Dữ liệu cần validate
     * @return array Mảng chứa các lỗi validation
     */
    public function getUpdateValidationErrors(array $updateData): array
    {
        try {
            $request = $this->createOrderUpdateRequest($updateData);
            return $request->getErrors();
        } catch (\Exception $e) {
            return ['general' => $e->getMessage()];
        }
    }

    /**
     * Chuẩn bị dữ liệu tìm kiếm cho API
     *
     * @param array $searchData Dữ liệu tìm kiếm từ người dùng
     * @return array Dữ liệu đã được format cho API
     */
    public function prepareSearchData(array $searchData): array
    {
        $request = $this->createOrderSearchRequest($searchData);

        // Tự động set date range nếu cần
        $request->setDefaultDateRange();

        return $request->toApiFormat();
    }

    /**
     * Chuẩn bị dữ liệu cập nhật cho API
     *
     * @param array $updateData Dữ liệu cập nhật từ người dùng
     * @return array Dữ liệu đã được format cho API
     */
    public function prepareUpdateData(array $updateData): array
    {
        $request = $this->createOrderUpdateRequest($updateData);
        return $request->toApiFormat();
    }
}
