<?php

namespace Puleeno\NhanhVn\Entities\Order;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Illuminate\Support\Collection;

/**
 * OrderSearchResponse Entity - Response tìm kiếm đơn hàng
 *
 * Entity này chứa kết quả tìm kiếm đơn hàng từ API Nhanh.vn
 * bao gồm: thông tin phân trang, danh sách đơn hàng
 */
class OrderSearchResponse extends AbstractEntity
{
    /** @var int Tổng số trang */
    protected int $totalPages = 0;

    /** @var int Tổng số bản ghi */
    protected int $totalRecords = 0;

    /** @var int Trang hiện tại */
    protected int $page = 1;

    /** @var Collection Danh sách đơn hàng */
    protected Collection $orders;

    /**
     * Constructor
     *
     * @param array $data Dữ liệu response từ API
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        // Khởi tạo orders collection
        $this->orders = new Collection();

        // Parse orders data nếu có
        if (isset($data['orders']) && is_array($data['orders'])) {
            $this->parseOrdersData($data['orders']);
        }
    }

    /**
     * Validate entity data
     */
    protected function validate(): void
    {
        // Validate totalPages
        if ($this->totalPages < 0) {
            $this->addError('totalPages', 'Tổng số trang không được âm');
        }

        // Validate totalRecords
        if ($this->totalRecords < 0) {
            $this->addError('totalRecords', 'Tổng số bản ghi không được âm');
        }

        // Validate page
        if ($this->page < 1) {
            $this->addError('page', 'Trang hiện tại phải lớn hơn 0');
        }

        if ($this->totalPages > 0 && $this->page > $this->totalPages) {
            $this->addError('page', 'Trang hiện tại không được lớn hơn tổng số trang');
        }
    }

    /**
     * Parse dữ liệu đơn hàng từ API response
     *
     * @param array $ordersData Dữ liệu đơn hàng từ API
     */
    private function parseOrdersData(array $ordersData): void
    {
        foreach ($ordersData as $orderId => $orderData) {
            try {
                $order = new Order($orderData);
                $this->orders->put($orderId, $order);
            } catch (\Exception $e) {
                // Log error và skip invalid order data
                // TODO: Add proper logging
            }
        }
    }

    // Getters
    public function getTotalPages(): int { return $this->totalPages; }
    public function getTotalRecords(): int { return $this->totalRecords; }
    public function getPage(): int { return $this->page; }
    public function getOrders(): Collection { return $this->orders; }

    /**
     * Lấy số lượng đơn hàng trong trang hiện tại
     *
     * @return int Số lượng đơn hàng
     */
    public function getCurrentPageOrderCount(): int
    {
        return $this->orders->count();
    }

    /**
     * Kiểm tra có trang tiếp theo không
     *
     * @return bool True nếu có trang tiếp theo
     */
    public function hasNextPage(): bool
    {
        return $this->page < $this->totalPages;
    }

    /**
     * Kiểm tra có trang trước không
     *
     * @return bool True nếu có trang trước
     */
    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    /**
     * Lấy số trang tiếp theo
     *
     * @return int|null Số trang tiếp theo hoặc null nếu không có
     */
    public function getNextPage(): ?int
    {
        return $this->hasNextPage() ? $this->page + 1 : null;
    }

