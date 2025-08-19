<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Warranty entity
 */
class ProductWarranty extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getWarranty()) {
            $this->addError('warranty', 'Số tháng bảo hành không được để trống');
        }

        if ($this->getWarranty() < 0) {
            $this->addError('warranty', 'Số tháng bảo hành không được âm');
        }
    }

    // Basic getters
    public function getWarranty(): ?int
    {
        return $this->getAttribute('warranty');
    }

    public function getWarrantyAddress(): ?string
    {
        return $this->getAttribute('warrantyAddress');
    }

    public function getWarrantyPhone(): ?string
    {
        return $this->getAttribute('warrantyPhone');
    }

    public function getWarrantyContent(): ?string
    {
        return $this->getAttribute('warrantyContent');
    }

    // Business logic methods
    public function hasWarranty(): bool
    {
        return $this->getWarranty() > 0;
    }

    public function isNoWarranty(): bool
    {
        return $this->getWarranty() === 0;
    }

    public function isLifetimeWarranty(): bool
    {
        return $this->getWarranty() === -1; // Giả sử -1 là bảo hành trọn đời
    }

    public function hasWarrantyAddress(): bool
    {
        return !empty($this->getWarrantyAddress());
    }

    public function hasWarrantyPhone(): bool
    {
        return !empty($this->getWarrantyPhone());
    }

    public function hasWarrantyContent(): bool
    {
        return !empty($this->getWarrantyContent());
    }

    public function hasWarrantyInfo(): bool
    {
        return $this->hasWarrantyAddress() || $this->hasWarrantyPhone() || $this->hasWarrantyContent();
    }

    public function getWarrantyText(): string
    {
        $warranty = $this->getWarranty();

        if ($warranty === null) {
            return 'Không xác định';
        }

        if ($warranty === 0) {
            return 'Không bảo hành';
        }

        if ($warranty === -1) {
            return 'Bảo hành trọn đời';
        }

        if ($warranty === 1) {
            return '1 tháng';
        }

        return "{$warranty} tháng";
    }

    public function getWarrantyYears(): ?float
    {
        $warranty = $this->getWarranty();

        if ($warranty === null || $warranty <= 0) {
            return null;
        }

        return round($warranty / 12, 1);
    }

    public function getWarrantyYearsText(): string
    {
        $years = $this->getWarrantyYears();

        if ($years === null) {
            return 'Không xác định';
        }

        if ($years < 1) {
            return $this->getWarrantyText();
        }

        if ($years === 1) {
            return '1 năm';
        }

        if ($years === (int)$years) {
            return (int)$years . ' năm';
        }

        return "{$years} năm";
    }

    public function getWarrantyStatus(): string
    {
        if ($this->isNoWarranty()) {
            return 'No Warranty';
        }

        if ($this->isLifetimeWarranty()) {
            return 'Lifetime';
        }

        $warranty = $this->getWarranty();

        if ($warranty <= 3) {
            return 'Short Term';
        }

        if ($warranty <= 12) {
            return 'Standard';
        }

        if ($warranty <= 24) {
            return 'Extended';
        }

        return 'Long Term';
    }

    public function getWarrantyStatusColor(): string
    {
        $status = $this->getWarrantyStatus();

        switch ($status) {
            case 'No Warranty':
                return '#dc3545'; // Red
            case 'Short Term':
                return '#fd7e14'; // Orange
            case 'Standard':
                return '#ffc107'; // Yellow
            case 'Extended':
                return '#17a2b8'; // Blue
            case 'Long Term':
                return '#28a745'; // Green
            case 'Lifetime':
                return '#6f42c1'; // Purple
            default:
                return '#6c757d'; // Gray
        }
    }

    public function getWarrantySummary(): string
    {
        $summary = [];

        $summary[] = $this->getWarrantyText();

        if ($this->hasWarrantyAddress()) {
            $summary[] = "Địa chỉ: {$this->getWarrantyAddress()}";
        }

        if ($this->hasWarrantyPhone()) {
            $summary[] = "Điện thoại: {$this->getWarrantyPhone()}";
        }

        if ($this->hasWarrantyContent()) {
            $summary[] = "Nội dung: {$this->getWarrantyContent()}";
        }

        return implode(' | ', $summary);
    }

    public function getShortWarrantyContent(int $length = 100): string
    {
        $content = $this->getWarrantyContent() ?: '';
        if (strlen($content) <= $length) {
            return $content;
        }

        return substr($content, 0, $length) . '...';
    }

    /**
     * Tính ngày hết hạn bảo hành từ ngày mua
     */
    public function calculateWarrantyExpiryDate(string $purchaseDate): ?string
    {
        if (!$this->hasWarranty()) {
            return null;
        }

        $purchaseTimestamp = strtotime($purchaseDate);
        if (!$purchaseTimestamp) {
            return null;
        }

        $warrantyMonths = $this->getWarranty();
        $expiryTimestamp = strtotime("+{$warrantyMonths} months", $purchaseTimestamp);

        return date('Y-m-d H:i:s', $expiryTimestamp);
    }

    /**
     * Kiểm tra xem sản phẩm còn trong thời hạn bảo hành không
     */
    public function isUnderWarranty(string $purchaseDate): bool
    {
        if (!$this->hasWarranty()) {
            return false;
        }

        $expiryDate = $this->calculateWarrantyExpiryDate($purchaseDate);
        if (!$expiryDate) {
            return false;
        }

        $expiryTimestamp = strtotime($expiryDate);
        return time() <= $expiryTimestamp;
    }

    /**
     * Tính số ngày còn lại trong thời hạn bảo hành
     */
    public function getRemainingWarrantyDays(string $purchaseDate): ?int
    {
        if (!$this->hasWarranty()) {
            return null;
        }

        $expiryDate = $this->calculateWarrantyExpiryDate($purchaseDate);
        if (!$expiryDate) {
            return null;
        }

        $expiryTimestamp = strtotime($expiryDate);
        $remaining = $expiryTimestamp - time();

        return max(0, ceil($remaining / 86400));
    }

    /**
     * Tính phần trăm thời gian bảo hành đã sử dụng
     */
    public function getWarrantyUsagePercentage(string $purchaseDate): ?float
    {
        if (!$this->hasWarranty()) {
            return null;
        }

        $purchaseTimestamp = strtotime($purchaseDate);
        if (!$purchaseTimestamp) {
            return null;
        }

        $warrantyMonths = $this->getWarranty();
        $warrantySeconds = $warrantyMonths * 30 * 24 * 3600; // Giả sử 1 tháng = 30 ngày

        $elapsed = time() - $purchaseTimestamp;
        $percentage = ($elapsed / $warrantySeconds) * 100;

        return min(100, max(0, round($percentage, 2)));
    }

    /**
     * Tạo warranty từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo warranty từ số tháng
     */
    public static function createFromMonths(int $months): self
    {
        return new self(['warranty' => $months]);
    }

    /**
     * Tạo warranty từ số năm
     */
    public static function createFromYears(float $years): self
    {
        $months = round($years * 12);
        return new self(['warranty' => $months]);
    }

    /**
     * Tạo warranty không bảo hành
     */
    public static function createNoWarranty(): self
    {
        return new self(['warranty' => 0]);
    }

    /**
     * Tạo warranty trọn đời
     */
    public static function createLifetimeWarranty(): self
    {
        return new self(['warranty' => -1]);
    }

    /**
     * Tạo warranty tiêu chuẩn (12 tháng)
     */
    public static function createStandardWarranty(): self
    {
        return new self(['warranty' => 12]);
    }

    /**
     * Tạo warranty mở rộng (24 tháng)
     */
    public static function createExtendedWarranty(): self
    {
        return new self(['warranty' => 24]);
    }

    /**
     * So sánh warranty với warranty khác
     */
    public function compareWith(ProductWarranty $other): int
    {
        $thisWarranty = $this->getWarranty() ?? 0;
        $otherWarranty = $other->getWarranty() ?? 0;

        // Xử lý trường hợp đặc biệt
        if ($thisWarranty === -1 && $otherWarranty === -1) {
            return 0; // Cả hai đều bảo hành trọn đời
        }

        if ($thisWarranty === -1) {
            return 1; // Bảo hành trọn đời luôn lớn hơn
        }

        if ($otherWarranty === -1) {
            return -1; // Bảo hành trọn đời luôn lớn hơn
        }

        return $thisWarranty <=> $otherWarranty;
    }

    /**
     * Kiểm tra xem warranty có tương thích với warranty khác không
     */
    public function isCompatibleWith(ProductWarranty $other): bool
    {
        // Logic: warranty tương thích nếu cùng loại hoặc có thể mở rộng
        $thisWarranty = $this->getWarranty() ?? 0;
        $otherWarranty = $other->getWarranty() ?? 0;

        // Cả hai đều không bảo hành
        if ($thisWarranty === 0 && $otherWarranty === 0) {
            return true;
        }

        // Cả hai đều bảo hành trọn đời
        if ($thisWarranty === -1 && $otherWarranty === -1) {
            return true;
        }

        // Một trong hai bảo hành trọn đời
        if ($thisWarranty === -1 || $otherWarranty === -1) {
            return true;
        }

        // Cả hai đều có thời hạn bảo hành
        if ($thisWarranty > 0 && $otherWarranty > 0) {
            return true;
        }

        return false;
    }

    /**
     * Merge warranty với warranty khác
     */
    public function mergeWith(ProductWarranty $other): self
    {
        $thisWarranty = $this->getWarranty() ?? 0;
        $otherWarranty = $other->getWarranty() ?? 0;

        // Logic merge: lấy warranty dài hơn
        $mergedWarranty = max($thisWarranty, $otherWarranty);

        // Merge thông tin khác
        $mergedData = [
            'warranty' => $mergedWarranty,
            'warrantyAddress' => $this->getWarrantyAddress() ?: $other->getWarrantyAddress(),
            'warrantyPhone' => $this->getWarrantyPhone() ?: $other->getWarrantyPhone(),
            'warrantyContent' => $this->getWarrantyContent() ?: $other->getWarrantyContent()
        ];

        return new self($mergedData);
    }
}
