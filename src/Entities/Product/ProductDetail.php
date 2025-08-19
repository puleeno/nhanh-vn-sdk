<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Detail entity với thông tin chi tiết
 */
class ProductDetail extends Product
{
    protected function validate(): void
    {
        parent::validate();

        // Additional validation cho product detail
        if (!$this->getIdNhanh()) {
            $this->addError('idNhanh', 'ID sản phẩm không được để trống');
        }
    }

    // Additional getters cho product detail
    public function getBarcode(): ?string
    {
        return $this->getAttribute('barcode');
    }

    public function getOtherName(): ?string
    {
        return $this->getAttribute('otherName');
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

    public function getPreviewLink(): ?string
    {
        return $this->getAttribute('previewLink');
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

    public function getTypeId(): ?int
    {
        return $this->getAttribute('typeId');
    }

    public function getTypeName(): ?string
    {
        return $this->getAttribute('typeName');
    }

    public function getAvgCost(): ?float
    {
        return $this->getAttribute('avgCost');
    }

    public function getCountryName(): ?string
    {
        return $this->getAttribute('countryName');
    }

    public function getUnit(): ?string
    {
        return $this->getAttribute('unit');
    }

    public function getImportType(): ?int
    {
        return $this->getAttribute('importType');
    }

    public function getImportTypeLabel(): ?string
    {
        return $this->getAttribute('importTypeLabel');
    }

    // Business logic methods
    public function isHot(): bool
    {
        return $this->getShowHot() === 1;
    }

    public function isNewProduct(): bool
    {
        return $this->getShowNew() === 1;
    }

    public function isShowOnHome(): bool
    {
        return $this->getShowHome() === 1;
    }

    public function hasDiscount(): bool
    {
        $oldPrice = $this->getOldPrice();
        $currentPrice = $this->getPrice();

        return $oldPrice && $currentPrice && $oldPrice > $currentPrice;
    }

    public function getDiscountAmount(): ?float
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return $this->getOldPrice() - $this->getPrice();
    }

    public function getDiscountPercentage(): ?float
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        $discountAmount = $this->getDiscountAmount();
        $oldPrice = $this->getOldPrice();

        return round(($discountAmount / $oldPrice) * 100, 2);
    }

    public function getFormattedOldPrice(): string
    {
        $price = $this->getOldPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : '';
    }

    public function getFormattedWholesalePrice(): string
    {
        $price = $this->getWholesalePrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : '';
    }

    public function getFormattedImportPrice(): string
    {
        $price = $this->getImportPrice();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : '';
    }

    public function getVatAmount(): ?float
    {
        $price = $this->getPrice();
        $vat = $this->getVat();

        if (!$price || !$vat) {
            return null;
        }

        return ($price * $vat) / 100;
    }

    public function getPriceWithVat(): ?float
    {
        $price = $this->getPrice();
        $vatAmount = $this->getVatAmount();

        if (!$price) {
            return null;
        }

        return $price + ($vatAmount ?? 0);
    }

    public function getFormattedPriceWithVat(): string
    {
        $price = $this->getPriceWithVat();
        return $price ? number_format($price, 0, ',', '.') . ' VNĐ' : '';
    }

    public function getDimensions(): string
    {
        $dimensions = [];

        if ($this->getLength()) {
            $dimensions[] = $this->getLength() . 'cm';
        }

        if ($this->getWidth()) {
            $dimensions[] = $this->getWidth() . 'cm';
        }

        if ($this->getHeight()) {
            $dimensions[] = $this->getHeight() . 'cm';
        }

        return empty($dimensions) ? 'N/A' : implode(' x ', $dimensions);
    }

    public function getFormattedShippingWeight(): string
    {
        $weight = $this->getShippingWeight();
        if (!$weight) {
            return 'N/A';
        }

        if ($weight >= 1000) {
            return round($weight / 1000, 2) . ' kg';
        }

        return $weight . ' g';
    }

    public function hasHighlight(): bool
    {
        return !empty($this->getHighlight());
    }

    public function getHighlightText(): string
    {
        $highlights = $this->getHighlight();
        return implode(', ', $highlights);
    }

    public function hasContent(): bool
    {
        return !empty($this->getContent());
    }

    public function getShortContent(int $length = 200): string
    {
        $content = $this->getContent() ?: '';
        if (strlen($content) <= $length) {
            return $content;
        }

        return substr($content, 0, $length) . '...';
    }
}