    /**
     * Lấy số trang trước
     *
     * @return int|null Số trang trước hoặc null nếu không có
     */
    public function getPreviousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->page - 1 : null;
    }

    /**
     * Lấy đơn hàng theo ID
     *
     * @param int $orderId ID đơn hàng
     * @return Order|null Đơn hàng hoặc null nếu không tìm thấy
     */
    public function getOrderById(int $orderId): ?Order
    {
        return $this->orders->get($orderId);
    }

    /**
     * Lọc đơn hàng theo trạng thái
     *
     * @param string $status Trạng thái cần lọc
     * @return Collection Collection đơn hàng đã lọc
     */
    public function filterByStatus(string $status): Collection
    {
        return $this->orders->filter(function ($order) use ($status) {
            return $order->getStatusCode() === $status;
        });
    }

    /**
     * Lọc đơn hàng theo loại
     *
     * @param int $typeId Loại đơn hàng cần lọc
     * @return Collection Collection đơn hàng đã lọc
     */
    public function filterByType(int $typeId): Collection
    {
        return $this->orders->filter(function ($order) use ($typeId) {
            return $order->getTypeId() === $typeId;
        });
    }

    /**
     * Lọc đơn hàng theo khách hàng
     *
     * @param int $customerId ID khách hàng
     * @return Collection Collection đơn hàng đã lọc
     */
    public function filterByCustomer(int $customerId): Collection
    {
        return $this->orders->filter(function ($order) use ($customerId) {
            return $order->getCustomerId() === $customerId;
        });
    }

    /**
     * Lọc đơn hàng theo khoảng giá
     *
     * @param float $minAmount Số tiền tối thiểu
     * @param float $maxAmount Số tiền tối đa
     * @return Collection Collection đơn hàng đã lọc
     */
    public function filterByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->orders->filter(function ($order) use ($minAmount, $maxAmount) {
            $totalAmount = $order->getCalcTotalMoney();
            return $totalAmount >= $minAmount && $totalAmount <= $maxAmount;
        });
    }

    /**
     * Lọc đơn hàng theo ngày tạo
     *
     * @param string $fromDate Ngày bắt đầu (Y-m-d)
     * @param string $toDate Ngày kết thúc (Y-m-d)
     * @return Collection Collection đơn hàng đã lọc
     */
    public function filterByCreatedDate(string $fromDate, string $toDate): Collection
    {
        $fromTimestamp = strtotime($fromDate);
        $toTimestamp = strtotime($toDate);

        return $this->orders->filter(function ($order) use ($fromTimestamp, $toTimestamp) {
            $orderTimestamp = strtotime($order->getCreatedDateTime());
            return $orderTimestamp >= $fromTimestamp && $orderTimestamp <= $toTimestamp;
        });
    }

    /**
     * Sắp xếp đơn hàng theo tiêu chí
     *
     * @param string $field Trường để sắp xếp
     * @param bool $ascending Thứ tự sắp xếp (true: tăng dần, false: giảm dần)
     * @return Collection Collection đơn hàng đã sắp xếp
     */
    public function sortOrders(string $field, bool $ascending = true): Collection
    {
        $sortedOrders = $this->orders->sortBy($field);

        if (!$ascending) {
            $sortedOrders = $sortedOrders->reverse();
        }

        return $sortedOrders;
    }

    /**
     * Lấy tổng doanh thu của tất cả đơn hàng
     *
     * @return float Tổng doanh thu
     */
    public function getTotalRevenue(): float
    {
        return $this->orders->sum('calcTotalMoney');
    }

    /**
     * Lấy tổng phí vận chuyển
     *
     * @return float Tổng phí vận chuyển
     */
    public function getTotalShippingFee(): float
    {
        return $this->orders->sum(function ($order) {
            return $order->getTotalCarrierFee();
        });
    }

    /**
     * Lấy thống kê theo trạng thái
     *
     * @return array Thống kê theo trạng thái
     */
    public function getStatusStatistics(): array
    {
        $stats = [];

        foreach ($this->orders as $order) {
            $status = $order->getStatusName();
            if (!isset($stats[$status])) {
                $stats[$status] = 0;
            }
            $stats[$status]++;
        }

        return $stats;
    }

    /**
     * Lấy thống kê theo loại đơn hàng
     *
     * @return array Thống kê theo loại đơn hàng
     */
    public function getTypeStatistics(): array
    {
        $stats = [];

        foreach ($this->orders as $order) {
            $type = $order->getType();
            if (!isset($stats[$type])) {
                $stats[$type] = 0;
            }
            $stats[$type]++;
        }

        return $stats;
    }

    /**
     * Lấy thống kê theo kênh bán hàng
     *
     * @return array Thống kê theo kênh bán hàng
     */
    public function getSaleChannelStatistics(): array
    {
        $stats = [];

        foreach ($this->orders as $order) {
            $channel = $order->getSaleChannelName();
            if (!isset($stats[$channel])) {
                $stats[$channel] = 0;
            }
            $stats[$channel]++;
        }

        return $stats;
    }
}
