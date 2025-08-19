<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product IMEI Sold entity
 */
class ProductImeiSold extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getImei()) {
            $this->addError('imei', 'IMEI không được để trống');
        }

        if (!$this->getSoldDate()) {
            $this->addError('soldDate', 'Ngày bán không được để trống');
        }
    }

    // Basic getters
    public function getImei(): ?string
    {
        return $this->getAttribute('imei');
    }

    public function getSoldDate(): ?string
    {
        return $this->getAttribute('soldDate');
    }

    public function getDepotId(): ?int
    {
        return $this->getAttribute('depotId');
    }

    public function getDepotName(): ?string
    {
        return $this->getAttribute('depotName');
    }

    public function getMode(): ?int
    {
        return $this->getAttribute('mode');
    }

    public function getModeName(): ?string
    {
        return $this->getAttribute('modeName');
    }

    public function getProductName(): ?string
    {
        return $this->getAttribute('productName');
    }

    public function getProductPrice(): ?float
    {
        return $this->getAttribute('productPrice');
    }

    public function getCustomer(): ?array
    {
        return $this->getAttribute('customer');
    }

    // Business logic methods
    public function isTransferSale(): bool
    {
        return $this->getMode() === 1;
    }

    public function isRetailSale(): bool
    {
        return $this->getMode() === 2;
    }

    public function isWholesaleSale(): bool
    {
        return $this->getMode() === 6;
    }

    public function getSaleType(): string
    {
        $mode = $this->getMode();

        switch ($mode) {
            case 1:
                return 'Chuyển hàng';
            case 2:
                return 'Bán lẻ';
            case 6:
                return 'Bán sỉ';
            default:
                return 'Không xác định';
        }
    }

    public function getSaleTypeColor(): string
    {
        $mode = $this->getMode();

        switch ($mode) {
            case 1:
                return '#17a2b8'; // Blue
            case 2:
                return '#28a745'; // Green
            case 6:
                return '#fd7e14'; // Orange
            default:
                return '#6c757d'; // Gray
        }
    }

    public function hasCustomer(): bool
    {
        return !empty($this->getCustomer());
    }

    public function getCustomerId(): ?int
    {
        $customer = $this->getCustomer();
        return $customer['id'] ?? null;
    }

    public function getCustomerCode(): ?string
    {
        $customer = $this->getCustomer();
        return $customer['code'] ?? null;
    }

    public function getCustomerPhone(): ?string
    {
        $customer = $this->getCustomer();
        return $customer['phone'] ?? null;
    }

    public function getCustomerName(): ?string
    {
        $customer = $this->getCustomer();
        return $customer['name'] ?? null;
    }

    public function getCustomerEmail(): ?string
    {
        $customer = $this->getCustomer();
        return $customer['email'] ?? null;
    }

    public function getCustomerAddress(): ?string
    {
        $customer = $this->getCustomer();
        return $customer['address'] ?? null;
    }

    public function getFormattedSoldDate(): string
    {
        $date = $this->getSoldDate();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y', strtotime($date));
    }

    public function getFormattedProductPrice(): string
    {
        $price = $this->getProductPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'N/A';
    }

    public function getFormattedImei(): string
    {
        $imei = $this->getImei();
        return $imei ?: 'N/A';
    }

    public function getDaysSinceSold(): ?int
    {
        $soldDate = strtotime($this->getSoldDate() ?? '');
        if (!$soldDate) {
            return null;
        }

        $days = ceil((time() - $soldDate) / 86400);
        return max(0, $days);
    }

    public function isSoldToday(): bool
    {
        $soldDate = strtotime($this->getSoldDate() ?? '');
        if (!$soldDate) {
            return false;
        }

        $today = strtotime('today');
        return $soldDate >= $today;
    }

    public function isSoldThisWeek(): bool
    {
        $soldDate = strtotime($this->getSoldDate() ?? '');
        if (!$soldDate) {
            return false;
        }

        $thisWeek = strtotime('this week');
        $nextWeek = strtotime('next week');

        return $soldDate >= $thisWeek && $soldDate < $nextWeek;
    }

    public function isSoldThisMonth(): bool
    {
        $soldDate = strtotime($this->getSoldDate() ?? '');
        if (!$soldDate) {
            return false;
        }

        $thisMonth = strtotime('first day of this month');
        $nextMonth = strtotime('first day of next month');

        return $soldDate >= $thisMonth && $soldDate < $nextMonth;
    }

    public function isSoldThisYear(): bool
    {
        $soldDate = strtotime($this->getSoldDate() ?? '');
        if (!$soldDate) {
            return false;
        }

        $thisYear = strtotime('first day of january this year');
        $nextYear = strtotime('first day of january next year');

        return $soldDate >= $thisYear && $soldDate < $nextYear;
    }

    public function getSoldPeriod(): string
    {
        if ($this->isSoldToday()) {
            return 'Hôm nay';
        }

        if ($this->isSoldThisWeek()) {
            return 'Tuần này';
        }

        if ($this->isSoldThisMonth()) {
            return 'Tháng này';
        }

        if ($this->isSoldThisYear()) {
            return 'Năm nay';
        }

        $days = $this->getDaysSinceSold();
        if ($days === null) {
            return 'Không xác định';
        }

        if ($days < 30) {
            return "{$days} ngày trước";
        }

        if ($days < 365) {
            $months = floor($days / 30);
            return "{$months} tháng trước";
        }

        $years = floor($days / 365);
        return "{$years} năm trước";
    }

    public function getCustomerSummary(): string
    {
        if (!$this->hasCustomer()) {
            return 'Khách lẻ';
        }

        $summary = [];

        if ($this->getCustomerName()) {
            $summary[] = $this->getCustomerName();
        }

        if ($this->getCustomerPhone()) {
            $summary[] = $this->getCustomerPhone();
        }

        if ($this->getCustomerCode()) {
            $summary[] = "Mã: {$this->getCustomerCode()}";
        }

        return empty($summary) ? 'Khách lẻ' : implode(' - ', $summary);
    }

    public function getSaleSummary(): string
    {
        $summary = [];

        $summary[] = $this->getProductName();
        $summary[] = $this->getSaleType();

        if ($this->getProductPrice()) {
            $summary[] = $this->getFormattedProductPrice();
        }

        if ($this->getDepotName()) {
            $summary[] = "tại {$this->getDepotName()}";
        }

        $summary[] = $this->getFormattedSoldDate();

        return implode(' - ', array_filter($summary));
    }

    /**
     * Tính tổng giá trị bán
     */
    public function calculateTotalValue(): float
    {
        $price = $this->getProductPrice() ?? 0;
        return $price;
    }

    /**
     * Tạo product IMEI sold từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều product IMEI sold từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $sold = [];

        foreach ($data as $soldData) {
            $sold[] = self::createFromArray($soldData);
        }

        return $sold;
    }

    /**
     * Lọc sold theo mode
     */
    public static function filterByMode(array $sold, int $mode): array
    {
        return array_filter($sold, function (ProductImeiSold $item) use ($mode) {
            return $item->getMode() === $mode;
        });
    }

    /**
     * Lọc sold theo depot
     */
    public static function filterByDepot(array $sold, int $depotId): array
    {
        return array_filter($sold, function (ProductImeiSold $item) use ($depotId) {
            return $item->getDepotId() === $depotId;
        });
    }

    /**
     * Lọc sold theo khoảng thời gian
     */
    public static function filterByDateRange(array $sold, string $fromDate, string $toDate): array
    {
        $fromTimestamp = strtotime($fromDate);
        $toTimestamp = strtotime($toDate);

        if (!$fromTimestamp || !$toTimestamp) {
            return [];
        }

        return array_filter($sold, function (ProductImeiSold $item) use ($fromTimestamp, $toTimestamp) {
            $soldTimestamp = strtotime($item->getSoldDate() ?? '');
            if (!$soldTimestamp) {
                return false;
            }

            return $soldTimestamp >= $fromTimestamp && $soldTimestamp <= $toTimestamp;
        });
    }

    /**
     * Lọc sold theo brand
     */
    public static function filterByBrand(array $sold, int $brandId): array
    {
        // Note: Brand filtering would need to be implemented based on product data
        // This is a placeholder for future implementation
        return $sold;
    }

    /**
     * Lọc sold theo IMEI
     */
    public static function filterByImei(array $sold, string $imei): array
    {
        return array_filter($sold, function (ProductImeiSold $item) use ($imei) {
            return $item->getImei() === $imei;
        });
    }

    /**
     * Sắp xếp sold theo ngày bán
     */
    public static function sortBySoldDate(array $sold, bool $ascending = true): array
    {
        usort($sold, function (ProductImeiSold $a, ProductImeiSold $b) use ($ascending) {
            $dateA = strtotime($a->getSoldDate() ?? '');
            $dateB = strtotime($b->getSoldDate() ?? '');

            if ($dateA === $dateB) {
                return 0;
            }

            if ($ascending) {
                return $dateA <=> $dateB;
            }

            return $dateB <=> $dateA;
        });

        return $sold;
    }

    /**
     * Sắp xếp sold theo giá
     */
    public static function sortByPrice(array $sold, bool $ascending = true): array
    {
        usort($sold, function (ProductImeiSold $a, ProductImeiSold $b) use ($ascending) {
            $priceA = $a->getProductPrice() ?? 0;
            $priceB = $b->getProductPrice() ?? 0;

            if ($priceA === $priceB) {
                return 0;
            }

            if ($ascending) {
                return $priceA <=> $priceB;
            }

            return $priceB <=> $priceA;
        });

        return $sold;
    }

    /**
     * Tính tổng doanh thu
     */
    public static function calculateTotalRevenue(array $sold): float
    {
        $total = 0;

        foreach ($sold as $item) {
            $total += $item->calculateTotalValue();
        }

        return $total;
    }

    /**
     * Tính tổng số lượng bán
     */
    public static function calculateTotalQuantity(array $sold): int
    {
        return count($sold);
    }

    /**
     * Tính doanh thu trung bình
     */
    public static function calculateAverageRevenue(array $sold): float
    {
        $count = count($sold);
        if ($count === 0) {
            return 0;
        }

        return self::calculateTotalRevenue($sold) / $count;
    }
}
