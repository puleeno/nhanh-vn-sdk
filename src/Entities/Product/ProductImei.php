<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product IMEI entity
 */
class ProductImei extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getIdNhanh()) {
            $this->addError('idNhanh', 'ID sản phẩm không được để trống');
        }

        if (!$this->getImeiCode()) {
            $this->addError('imeiCode', 'Mã IMEI không được để trống');
        }
    }

    // Basic getters
    public function getIdNhanh(): ?int
    {
        return $this->getAttribute('idNhanh');
    }

    public function getProductName(): ?string
    {
        return $this->getAttribute('productName');
    }

    public function getProductCode(): ?string
    {
        return $this->getAttribute('productCode');
    }

    public function getProductBarcode(): ?string
    {
        return $this->getAttribute('productBarcode');
    }

    public function getDepotId(): ?int
    {
        return $this->getAttribute('depotId');
    }

    public function getDepotName(): ?string
    {
        return $this->getAttribute('depotName');
    }

    public function getImeiCode(): ?string
    {
        return $this->getAttribute('imeiCode');
    }

    public function getPrice(): ?int
    {
        return $this->getAttribute('price');
    }

    public function getImportPrice(): ?int
    {
        return $this->getAttribute('importPrice');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getStatus(): ?int
    {
        return $this->getAttribute('status');
    }

    public function getStatusName(): ?string
    {
        return $this->getAttribute('statusName');
    }

    public function getWarrantyMonths(): ?int
    {
        return $this->getAttribute('warrantyMonths');
    }

    public function getExtendedWarrantyId(): ?int
    {
        return $this->getAttribute('extendedWarrantyId');
    }

    public function getExtendedWarrantyName(): ?string
    {
        return $this->getAttribute('extendedWarrantyName');
    }

    public function getExtendedWarrantyMonths(): ?int
    {
        return $this->getAttribute('extendedWarrantyMonths');
    }

    public function getWarrantyExpiredDate(): ?string
    {
        return $this->getAttribute('warrantyExpiredDate');
    }

    public function getCreatedById(): ?int
    {
        return $this->getAttribute('createdById');
    }

    public function getCreatedDateTime(): ?string
    {
        return $this->getAttribute('createdDateTime');
    }

    public function getActivatedById(): ?int
    {
        return $this->getAttribute('activatedById');
    }

    public function getActivatedByDateTime(): ?string
    {
        return $this->getAttribute('activatedByDateTime');
    }

    public function getImeiHistories(): array
    {
        return $this->getAttribute('imeiHistories', []);
    }

    // Business logic methods
    public function isNew(): bool
    {
        return $this->getStatus() === 1;
    }

    public function isSold(): bool
    {
        return $this->getStatus() === 2;
    }

    public function isInTransit(): bool
    {
        return $this->getStatus() === 3;
    }

    public function isDamaged(): bool
    {
        return $this->getStatus() === 5;
    }

    public function isReturnedToSupplier(): bool
    {
        return $this->getStatus() === 6;
    }

    public function isTransferring(): bool
    {
        return $this->getStatus() === 8;
    }

    public function isUnderWarranty(): bool
    {
        return $this->getStatus() === 9;
    }

    public function isWarrantyReturned(): bool
    {
        return $this->getStatus() === 10;
    }

    public function isAvailable(): bool
    {
        return $this->isNew();
    }

    public function isUnavailable(): bool
    {
        return !$this->isAvailable();
    }

    public function hasWarranty(): bool
    {
        return $this->getWarrantyMonths() > 0;
    }

    public function hasExtendedWarranty(): bool
    {
        return $this->getExtendedWarrantyId() !== null;
    }

    public function getTotalWarrantyMonths(): int
    {
        $baseWarranty = $this->getWarrantyMonths() ?? 0;
        $extendedWarranty = $this->getExtendedWarrantyMonths() ?? 0;

        return $baseWarranty + $extendedWarranty;
    }

    public function getWarrantyStatus(): string
    {
        if (!$this->hasWarranty()) {
            return 'No Warranty';
        }

        if ($this->isWarrantyExpired()) {
            return 'Expired';
        }

        if ($this->isUnderWarranty()) {
            return 'Under Warranty';
        }

        return 'Available';
    }

    public function isWarrantyExpired(): bool
    {
        if (!$this->hasWarranty()) {
            return true;
        }

        $expiredDate = strtotime($this->getWarrantyExpiredDate() ?? '');
        if (!$expiredDate) {
            return true;
        }

        return time() > $expiredDate;
    }

    public function getDaysUntilWarrantyExpiry(): ?int
    {
        if (!$this->hasWarranty()) {
            return null;
        }

        $expiredDate = strtotime($this->getWarrantyExpiredDate() ?? '');
        if (!$expiredDate) {
            return null;
        }

        $days = ceil(($expiredDate - time()) / 86400);
        return max(0, $days);
    }

    public function getFormattedPrice(): string
    {
        $price = $this->getPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'N/A';
    }

    public function getFormattedImportPrice(): string
    {
        $price = $this->getImportPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'N/A';
    }

    public function getFormattedWarrantyExpiredDate(): string
    {
        $date = $this->getWarrantyExpiredDate();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y', strtotime($date));
    }

    public function getFormattedCreatedDateTime(): string
    {
        $date = $this->getCreatedDateTime();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getFormattedActivatedDateTime(): string
    {
        $date = $this->getActivatedByDateTime();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getStatusText(): string
    {
        return $this->getStatusName() ?: 'Unknown';
    }

    public function hasHistory(): bool
    {
        return !empty($this->getImeiHistories());
    }

    public function getHistoryCount(): int
    {
        return count($this->getImeiHistories());
    }

    public function getLatestHistory(): ?array
    {
        if (empty($this->getImeiHistories())) {
            return null;
        }

        // Giả sử histories đã được sắp xếp theo thời gian
        return $this->getImeiHistories()[0];
    }

    public function getHistoryByStep(int $step): ?array
    {
        foreach ($this->getImeiHistories() as $history) {
            if ($history['step'] === $step) {
                return $history;
            }
        }

        return null;
    }

    public function hasStep(int $step): bool
    {
        return $this->getHistoryByStep($step) !== null;
    }

    /**
     * Tính margin
     */
    public function getMargin(): ?int
    {
        $price = $this->getPrice();
        $importPrice = $this->getImportPrice();

        if (!$price || !$importPrice) {
            return null;
        }

        return $price - $importPrice;
    }

    /**
     * Tính margin percentage
     */
    public function getMarginPercentage(): ?float
    {
        $price = $this->getPrice();
        $importPrice = $this->getImportPrice();

        if (!$price || !$importPrice) {
            return null;
        }

        if ($importPrice === 0) {
            return null;
        }

        return round((($price - $importPrice) / $importPrice) * 100, 2);
    }

    /**
     * Tạo product IMEI từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều product IMEI từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $imeis = [];

        foreach ($data as $imeiData) {
            $imeis[] = self::createFromArray($imeiData);
        }

        return $imeis;
    }

    /**
     * Lọc IMEI theo trạng thái
     */
    public static function filterByStatus(array $imeis, int $status): array
    {
        return array_filter($imeis, function (ProductImei $imei) use ($status) {
            return $imei->getStatus() === $status;
        });
    }

    /**
     * Lọc IMEI theo depot
     */
    public static function filterByDepot(array $imeis, int $depotId): array
    {
        return array_filter($imeis, function (ProductImei $imei) use ($depotId) {
            return $imei->getDepotId() === $depotId;
        });
    }

    /**
     * Lọc IMEI có warranty
     */
    public static function filterWithWarranty(array $imeis): array
    {
        return array_filter($imeis, function (ProductImei $imei) {
            return $imei->hasWarranty();
        });
    }

    /**
     * Lọc IMEI available
     */
    public static function filterAvailable(array $imeis): array
    {
        return array_filter($imeis, function (ProductImei $imei) {
            return $imei->isAvailable();
        });
    }
}
