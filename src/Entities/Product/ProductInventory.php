<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Inventory entity
 */
class ProductInventory extends AbstractEntity
{
    protected function validate(): void
    {
        // Basic validation
        if (empty($this->attributes)) {
            $this->addError('base', 'Inventory data không được để trống');
        }
    }

    // Basic getters
    public function getRemain(): ?int
    {
        return $this->getAttribute('remain');
    }

    public function getShipping(): ?int
    {
        return $this->getAttribute('shipping');
    }

    public function getHolding(): ?int
    {
        return $this->getAttribute('holding');
    }

    public function getDamage(): ?int
    {
        return $this->getAttribute('damage');
    }

    public function getAvailable(): ?int
    {
        return $this->getAttribute('available');
    }

    public function getWarranty(): ?int
    {
        return $this->getAttribute('warranty');
    }

    public function getWarrantyHolding(): ?int
    {
        return $this->getAttribute('warrantyHolding');
    }

    public function getTransferring(): ?int
    {
        return $this->getAttribute('transferring');
    }

    public function getDepots(): array
    {
        return $this->getAttribute('depots', []);
    }

    // Business logic methods
    public function getTotalStock(): int
    {
        return $this->getRemain() ?? 0;
    }

    public function getTotalInTransit(): int
    {
        return ($this->getShipping() ?? 0) + ($this->getTransferring() ?? 0);
    }

    public function getTotalHolding(): int
    {
        return ($this->getHolding() ?? 0) + ($this->getWarrantyHolding() ?? 0);
    }

    public function getTotalUnavailable(): int
    {
        return ($this->getDamage() ?? 0) + ($this->getWarranty() ?? 0);
    }

    public function getTotalQuantity(): int
    {
        return $this->getTotalStock() + $this->getTotalInTransit() + $this->getTotalHolding() + $this->getTotalUnavailable();
    }

    public function isInStock(): bool
    {
        return $this->getAvailable() > 0;
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->getAvailable() > 0 && $this->getAvailable() <= $threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->getAvailable() <= 0;
    }

    public function hasStock(): bool
    {
        return $this->getTotalStock() > 0;
    }

    public function hasInTransit(): bool
    {
        return $this->getTotalInTransit() > 0;
    }

    public function hasHolding(): bool
    {
        return $this->getTotalHolding() > 0;
    }

    public function hasDamage(): bool
    {
        return $this->getDamage() > 0;
    }

    public function hasWarranty(): bool
    {
        return $this->getWarranty() > 0;
    }

    public function getStockLevel(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of Stock';
        }

        if ($this->isLowStock()) {
            return 'Low Stock';
        }

        if ($this->getAvailable() > 20) {
            return 'In Stock';
        }

        return 'Limited Stock';
    }

    public function getStockPercentage(): float
    {
        $total = $this->getTotalQuantity();
        if ($total === 0) {
            return 0;
        }

        return round(($this->getAvailable() / $total) * 100, 2);
    }

    public function getDepotCount(): int
    {
        return count($this->getDepots());
    }

    public function hasMultipleDepots(): bool
    {
        return $this->getDepotCount() > 1;
    }

    /**
     * Lấy inventory theo depot ID
     */
    public function getDepotInventory(int $depotId): ?array
    {
        return $this->getDepots()[$depotId] ?? null;
    }

    /**
     * Lấy tất cả depot IDs
     */
    public function getDepotIds(): array
    {
        return array_keys($this->getDepots());
    }

    /**
     * Lấy depot có stock cao nhất
     */
    public function getHighestStockDepot(): ?array
    {
        if (empty($this->getDepots())) {
            return null;
        }

        $highest = null;
        $maxStock = -1;

        foreach ($this->getDepots() as $depotId => $depot) {
            $stock = $depot['available'] ?? 0;
            if ($stock > $maxStock) {
                $maxStock = $stock;
                $highest = array_merge(['depotId' => $depotId], $depot);
            }
        }

        return $highest;
    }

    /**
     * Lấy depot có stock thấp nhất
     */
    public function getLowestStockDepot(): ?array
    {
        if (empty($this->getDepots())) {
            return null;
        }

        $lowest = null;
        $minStock = PHP_INT_MAX;

        foreach ($this->getDepots() as $depotId => $depot) {
            $stock = $depot['available'] ?? 0;
            if ($stock < $minStock) {
                $minStock = $stock;
                $lowest = array_merge(['depotId' => $depotId], $depot);
            }
        }

        return $lowest;
    }

    /**
     * Lấy tổng stock theo depot
     */
    public function getTotalStockByDepot(): array
    {
        $totals = [];

        foreach ($this->getDepots() as $depotId => $depot) {
            $totals[$depotId] = [
                'depotId' => $depotId,
                'total' => ($depot['remain'] ?? 0) + ($depot['shipping'] ?? 0) + ($depot['holding'] ?? 0) + ($depot['damage'] ?? 0) + ($depot['warranty'] ?? 0) + ($depot['warrantyHolding'] ?? 0) + ($depot['transferring'] ?? 0),
                'available' => $depot['available'] ?? 0,
                'inTransit' => ($depot['shipping'] ?? 0) + ($depot['transferring'] ?? 0),
                'holding' => ($depot['holding'] ?? 0) + ($depot['warrantyHolding'] ?? 0),
                'unavailable' => ($depot['damage'] ?? 0) + ($depot['warranty'] ?? 0)
            ];
        }

        return $totals;
    }
}
