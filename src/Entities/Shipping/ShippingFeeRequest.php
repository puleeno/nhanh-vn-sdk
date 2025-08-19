<?php

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Shipping Fee Request Entity
 *
 * Entity này đại diện cho request tính phí vận chuyển qua cổng Nhanh.vn
 * Dùng cho API /api/shipping/fee
 */
class ShippingFeeRequest extends AbstractEntity
{
    protected array $fillable = [
        'fromCityName', 'fromDistrictName', 'toCityName', 'toDistrictName',
        'codMoney', 'shippingWeight', 'productIds', 'carrierIds',
        'length', 'width', 'height'
    ];

    protected array $rules = [
        'fromCityName' => 'required|string|max:255',
        'fromDistrictName' => 'required|string|max:255',
        'toCityName' => 'required|string|max:255',
        'toDistrictName' => 'required|string|max:255',
        'codMoney' => 'nullable|integer|min:0',
        'shippingWeight' => 'nullable|integer|min:0|max:100000',
        'productIds' => 'nullable|array',
        'carrierIds' => 'nullable|array',
        'length' => 'nullable|integer|min:0',
        'width' => 'nullable|integer|min:0',
        'height' => 'nullable|integer|min:0'
    ];

    protected function validate(): void
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rule) {
            if (!$this->hasAttribute($field)) {
                continue;
            }

            $value = $this->getAttribute($field);
            $this->validateField($field, $value, $rule);
        }

        // Validate either shippingWeight or productIds must be provided
        if (!$this->hasAttribute('shippingWeight') && !$this->hasAttribute('productIds')) {
            $this->addError('shippingWeight', 'Phải cung cấp shippingWeight hoặc productIds');
        }

        // Validate productIds format
        if ($this->hasAttribute('productIds') && is_array($this->getAttribute('productIds'))) {
            $this->validateProductIds($this->getAttribute('productIds'));
        }

        // Validate carrierIds format
        if ($this->hasAttribute('carrierIds') && is_array($this->getAttribute('carrierIds'))) {
            $this->validateCarrierIds($this->getAttribute('carrierIds'));
        }
    }

    /**
     * Validate product IDs format
     */
    private function validateProductIds(array $productIds): void
    {
        foreach ($productIds as $productId => $quantity) {
            if (!is_numeric($productId)) {
                $this->addError('productIds', "Product ID {$productId} phải là số");
                continue;
            }

            if (!is_numeric($quantity) || $quantity <= 0) {
                $this->addError('productIds', "Quantity cho product {$productId} phải là số dương");
            }
        }
    }

    /**
     * Validate carrier IDs format
     */
    private function validateCarrierIds(array $carrierIds): void
    {
        foreach ($carrierIds as $carrierId) {
            if (!is_numeric($carrierId)) {
                $this->addError('carrierIds', "Carrier ID {$carrierId} phải là số");
            }
        }
    }

    /**
     * Validate single field according to rule
     */
    private function validateField(string $field, mixed $value, string $rule): void
    {
        $rules = explode('|', $rule);

        foreach ($rules as $singleRule) {
            if (strpos($singleRule, ':') !== false) {
                [$ruleName, $ruleValue] = explode(':', $singleRule, 2);
            } else {
                $ruleName = $singleRule;
                $ruleValue = null;
            }

            if (!$this->validateSingleRule($field, $value, $ruleName, $ruleValue)) {
                break;
            }
        }
    }

    /**
     * Validate single rule
     */
    private function validateSingleRule(string $field, mixed $value, string $ruleName, ?string $ruleValue): bool
    {
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== 0) {
                    $this->addError($field, "Trường {$field} là bắt buộc");
                    return false;
                }
                break;

            case 'nullable':
                if (is_null($value)) {
                    return true; // Skip other validations for null values
                }
                break;

            case 'string':
                if (!is_string($value)) {
                    $this->addError($field, "Trường {$field} phải là chuỗi");
                    return false;
                }
                break;

            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    $this->addError($field, "Trường {$field} phải là số nguyên");
                    return false;
                }
                break;

            case 'array':
                if (!is_array($value)) {
                    $this->addError($field, "Trường {$field} phải là mảng");
                    return false;
                }
                break;

            case 'min':
                if (is_numeric($value) && $value < (float)$ruleValue) {
                    $this->addError($field, "Trường {$field} phải lớn hơn hoặc bằng {$ruleValue}");
                    return false;
                }
                break;

            case 'max':
                if (is_numeric($value) && $value > (float)$ruleValue) {
                    $this->addError($field, "Trường {$field} phải nhỏ hơn hoặc bằng {$ruleValue}");
                    return false;
                } elseif (is_string($value) && strlen($value) > (int)$ruleValue) {
                    $this->addError($field, "Trường {$field} không được vượt quá {$ruleValue} ký tự");
                    return false;
                }
                break;
        }

        return true;
    }

    // Getters
    public function getFromCityName(): string { return $this->getAttribute('fromCityName', ''); }
    public function getFromDistrictName(): string { return $this->getAttribute('fromDistrictName', ''); }
    public function getToCityName(): string { return $this->getAttribute('toCityName', ''); }
    public function getToDistrictName(): string { return $this->getAttribute('toDistrictName', ''); }
    public function getCodMoney(): int { return $this->getAttribute('codMoney', 0); }
    public function getShippingWeight(): ?int { return $this->getAttribute('shippingWeight'); }
    public function getProductIds(): ?array { return $this->getAttribute('productIds'); }
    public function getCarrierIds(): ?array { return $this->getAttribute('carrierIds'); }
    public function getLength(): ?int { return $this->getAttribute('length'); }
    public function getWidth(): ?int { return $this->getAttribute('width'); }
    public function getHeight(): ?int { return $this->getAttribute('height'); }

    // Setters
    public function setFromCityName(string $value): self { $this->setAttribute('fromCityName', $value); return $this; }
    public function setFromDistrictName(string $value): self { $this->setAttribute('fromDistrictName', $value); return $this; }
    public function setToCityName(string $value): self { $this->setAttribute('toCityName', $value); return $this; }
    public function setToDistrictName(string $value): self { $this->setAttribute('toDistrictName', $value); return $this; }
    public function setCodMoney(int $value): self { $this->setAttribute('codMoney', $value); return $this; }
    public function setShippingWeight(?int $value): self { $this->setAttribute('shippingWeight', $value); return $this; }
    public function setProductIds(?array $value): self { $this->setAttribute('productIds', $value); return $this; }
    public function setCarrierIds(?array $value): self { $this->setAttribute('carrierIds', $value); return $this; }
    public function setLength(?int $value): self { $this->setAttribute('length', $value); return $this; }
    public function setWidth(?int $value): self { $this->setAttribute('width', $value); return $this; }
    public function setHeight(?int $value): self { $this->setAttribute('height', $value); return $this; }

    /**
     * Check if has dimensions
     */
    public function hasDimensions(): bool
    {
        return $this->hasAttribute('length') && $this->hasAttribute('width') && $this->hasAttribute('height');
    }

    /**
     * Check if using product IDs
     */
    public function hasProductIds(): bool
    {
        return $this->hasAttribute('productIds') && !empty($this->getAttribute('productIds'));
    }

    /**
     * Check if using shipping weight
     */
    public function hasShippingWeight(): bool
    {
        return $this->hasAttribute('shippingWeight') && $this->getAttribute('shippingWeight') > 0;
    }
}
