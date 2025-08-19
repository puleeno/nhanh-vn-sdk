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
}
