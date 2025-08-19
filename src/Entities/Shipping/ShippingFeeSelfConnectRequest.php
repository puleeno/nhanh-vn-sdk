<?php

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Shipping Fee Self Connect Request Entity
 *
 * Entity này đại diện cho request tính phí vận chuyển tự kết nối
 * Dùng cho API /api/shipping/feeselfconnect
 */
class ShippingFeeSelfConnectRequest extends AbstractEntity
{
    protected array $fillable = [
        'carrierId', 'fromMobile', 'fromCityName', 'fromDistrictName', 'fromWardName', 'fromAddress',
        'toCityName', 'toDistrictName', 'toWardName', 'toAddress',
        'codMoney', 'shippingWeight', 'productIds'
    ];

    protected array $rules = [
        'carrierId' => 'required|integer',
        'fromMobile' => 'nullable|string|max:20',
        'fromCityName' => 'required|string|max:255',
        'fromDistrictName' => 'required|string|max:255',
        'fromWardName' => 'nullable|string|max:255',
        'fromAddress' => 'nullable|string|max:500',
        'toCityName' => 'required|string|max:255',
        'toDistrictName' => 'required|string|max:255',
        'toWardName' => 'nullable|string|max:255',
        'toAddress' => 'nullable|string|max:500',
        'codMoney' => 'nullable|integer|min:0',
        'shippingWeight' => 'nullable|integer|min:0',
        'productIds' => 'nullable|array'
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

        // Validate special requirements based on carrier
        $this->validateCarrierSpecificRequirements();
    }

    /**
     * Validate carrier specific requirements
     */
    private function validateCarrierSpecificRequirements(): void
    {
        $carrierId = $this->getAttribute('carrierId');

        // Giao hàng nhanh (ID = 5) requirements
        if ($carrierId == 5) {
            if (!$this->hasAttribute('fromMobile') || empty($this->getAttribute('fromMobile'))) {
                $this->addError('fromMobile', 'fromMobile là bắt buộc khi carrierId = 5 (Giao hàng nhanh)');
            }

            if (!$this->hasAttribute('fromAddress') || empty($this->getAttribute('fromAddress'))) {
                $this->addError('fromAddress', 'fromAddress là bắt buộc khi carrierId = 5 (Giao hàng nhanh)');
            }
        }

        // JT Express, Shopee Express requirements
        $requireWardCarriers = [24, 30]; // JT Express, Shopee Express (example IDs)
        if (in_array($carrierId, $requireWardCarriers)) {
            if (!$this->hasAttribute('toWardName') || empty($this->getAttribute('toWardName'))) {
                $this->addError('toWardName', "toWardName là bắt buộc khi carrierId = {$carrierId}");
            }
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
    public function getCarrierId(): int { return $this->getAttribute('carrierId', 0); }
    public function getFromMobile(): ?string { return $this->getAttribute('fromMobile'); }
    public function getFromCityName(): string { return $this->getAttribute('fromCityName', ''); }
    public function getFromDistrictName(): string { return $this->getAttribute('fromDistrictName', ''); }
    public function getFromWardName(): ?string { return $this->getAttribute('fromWardName'); }
    public function getFromAddress(): ?string { return $this->getAttribute('fromAddress'); }
    public function getToCityName(): string { return $this->getAttribute('toCityName', ''); }
    public function getToDistrictName(): string { return $this->getAttribute('toDistrictName', ''); }
    public function getToWardName(): ?string { return $this->getAttribute('toWardName'); }
    public function getToAddress(): ?string { return $this->getAttribute('toAddress'); }
    public function getCodMoney(): int { return $this->getAttribute('codMoney', 0); }
    public function getShippingWeight(): ?int { return $this->getAttribute('shippingWeight'); }
    public function getProductIds(): ?array { return $this->getAttribute('productIds'); }

    // Setters
    public function setCarrierId(int $value): self { $this->setAttribute('carrierId', $value); return $this; }
    public function setFromMobile(?string $value): self { $this->setAttribute('fromMobile', $value); return $this; }
    public function setFromCityName(string $value): self { $this->setAttribute('fromCityName', $value); return $this; }
    public function setFromDistrictName(string $value): self { $this->setAttribute('fromDistrictName', $value); return $this; }
    public function setFromWardName(?string $value): self { $this->setAttribute('fromWardName', $value); return $this; }
    public function setFromAddress(?string $value): self { $this->setAttribute('fromAddress', $value); return $this; }
    public function setToCityName(string $value): self { $this->setAttribute('toCityName', $value); return $this; }
    public function setToDistrictName(string $value): self { $this->setAttribute('toDistrictName', $value); return $this; }
    public function setToWardName(?string $value): self { $this->setAttribute('toWardName', $value); return $this; }
    public function setToAddress(?string $value): self { $this->setAttribute('toAddress', $value); return $this; }
    public function setCodMoney(int $value): self { $this->setAttribute('codMoney', $value); return $this; }
    public function setShippingWeight(?int $value): self { $this->setAttribute('shippingWeight', $value); return $this; }
    public function setProductIds(?array $value): self { $this->setAttribute('productIds', $value); return $this; }

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

    /**
     * Check if is Giao hàng nhanh
     */
    public function isGiaoHangNhanh(): bool
    {
        return $this->getCarrierId() == 5;
    }

    /**
     * Check if requires ward name
     */
    public function requiresWardName(): bool
    {
        $requireWardCarriers = [5, 24, 30]; // Giao hàng nhanh, JT Express, Shopee Express
        return in_array($this->getCarrierId(), $requireWardCarriers);
    }
}
