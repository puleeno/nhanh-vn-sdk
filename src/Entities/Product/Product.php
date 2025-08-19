<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product entity cơ bản
 */
class Product extends AbstractEntity
{
    protected function validate(): void
    {
        // Basic validation - sẽ được override bởi các class con
        if (empty($this->attributes)) {
            $this->addError('base', 'Product data không được để trống');
        }
    }

    // Basic getters
    public function getIdNhanh(): ?int
    {
        return $this->getAttribute('idNhanh');
    }

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getPrice(): ?float
    {
        return $this->getAttribute('price');
    }

    public function getStatus(): ?string
    {
        return $this->getAttribute('status');
    }

    public function getCategoryId(): ?int
    {
        return $this->getAttribute('categoryId');
    }

    public function getBrandId(): ?int
    {
        return $this->getAttribute('brandId');
    }

    public function getBrandName(): ?string
    {
        return $this->getAttribute('brandName');
    }

    public function getImage(): ?string
    {
        return $this->getAttribute('image');
    }

    public function getImages(): array
    {
        return $this->getAttribute('images', []);
    }

    public function getCreatedDateTime(): ?string
    {
        return $this->getAttribute('createdDateTime');
    }

    public function getUpdatedAt(): ?int
    {
        return $this->getAttribute('updatedAt');
    }

    public function getId(): ?int
    {
        return $this->getIdNhanh();
    }

    public function getCategoryName(): ?string
    {
        return $this->getAttribute('categoryName');
    }

    public function getInventory(): array
    {
        return $this->getAttribute('inventory', []);
    }

    public function getAvailableQuantity(): int
    {
        $inventory = $this->getInventory();
        return isset($inventory['available']) ? (int)$inventory['available'] : 0;
    }

    public function getTotalQuantity(): int
    {
        $inventory = $this->getInventory();
        return isset($inventory['remain']) ? (int)$inventory['remain'] : 0;
    }

    // Business logic methods
    public function isActive(): bool
    {
        return $this->getStatus() === 'Active';
    }

    public function isNew(): bool
    {
        return $this->getStatus() === 'New';
    }

    public function isInactive(): bool
    {
        return $this->getStatus() === 'Inactive';
    }

    public function isOutOfStock(): bool
    {
        return $this->getStatus() === 'OutOfStock';
    }

    public function hasImage(): bool
    {
        return !empty($this->getImage());
    }

    public function hasMultipleImages(): bool
    {
        return count($this->getImages()) > 1;
    }

    public function getMainImage(): ?string
    {
        return $this->getImage() ?: ($this->getImages()[0] ?? null);
    }

    public function getFormattedPrice(): string
    {
        $price = $this->getPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : 'Liên hệ';
    }

    public function getShortDescription(int $length = 100): string
    {
        $name = $this->getName() ?: '';
        if (strlen($name) <= $length) {
            return $name;
        }

        return substr($name, 0, $length) . '...';
    }

    // Additional getters for product detail API
    public function getOtherName(): ?string
    {
        return $this->getAttribute('otherName');
    }

    public function getBarcode(): ?string
    {
        return $this->getAttribute('barcode');
    }

    public function getImportPrice(): ?float
    {
        return $this->getAttribute('importPrice');
    }

    public function getOldPrice(): ?float
    {
        return $this->getAttribute('oldPrice');
    }

    public function getWholesalePrice(): ?float
    {
        return $this->getAttribute('wholesalePrice');
    }

    public function getVat(): ?int
    {
        return $this->getAttribute('vat');
    }

    public function getPreviewLink(): ?string
    {
        return $this->getAttribute('previewLink');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getHighlight(): array
    {
        return $this->getAttribute('highlight', []);
    }

    public function getContent(): ?string
    {
        return $this->getAttribute('content');
    }

    public function getShowHot(): bool
    {
        return (bool) $this->getAttribute('showHot');
    }

    public function getShowNew(): bool
    {
        return (bool) $this->getAttribute('showNew');
    }

    public function getShowHome(): bool
    {
        return (bool) $this->getAttribute('showHome');
    }

    public function getWidth(): ?int
    {
        return $this->getAttribute('width');
    }

    public function getHeight(): ?int
    {
        return $this->getAttribute('height');
    }

    public function getLength(): ?int
    {
        return $this->getAttribute('length');
    }

    public function getShippingWeight(): ?int
    {
        return $this->getAttribute('shippingWeight');
    }

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

    public function getTypeId(): ?int
    {
        return $this->getAttribute('typeId');
    }

    public function getTypeName(): ?string
    {
        return $this->getAttribute('typeName');
    }

    public function getCountryName(): ?string
    {
        return $this->getAttribute('countryName');
    }

    public function getUnit(): ?string
    {
        return $this->getAttribute('unit');
    }

    public function getParentId(): ?int
    {
        return $this->getAttribute('parentId');
    }

    public function getMerchantCategoryId(): ?int
    {
        return $this->getAttribute('merchantCategoryId');
    }

    public function getMerchantProductId(): ?int
    {
        return $this->getAttribute('merchantProductId');
    }

    public function getAvgCost(): ?float
    {
        return $this->getAttribute('avgCost');
    }

    public function getImportType(): ?int
    {
        return $this->getAttribute('importType');
    }

    public function getImportTypeLabel(): ?string
    {
        return $this->getAttribute('importTypeLabel');
    }

    public function getAttributes(): array
    {
        return $this->getAttribute('attributes', []);
    }

    public function getUnits(): array
    {
        return $this->getAttribute('units', []);
    }

    public function getWebsiteInfo(): array
    {
        return $this->getAttribute('websiteInfo', []);
    }
}
