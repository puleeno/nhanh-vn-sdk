<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Expiry entity
 */
class ProductExpiry extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID sản phẩm không được để trống');
        }

        if (!$this->getProductName()) {
            $this->addError('productName', 'Tên sản phẩm không được để trống');
        }

        if (!$this->getExpiredDate()) {
            $this->addError('expiredDate', 'Ngày hết hạn không được để trống');
        }
    }

    // Basic getters
    public function getId(): mixed
    {
        return $this->getAttribute('id');
    }

    public function getDepotName(): ?string
    {
        return $this->getAttribute('depotName');
    }

    public function getProductName(): ?string
    {
        return $this->getAttribute('productName');
    }

    public function getBillId(): ?int
    {
        return $this->getAttribute('billId');
    }

    public function getQuantity(): ?float
    {
        return $this->getAttribute('quantity');
    }

    public function getExpiredDate(): ?string
    {
        return $this->getAttribute('expiredDate');
    }

    public function getPriorWarningDays(): ?int
    {
        return $this->getAttribute('priorWarningDays');
    }

    public function getStatus(): ?int
    {
        return $this->getAttribute('status');
    }

    // Business logic methods
    public function isNew(): bool
    {
        return $this->getStatus() === 1;
    }

    public function isChecked(): bool
    {
        return $this->getStatus() === 2;
    }

    public function isExpired(): bool
    {
        $expiredDate = strtotime($this->getExpiredDate() ?? '');
        if (!$expiredDate) {
            return false;
        }

        return time() > $expiredDate;
    }

    public function isExpiringSoon(int $warningDays = null): bool
    {
        $warningDays = $warningDays ?? $this->getPriorWarningDays() ?? 30;
        $expiredDate = strtotime($this->getExpiredDate() ?? '');

        if (!$expiredDate) {
            return false;
        }

        $warningTimestamp = $expiredDate - ($warningDays * 86400);
        return time() >= $warningTimestamp;
    }

    public function isExpiringToday(): bool
    {
        $expiredDate = strtotime($this->getExpiredDate() ?? '');
        if (!$expiredDate) {
            return false;
        }

        $today = strtotime('today');
        return $expiredDate <= $today;
    }

    public function isExpiringThisWeek(): bool
    {
        $expiredDate = strtotime($this->getExpiredDate() ?? '');
        if (!$expiredDate) {
            return false;
        }

        $thisWeek = strtotime('this week');
        $nextWeek = strtotime('next week');

        return $expiredDate >= $thisWeek && $expiredDate < $nextWeek;
    }

    public function isExpiringThisMonth(): bool
    {
        $expiredDate = strtotime($this->getExpiredDate() ?? '');
        if (!$expiredDate) {
            return false;
        }

        $thisMonth = strtotime('first day of this month');
        $nextMonth = strtotime('first day of next month');

        return $expiredDate >= $thisMonth && $expiredDate < $nextMonth;
    }

    public function getDaysUntilExpiry(): ?int
    {
        $expiredDate = strtotime($this->getExpiredDate() ?? '');
        if (!$expiredDate) {
            return null;
        }

        $days = ceil(($expiredDate - time()) / 86400);
        return $days;
    }

    public function getDaysSinceExpiry(): ?int
    {
        if (!$this->isExpired()) {
            return null;
        }

        $expiredDate = strtotime($this->getExpiredDate() ?? '');
        if (!$expiredDate) {
            return null;
        }

        $days = ceil((time() - $expiredDate) / 86400);
        return max(0, $days);
    }

    public function getExpiryStatus(): string
    {
        if ($this->isExpired()) {
            $days = $this->getDaysSinceExpiry();
            return "Đã hết hạn {$days} ngày";
        }

        if ($this->isExpiringToday()) {
            return 'Hết hạn hôm nay';
        }

        if ($this->isExpiringSoon()) {
            $days = $this->getDaysUntilExpiry();
            return "Sắp hết hạn ({$days} ngày)";
        }

        $days = $this->getDaysUntilExpiry();
        return "Còn {$days} ngày";
    }

    public function getExpiryPriority(): string
    {
        if ($this->isExpired()) {
            return 'Critical';
        }

        if ($this->isExpiringToday()) {
            return 'High';
        }

        if ($this->isExpiringSoon(7)) {
            return 'Medium';
        }

        if ($this->isExpiringSoon(30)) {
            return 'Low';
        }

        return 'Normal';
    }

    public function getExpiryColor(): string
    {
        switch ($this->getExpiryPriority()) {
            case 'Critical':
                return '#dc3545'; // Red
            case 'High':
                return '#fd7e14'; // Orange
            case 'Medium':
                return '#ffc107'; // Yellow
            case 'Low':
                return '#17a2b8'; // Blue
            default:
                return '#28a745'; // Green
        }
    }

    public function getFormattedExpiredDate(): string
    {
        $date = $this->getExpiredDate();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y', strtotime($date));
    }

    public function getFormattedQuantity(): string
    {
        $quantity = $this->getQuantity();
        return $quantity ? (string) $quantity : 'N/A';
    }

    public function getFormattedPriorWarningDays(): string
    {
        $days = $this->getPriorWarningDays();
        return $days ? "{$days} ngày" : 'N/A';
    }

    public function getStatusText(): string
    {
        return $this->isNew() ? 'Mới' : 'Đã kiểm tra';
    }

    public function hasQuantity(): bool
    {
        return $this->getQuantity() !== null && $this->getQuantity() > 0;
    }

    public function hasWarningDays(): bool
    {
        return $this->getPriorWarningDays() !== null && $this->getPriorWarningDays() > 0;
    }

    public function getExpirySummary(): string
    {
        $summary = [];

        $summary[] = $this->getProductName();

        if ($this->getDepotName()) {
            $summary[] = "tại {$this->getDepotName()}";
        }

        if ($this->hasQuantity()) {
            $summary[] = "số lượng: {$this->getFormattedQuantity()}";
        }

        $summary[] = $this->getExpiryStatus();

        return implode(' - ', array_filter($summary));
    }

    /**
     * Tính tổng giá trị sản phẩm sắp hết hạn
     */
    public function calculateExpiryValue(float $unitPrice): float
    {
        $quantity = $this->getQuantity() ?? 0;
        return $quantity * $unitPrice;
    }

    /**
     * Kiểm tra xem có cần cảnh báo không
     */
    public function needsWarning(): bool
    {
        return $this->isExpiringSoon() || $this->isExpired();
    }

    /**
     * Tạo product expiry từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều product expiry từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $expiries = [];

        foreach ($data as $expiryData) {
            $expiries[] = self::createFromArray($expiryData);
        }

        return $expiries;
    }

    /**
     * Lọc expiries theo trạng thái
     */
    public static function filterByStatus(array $expiries, int $status): array
    {
        return array_filter($expiries, function (ProductExpiry $expiry) use ($status) {
            return $expiry->getStatus() === $status;
        });
    }

    /**
     * Lọc expiries đã hết hạn
     */
    public static function filterExpired(array $expiries): array
    {
        return array_filter($expiries, function (ProductExpiry $expiry) {
            return $expiry->isExpired();
        });
    }

    /**
     * Lọc expiries sắp hết hạn
     */
    public static function filterExpiringSoon(array $expiries, int $warningDays = 30): array
    {
        return array_filter($expiries, function (ProductExpiry $expiry) use ($warningDays) {
            return $expiry->isExpiringSoon($warningDays);
        });
    }

    /**
     * Lọc expiries theo priority
     */
    public static function filterByPriority(array $expiries, string $priority): array
    {
        return array_filter($expiries, function (ProductExpiry $expiry) use ($priority) {
            return $expiry->getExpiryPriority() === $priority;
        });
    }

    /**
     * Sắp xếp expiries theo ngày hết hạn
     */
    public static function sortByExpiryDate(array $expiries, bool $ascending = true): array
    {
        usort($expiries, function (ProductExpiry $a, ProductExpiry $b) use ($ascending) {
            $dateA = strtotime($a->getExpiredDate() ?? '');
            $dateB = strtotime($b->getExpiredDate() ?? '');

            if ($dateA === $dateB) {
                return 0;
            }

            if ($ascending) {
                return $dateA <=> $dateB;
            }

            return $dateB <=> $dateA;
        });

        return $expiries;
    }

    /**
     * Sắp xếp expiries theo priority
     */
    public static function sortByPriority(array $expiries): array
    {
        $priorityOrder = ['Critical', 'High', 'Medium', 'Low', 'Normal'];

        usort($expiries, function (ProductExpiry $a, ProductExpiry $b) use ($priorityOrder) {
            $priorityA = array_search($a->getExpiryPriority(), $priorityOrder);
            $priorityB = array_search($b->getExpiryPriority(), $priorityOrder);

            if ($priorityA === $priorityB) {
                // Nếu cùng priority, sắp xếp theo ngày hết hạn
                $dateA = strtotime($a->getExpiredDate() ?? '');
                $dateB = strtotime($b->getExpiredDate() ?? '');

                return $dateA <=> $dateB;
            }

            return $priorityA <=> $priorityB;
        });

        return $expiries;
    }
}
