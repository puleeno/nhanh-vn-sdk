<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Unit entity
 */
class ProductUnit extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID đơn vị tính không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên đơn vị tính không được để trống');
        }

        if ($this->getQuantity() === null || $this->getQuantity() <= 0) {
            $this->addError('quantity', 'Số lượng quy đổi phải lớn hơn 0');
        }
    }

    // Basic getters
    public function getId(): mixed
    {
        return $this->getAttribute('id');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getQuantity(): ?int
    {
        return $this->getAttribute('quantity');
    }

    public function getImportPrice(): ?float
    {
        return $this->getAttribute('importPrice');
    }

    public function getPrice(): ?float
    {
        return $this->getAttribute('price');
    }

    public function getWholesalePrice(): ?float
    {
        return $this->getAttribute('wholesalePrice');
    }

    // Business logic methods
    public function isBaseUnit(): bool
    {
        return $this->getQuantity() === 1;
    }

    public function isMultipleUnit(): bool
    {
        return $this->getQuantity() > 1;
    }

    public function isFractionalUnit(): bool
    {
        return $this->getQuantity() < 1;
    }

    public function getConversionRate(): float
    {
        return $this->getQuantity() ?? 1;
    }

    public function getFormattedQuantity(): string
    {
        $quantity = $this->getQuantity();
        if ($quantity === 1) {
            return '1';
        }

        if ($quantity > 1) {
            return $quantity . 'x';
        }

        return '1/' . (1 / $quantity);
    }

    public function getFormattedImportPrice(): string
    {
        $price = $this->getImportPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'N/A';
    }

    public function getFormattedPrice(): string
    {
        $price = $this->getPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'N/A';
    }

    public function getFormattedWholesalePrice(): string
    {
        $price = $this->getWholesalePrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'N/A';
    }

    public function hasImportPrice(): bool
    {
        return $this->getImportPrice() !== null;
    }

    public function hasPrice(): bool
    {
        return $this->getPrice() !== null;
    }

    public function hasWholesalePrice(): bool
    {
        return $this->getWholesalePrice() !== null;
    }

    public function hasPricing(): bool
    {
        return $this->hasImportPrice() || $this->hasPrice() || $this->hasWholesalePrice();
    }

    /**
     * Tính giá theo đơn vị cơ bản
     */
    public function getBaseUnitPrice(): ?float
    {
        $price = $this->getPrice();
        $quantity = $this->getQuantity();

        if (!$price || !$quantity) {
            return null;
        }

        return $price / $quantity;
    }

    /**
     * Tính giá nhập theo đơn vị cơ bản
     */
    public function getBaseUnitImportPrice(): ?float
    {
        $price = $this->getImportPrice();
        $quantity = $this->getQuantity();

        if (!$price || !$quantity) {
            return null;
        }

        return $price / $quantity;
    }

    /**
     * Tính giá bán buôn theo đơn vị cơ bản
     */
    public function getBaseUnitWholesalePrice(): ?float
    {
        $price = $this->getWholesalePrice();
        $quantity = $this->getQuantity();

        if (!$price || !$quantity) {
            return null;
        }

        return $price / $quantity;
    }

    /**
     * Tính margin theo đơn vị cơ bản
     */
    public function getBaseUnitMargin(): ?float
    {
        $basePrice = $this->getBaseUnitPrice();
        $baseImportPrice = $this->getBaseUnitImportPrice();

        if (!$basePrice || !$baseImportPrice) {
            return null;
        }

        return $basePrice - $baseImportPrice;
    }

    /**
     * Tính margin percentage theo đơn vị cơ bản
     */
    public function getBaseUnitMarginPercentage(): ?float
    {
        $basePrice = $this->getBaseUnitPrice();
        $baseImportPrice = $this->getBaseUnitImportPrice();

        if (!$basePrice || !$baseImportPrice) {
            return null;
        }

        if ($baseImportPrice === 0) {
            return null;
        }

        return round((($basePrice - $baseImportPrice) / $baseImportPrice) * 100, 2);
    }

    /**
     * Kiểm tra xem có phải đơn vị tính chính không
     */
    public function isMainUnit(): bool
    {
        // Logic: đơn vị có quantity = 1 hoặc có giá thấp nhất
        return $this->isBaseUnit();
    }

    /**
     * So sánh với đơn vị khác
     */
    public function compareWith(ProductUnit $other): int
    {
        $quantityThis = $this->getQuantity() ?? 0;
        $quantityOther = $other->getQuantity() ?? 0;

        return $quantityThis <=> $quantityOther;
    }

    /**
     * Tạo đơn vị tính từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều đơn vị tính từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $units = [];

        foreach ($data as $unitData) {
            $units[] = self::createFromArray($unitData);
        }

        return $units;
    }

    /**
     * Sắp xếp units theo quantity
     */
    public static function sortByQuantity(array $units): array
    {
        usort($units, function (ProductUnit $a, ProductUnit $b) {
            return $a->compareWith($b);
        });

        return $units;
    }

    /**
     * Lấy base unit từ danh sách units
     */
    public static function getBaseUnit(array $units): ?ProductUnit
    {
        foreach ($units as $unit) {
            if ($unit->isBaseUnit()) {
                return $unit;
            }
        }

        return null;
    }

    /**
     * Lấy main unit (đơn vị chính) từ danh sách units
     */
    public static function getMainUnit(array $units): ?ProductUnit
    {
        $baseUnit = self::getBaseUnit($units);
        if ($baseUnit) {
            return $baseUnit;
        }

        // Nếu không có base unit, lấy unit đầu tiên
        return !empty($units) ? $units[0] : null;
    }
}
