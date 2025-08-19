<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Gift entity
 */
class ProductGift extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getProductId()) {
            $this->addError('productId', 'ID sản phẩm không được để trống');
        }

        if (!$this->getProductGiftId()) {
            $this->addError('productGiftId', 'ID sản phẩm quà tặng không được để trống');
        }

        if ($this->getQuantity() === null || $this->getQuantity() <= 0) {
            $this->addError('quantity', 'Số lượng quà tặng phải lớn hơn 0');
        }
    }

    // Basic getters
    public function getProductId(): ?int
    {
        return $this->getAttribute('productId');
    }

    public function getProductCode(): ?string
    {
        return $this->getAttribute('productCode');
    }

    public function getProductName(): ?string
    {
        return $this->getAttribute('productName');
    }

    public function getProductGiftId(): ?int
    {
        return $this->getAttribute('productGiftId');
    }

    public function getProductGiftCode(): ?string
    {
        return $this->getAttribute('productGiftCode');
    }

    public function getProductGiftName(): ?string
    {
        return $this->getAttribute('productGiftName');
    }

    public function getQuantity(): ?int
    {
        return $this->getAttribute('quantity');
    }

    public function getValue(): ?int
    {
        return $this->getAttribute('value');
    }

    public function getPromotionFromDate(): ?string
    {
        return $this->getAttribute('promotionFromDate');
    }

    public function getPromotionToDate(): ?string
    {
        return $this->getAttribute('promotionToDate');
    }

    public function getPromotionStatus(): ?int
    {
        return $this->getAttribute('promotionStatus');
    }

    public function getPromotionDepotIds(): array
    {
        return $this->getAttribute('promotionDepotIds', []);
    }

    // Business logic methods
    public function isActive(): bool
    {
        return $this->getPromotionStatus() === 1;
    }

    public function isInactive(): bool
    {
        return $this->getPromotionStatus() === 2;
    }

    public function isPromotionActive(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $now = time();
        $fromDate = strtotime($this->getPromotionFromDate() ?? '');
        $toDate = strtotime($this->getPromotionToDate() ?? '');

        if (!$fromDate || !$toDate) {
            return false;
        }

        return $now >= $fromDate && $now <= $toDate;
    }

    public function isPromotionExpired(): bool
    {
        $toDate = strtotime($this->getPromotionToDate() ?? '');
        if (!$toDate) {
            return true;
        }

        return time() > $toDate;
    }

    public function isPromotionNotStarted(): bool
    {
        $fromDate = strtotime($this->getPromotionFromDate() ?? '');
        if (!$fromDate) {
            return true;
        }

        return time() < $fromDate;
    }

    public function getDaysUntilStart(): ?int
    {
        $fromDate = strtotime($this->getPromotionFromDate() ?? '');
        if (!$fromDate) {
            return null;
        }

        $days = ceil(($fromDate - time()) / 86400);
        return max(0, $days);
    }

    public function getDaysUntilExpiry(): ?int
    {
        $toDate = strtotime($this->getPromotionToDate() ?? '');
        if (!$toDate) {
            return null;
        }

        $days = ceil(($toDate - time()) / 86400);
        return max(0, $days);
    }

    public function getPromotionDuration(): ?int
    {
        $fromDate = strtotime($this->getPromotionFromDate() ?? '');
        $toDate = strtotime($this->getPromotionToDate() ?? '');

        if (!$fromDate || !$toDate) {
            return null;
        }

        return ceil(($toDate - $fromDate) / 86400);
    }

    public function getFormattedValue(): string
    {
        $value = $this->getValue();
        return $value ? number_format($value, 0, ',', '.') . ' VNĐ' : 'N/A';
    }

    public function getFormattedQuantity(): string
    {
        $quantity = $this->getQuantity();
        return $quantity ? (string) $quantity : 'N/A';
    }

    public function getFormattedPromotionFromDate(): string
    {
        $date = $this->getPromotionFromDate();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getFormattedPromotionToDate(): string
    {
        $date = $this->getPromotionToDate();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getPromotionStatusText(): string
    {
        return $this->isActive() ? 'Active' : 'Inactive';
    }

    public function hasMultipleDepots(): bool
    {
        return count($this->getPromotionDepotIds()) > 1;
    }

    public function getDepotCount(): int
    {
        return count($this->getPromotionDepotIds());
    }

    public function isAvailableInDepot(int $depotId): bool
    {
        return in_array($depotId, $this->getPromotionDepotIds());
    }

    public function getPromotionSummary(): string
    {
        $summary = [];

        if ($this->getQuantity() > 1) {
            $summary[] = "Mua {$this->getQuantity()}x";
        }

        $summary[] = "được tặng {$this->getProductGiftName()}";

        if ($this->getValue()) {
            $summary[] = "trị giá {$this->getFormattedValue()}";
        }

        return implode(' ', $summary);
    }

    public function getPromotionTimeStatus(): string
    {
        if ($this->isPromotionNotStarted()) {
            $days = $this->getDaysUntilStart();
            return "Bắt đầu sau {$days} ngày";
        }

        if ($this->isPromotionExpired()) {
            return 'Đã kết thúc';
        }

        if ($this->isPromotionActive()) {
            $days = $this->getDaysUntilExpiry();
            return "Còn {$days} ngày";
        }

        return 'Không xác định';
    }

    /**
     * Tính giá trị quà tặng theo số lượng sản phẩm mua
     */
    public function calculateGiftValue(int $purchasedQuantity): float
    {
        $baseQuantity = 1; // Số lượng cơ bản để được tặng
        $giftQuantity = $this->getQuantity();
        $giftValue = $this->getValue() ?? 0;

        if ($giftQuantity <= 0) {
            return 0;
        }

        // Tính số lần được tặng
        $giftTimes = floor($purchasedQuantity / $baseQuantity);

        // Tính tổng giá trị quà tặng
        return $giftTimes * $giftValue;
    }

    /**
     * Tính số lượng quà tặng theo số lượng sản phẩm mua
     */
    public function calculateGiftQuantity(int $purchasedQuantity): int
    {
        $baseQuantity = 1; // Số lượng cơ bản để được tặng
        $giftQuantity = $this->getQuantity();

        if ($giftQuantity <= 0) {
            return 0;
        }

        // Tính số lần được tặng
        $giftTimes = floor($purchasedQuantity / $baseQuantity);

        // Tính tổng số lượng quà tặng
        return $giftTimes * $giftQuantity;
    }

    /**
     * Tạo product gift từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều product gift từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $gifts = [];

        foreach ($data as $giftData) {
            $gifts[] = self::createFromArray($giftData);
        }

        return $gifts;
    }

    /**
     * Lọc gifts theo trạng thái
     */
    public static function filterByStatus(array $gifts, int $status): array
    {
        return array_filter($gifts, function (ProductGift $gift) use ($status) {
            return $gift->getPromotionStatus() === $status;
        });
    }

    /**
     * Lọc gifts đang active
     */
    public static function filterActive(array $gifts): array
    {
        return array_filter($gifts, function (ProductGift $gift) {
            return $gift->isPromotionActive();
        });
    }

    /**
     * Lọc gifts theo depot
     */
    public static function filterByDepot(array $gifts, int $depotId): array
    {
        return array_filter($gifts, function (ProductGift $gift) use ($depotId) {
            return $gift->isAvailableInDepot($depotId);
        });
    }
}
