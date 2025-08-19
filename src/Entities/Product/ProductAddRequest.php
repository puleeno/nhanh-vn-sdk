<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Add Request DTO
 *
 * DTO này đại diện cho request data khi thêm/sửa sản phẩm
 * theo API specification của Nhanh.vn
 *
 * @package Puleeno\NhanhVn\Entities\Product
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @since 2.0.0
 */
class ProductAddRequest extends AbstractEntity
{
    /**
     * Validate product add request data
     */
    protected function validate(): void
    {
        // Required fields validation
        if (!$this->getId()) {
            $this->addError('id', 'ID sản phẩm trên hệ thống riêng không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên sản phẩm không được để trống');
        }

        if (!$this->getPrice()) {
            $this->addError('price', 'Giá sản phẩm không được để trống');
        }

        // Data type validation
        if ($this->getPrice() && !is_numeric($this->getPrice())) {
            $this->addError('price', 'Giá sản phẩm phải là số');
        }

        if ($this->getImportPrice() && !is_numeric($this->getImportPrice())) {
            $this->addError('importPrice', 'Giá nhập phải là số');
        }

        if ($this->getWholesalePrice() && !is_numeric($this->getWholesalePrice())) {
            $this->addError('wholesalePrice', 'Giá bán buôn phải là số');
        }

        if ($this->getShippingWeight() && !is_numeric($this->getShippingWeight())) {
            $this->addError('shippingWeight', 'Cân nặng vận chuyển phải là số');
        }

        if ($this->getVat() && !is_numeric($this->getVat())) {
            $this->addError('vat', 'Thuế VAT phải là số');
        }

        // Business logic validation
        if ($this->getPrice() && $this->getImportPrice() && $this->getPrice() < $this->getImportPrice()) {
            $this->addError('price', 'Giá bán không được thấp hơn giá nhập');
        }

        if ($this->getVat() && ($this->getVat() < 0 || $this->getVat() > 100)) {
            $this->addError('vat', 'Thuế VAT phải từ 0 đến 100%');
        }
    }

    // Required fields
    public function getId(): ?string
    {
        return $this->getAttribute('id');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getPrice(): ?int
    {
        return $this->getAttribute('price');
    }

    // Optional fields
    public function getIdNhanh(): ?int
    {
        return $this->getAttribute('idNhanh');
    }

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function getBarcode(): ?string
    {
        return $this->getAttribute('barcode');
    }

    public function getShippingWeight(): ?int
    {
        return $this->getAttribute('shippingWeight');
    }

    public function getVat(): ?int
    {
        return $this->getAttribute('vat');
    }

    public function getImportPrice(): ?int
    {
        return $this->getAttribute('importPrice');
    }

    public function getWholesalePrice(): ?int
    {
        return $this->getAttribute('wholesalePrice');
    }

    public function getStatus(): ?string
    {
        return $this->getAttribute('status');
    }

    public function getCategoryId(): ?int
    {
        return $this->getAttribute('categoryId');
    }

    public function getOldPrice(): ?int
    {
        return $this->getAttribute('oldPrice');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getContent(): ?string
    {
        return $this->getAttribute('content');
    }

    public function getExternalImages(): array
    {
        return $this->getAttribute('externalImages', []);
    }

    // Business logic methods
    public function hasExternalImages(): bool
    {
        return !empty($this->getExternalImages());
    }

    public function getExternalImagesCount(): int
    {
        return count($this->getExternalImages());
    }

    public function isUpdate(): bool
    {
        return $this->getIdNhanh() !== null;
    }

    public function isNew(): bool
    {
        return !$this->isUpdate();
    }

    public function hasDiscount(): bool
    {
        return $this->getOldPrice() && $this->getPrice() && $this->getOldPrice() > $this->getPrice();
    }

    public function getDiscountAmount(): ?int
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

        return round(($this->getDiscountAmount() / $this->getOldPrice()) * 100, 2);
    }

    /**
     * Convert to API request format
     */
    public function toApiFormat(): array
    {
        $data = [];

        // Required fields
        $data['id'] = $this->getId();
        $data['name'] = $this->getName();
        $data['price'] = $this->getPrice();

        // Optional fields
        if ($this->getIdNhanh()) {
            $data['idNhanh'] = $this->getIdNhanh();
        }

        if ($this->getCode()) {
            $data['code'] = $this->getCode();
        }

        if ($this->getBarcode()) {
            $data['barcode'] = $this->getBarcode();
        }

        if ($this->getShippingWeight()) {
            $data['shippingWeight'] = $this->getShippingWeight();
        }

        if ($this->getVat()) {
            $data['vat'] = $this->getVat();
        }

        if ($this->getImportPrice()) {
            $data['importPrice'] = $this->getImportPrice();
        }

        if ($this->getWholesalePrice()) {
            $data['wholesalePrice'] = $this->getWholesalePrice();
        }

        if ($this->getStatus()) {
            $data['status'] = $this->getStatus();
        }

        if ($this->getCategoryId()) {
            $data['categoryId'] = $this->getCategoryId();
        }

        if ($this->getOldPrice()) {
            $data['oldPrice'] = $this->getOldPrice();
        }

        if ($this->getDescription()) {
            $data['description'] = $this->getDescription();
        }

        if ($this->getContent()) {
            $data['content'] = $this->getContent();
        }

        if ($this->hasExternalImages()) {
            $data['externalImages'] = $this->getExternalImages();
        }

        return $data;
    }

    /**
     * Create from array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Create multiple from array data
     */
    public static function createMultipleFromArray(array $productsData): array
    {
        $requests = [];

        foreach ($productsData as $productData) {
            $requests[] = self::createFromArray($productData);
        }

        return $requests;
    }

    /**
     * Validate multiple requests
     */
    public static function validateMultiple(array $requests): array
    {
        $errors = [];

        foreach ($requests as $index => $request) {
            if (!$request->isValid()) {
                $errors[$index] = $request->getErrors();
            }
        }

        return $errors;
    }
}
