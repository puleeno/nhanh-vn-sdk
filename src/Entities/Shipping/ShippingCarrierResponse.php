<?php

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Illuminate\Support\Collection;

/**
 * Shipping Carrier Response Entity
 *
 * Entity này đại diện cho response từ API hãng vận chuyển trong Nhanh.vn
 * Chứa danh sách các hãng vận chuyển và các dịch vụ của họ
 */
class ShippingCarrierResponse extends AbstractEntity
{
    protected array $fillable = [
        'carriers', 'totalCarriers', 'cached', 'cacheExpiry'
    ];

    protected array $rules = [
        'carriers' => 'required|array',
        'totalCarriers' => 'required|integer|min:0',
        'cached' => 'nullable|boolean',
        'cacheExpiry' => 'nullable|string'
    ];

    /**
     * Validate entity theo rules
     */
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

        // Validate carriers array
        if ($this->hasAttribute('carriers') && is_array($this->getAttribute('carriers'))) {
            $this->validateCarriers($this->getAttribute('carriers'));
        }
    }

    /**
     * Validate từng field theo rule
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

            case 'array':
                if (!is_array($value)) {
                    $this->addError($field, "Trường {$field} phải là mảng");
                    return false;
                }
                break;

            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    $this->addError($field, "Trường {$field} phải là số nguyên");
                    return false;
                }
                break;

            case 'boolean':
                if (!is_bool($value) && !in_array($value, [0, 1, '0', '1'], true)) {
                    $this->addError($field, "Trường {$field} phải là boolean");
                    return false;
                }
                break;

            case 'string':
                if (!is_string($value)) {
                    $this->addError($field, "Trường {$field} phải là chuỗi");
                    return false;
                }
                break;

            case 'min':
                if (is_numeric($value) && $value < (float)$ruleValue) {
                    $this->addError($field, "Trường {$field} phải lớn hơn hoặc bằng {$ruleValue}");
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Validate carriers array
     */
    private function validateCarriers(array $carriers): void
    {
        foreach ($carriers as $index => $carrier) {
            if (!is_array($carrier)) {
                $this->addError("carriers.{$index}", "Hãng vận chuyển phải là mảng");
                continue;
            }

            if (empty($carrier['id'])) {
                $this->addError("carriers.{$index}.id", "ID hãng vận chuyển là bắt buộc");
            }

            if (empty($carrier['name'])) {
                $this->addError("carriers.{$index}.name", "Tên hãng vận chuyển là bắt buộc");
            }

            if (isset($carrier['id']) && (!is_numeric($carrier['id']) || (int)$carrier['id'] != $carrier['id'])) {
                $this->addError("carriers.{$index}.id", "ID hãng vận chuyển phải là số nguyên");
            }

            if (isset($carrier['name']) && !is_string($carrier['name'])) {
                $this->addError("carriers.{$index}.name", "Tên hãng vận chuyển phải là chuỗi");
            }

            if (isset($carrier['logo']) && !is_string($carrier['logo'])) {
                $this->addError("carriers.{$index}.logo", "Logo hãng vận chuyển phải là chuỗi");
            }

            if (isset($carrier['services']) && !is_array($carrier['services'])) {
                $this->addError("carriers.{$index}.services", "Dịch vụ hãng vận chuyển phải là mảng");
            }
        }
    }

    // Getters
    public function getCarriers(): array
    {
        $carriers = $this->getAttribute('carriers');
        if (is_array($carriers)) {
            return $carriers;
        }

        // Fallback: try to get from 'data' field (API response structure)
        $data = $this->getAttribute('data');
        if (is_array($data)) {
            return $data;
        }

        return [];
    }
    public function getTotalCarriers(): int
    {
        $total = $this->getAttribute('totalCarriers');
        if (is_numeric($total)) {
            return (int)$total;
        }

        // Fallback: count from data field
        $data = $this->getAttribute('data');
        if (is_array($data)) {
            return count($data);
        }

        return 0;
    }
    public function isCached(): bool
    {
        return (bool)$this->getAttribute('cached');
    }
    public function getCacheExpiry(): ?string
    {
        return $this->getAttribute('cacheExpiry');
    }

    /**
     * Lấy danh sách hãng vận chuyển dưới dạng Collection
     */
    public function getCarriersCollection(): Collection
    {
        $carriers = $this->getCarriers();
        if (empty($carriers)) {
            return collect();
        }

        return collect($carriers);
    }

    /**
     * Tìm hãng vận chuyển theo ID
     */
    public function findCarrierById(int $carrierId): ?array
    {
        $carriers = $this->getCarriers();
        if (empty($carriers)) {
            return null;
        }

        foreach ($carriers as $carrier) {
            if (isset($carrier['id']) && (int)$carrier['id'] === $carrierId) {
                return $carrier;
            }
        }

        return null;
    }

    /**
     * Tìm hãng vận chuyển theo tên
     */
    public function findCarrierByName(string $carrierName): ?array
    {
        $carriers = $this->getCarriers();
        if (empty($carriers)) {
            return null;
        }

        foreach ($carriers as $carrier) {
            if (isset($carrier['name']) && $carrier['name'] === $carrierName) {
                return $carrier;
            }
        }

        return null;
    }

    /**
     * Lấy danh sách tên hãng vận chuyển
     */
    public function getCarrierNames(): array
    {
        $carriers = $this->getCarriers();
        if (empty($carriers)) {
            return [];
        }

        $names = [];
        foreach ($carriers as $carrier) {
            if (isset($carrier['name'])) {
                $names[] = $carrier['name'];
            }
        }

        return $names;
    }

    /**
     * Lấy danh sách ID hãng vận chuyển
     */
    public function getCarrierIds(): array
    {
        $carriers = $this->getCarriers();
        if (empty($carriers)) {
            return [];
        }

        $ids = [];
        foreach ($carriers as $carrier) {
            if (isset($carrier['id'])) {
                $ids[] = (int)$carrier['id'];
            }
        }

        return $ids;
    }

    /**
     * Kiểm tra có hãng vận chuyển nào không
     */
    public function hasCarriers(): bool
    {
        $carriers = $this->getCarriers();
        return !empty($carriers) && is_array($carriers);
    }

    /**
     * Lấy tổng số dịch vụ vận chuyển
     */
    public function getTotalServices(): int
    {
        $carriers = $this->getCarriers();
        if (empty($carriers)) {
            return 0;
        }

        $total = 0;
        foreach ($carriers as $carrier) {
            if (isset($carrier['services']) && is_array($carrier['services'])) {
                $total += count($carrier['services']);
            }
        }

        return $total;
    }

    /**
     * Lấy danh sách tất cả dịch vụ vận chuyển
     */
    public function getAllServices(): array
    {
        $carriers = $this->getCarriers();
        if (empty($carriers)) {
            return [];
        }

        $allServices = [];
        foreach ($carriers as $carrier) {
            if (isset($carrier['services']) && is_array($carrier['services'])) {
                foreach ($carrier['services'] as $service) {
                    $service['carrierId'] = $carrier['id'];
                    $service['carrierName'] = $carrier['name'];
                    $allServices[] = $service;
                }
            }
        }

        return $allServices;
    }

    /**
     * Kiểm tra cache có hết hạn chưa
     */
    public function isCacheExpired(): bool
    {
        $expiry = $this->getCacheExpiry();
        if (empty($expiry)) {
            return true;
        }

        try {
            $expiryTime = strtotime($expiry);
            return $expiryTime === false || $expiryTime < time();
        } catch (Exception $e) {
            return true;
        }
    }

    /**
     * Lấy thời gian còn lại của cache (giây)
     */
    public function getCacheTimeLeft(): int
    {
        $expiry = $this->getCacheExpiry();
        if (empty($expiry)) {
            return 0;
        }

        try {
            $expiryTime = strtotime($expiry);
            if ($expiryTime === false) {
                return 0;
            }

            $timeLeft = $expiryTime - time();
            return max(0, $timeLeft);
        } catch (Exception $e) {
            return 0;
        }
    }
}
