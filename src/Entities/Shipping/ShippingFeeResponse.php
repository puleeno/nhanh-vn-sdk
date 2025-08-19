<?php

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Illuminate\Support\Collection;

/**
 * Shipping Fee Response Entity
 *
 * Entity này đại diện cho response từ API tính phí vận chuyển qua cổng Nhanh.vn
 * Chứa danh sách các dịch vụ vận chuyển và phí tương ứng
 */
class ShippingFeeResponse extends AbstractEntity
{
    protected array $fillable = [
        'services', 'totalServices', 'cached', 'cacheExpiry'
    ];

    protected array $rules = [
        'services' => 'required|array',
        'totalServices' => 'required|integer|min:0',
        'cached' => 'nullable|boolean',
        'cacheExpiry' => 'nullable|string'
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

        // Validate services array
        if ($this->hasAttribute('services') && is_array($this->getAttribute('services'))) {
            $this->validateServices($this->getAttribute('services'));
        }
    }

    /**
     * Validate services array
     */
    private function validateServices(array $services): void
    {
        foreach ($services as $index => $service) {
            if (!is_array($service)) {
                $this->addError("services.{$index}", "Dịch vụ phải là mảng");
                continue;
            }

            // Validate required fields for service
            $requiredFields = ['carrierId', 'carrierName', 'serviceId', 'serviceName', 'shipFee'];
            foreach ($requiredFields as $field) {
                if (!isset($service[$field])) {
                    $this->addError("services.{$index}.{$field}", "Trường {$field} là bắt buộc");
                }
            }

            // Validate numeric fields
            $numericFields = ['carrierId', 'serviceId', 'shipFee', 'codFee', 'declaredFee', 'estimatedDeliveryTime'];
            foreach ($numericFields as $field) {
                if (isset($service[$field]) && !is_numeric($service[$field])) {
                    $this->addError("services.{$index}.{$field}", "Trường {$field} phải là số");
                }
            }

            // Validate boolean fields
            $booleanFields = ['isBulkyGoods', 'isRequiredInsurance'];
            foreach ($booleanFields as $field) {
                if (isset($service[$field]) && !in_array($service[$field], [0, 1, true, false], true)) {
                    $this->addError("services.{$index}.{$field}", "Trường {$field} phải là boolean");
                }
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

    // Getters
    public function getServices(): array {
        $services = $this->getAttribute('services');
        if (is_array($services)) {
            return $services;
        }

        // Fallback: try to get from 'data' field (API response structure)
        $data = $this->getAttribute('data');
        if (is_array($data)) {
            return $data;
        }

        return [];
    }

    public function getTotalServices(): int {
        $total = $this->getAttribute('totalServices');
        if (is_numeric($total)) {
            return (int)$total;
        }

        // Fallback: count from services
        $services = $this->getServices();
        return count($services);
    }

    public function isCached(): bool { return (bool)$this->getAttribute('cached', false); }
    public function getCacheExpiry(): ?string { return $this->getAttribute('cacheExpiry'); }

    /**
     * Get services as Collection
     */
    public function getServicesCollection(): Collection
    {
        return collect($this->getServices());
    }

    /**
     * Check if has services
     */
    public function hasServices(): bool
    {
        return !empty($this->getServices());
    }

    /**
     * Find service by carrier ID
     */
    public function findServicesByCarrierId(int $carrierId): array
    {
        return array_filter($this->getServices(), function ($service) use ($carrierId) {
            return isset($service['carrierId']) && $service['carrierId'] == $carrierId;
        });
    }

    /**
     * Find service by service ID
     */
    public function findServiceById(int $serviceId): ?array
    {
        foreach ($this->getServices() as $service) {
            if (isset($service['serviceId']) && $service['serviceId'] == $serviceId) {
                return $service;
            }
        }
        return null;
    }

    /**
     * Get services sorted by price (lowest first)
     */
    public function getServicesSortedByPrice(): array
    {
        $services = $this->getServices();
        usort($services, function ($a, $b) {
            $totalA = ($a['shipFee'] ?? 0) + ($a['codFee'] ?? 0) + ($a['declaredFee'] ?? 0);
            $totalB = ($b['shipFee'] ?? 0) + ($b['codFee'] ?? 0) + ($b['declaredFee'] ?? 0);
            return $totalA <=> $totalB;
        });
        return $services;
    }

    /**
     * Get services sorted by delivery time (fastest first)
     */
    public function getServicesSortedByDeliveryTime(): array
    {
        $services = $this->getServices();
        usort($services, function ($a, $b) {
            return ($a['estimatedDeliveryTime'] ?? 999) <=> ($b['estimatedDeliveryTime'] ?? 999);
        });
        return $services;
    }

    /**
     * Get cheapest service
     */
    public function getCheapestService(): ?array
    {
        $sorted = $this->getServicesSortedByPrice();
        return $sorted[0] ?? null;
    }

    /**
     * Get fastest service
     */
    public function getFastestService(): ?array
    {
        $sorted = $this->getServicesSortedByDeliveryTime();
        return $sorted[0] ?? null;
    }

    /**
     * Get carrier names
     */
    public function getCarrierNames(): array
    {
        $names = [];
        foreach ($this->getServices() as $service) {
            if (isset($service['carrierName'])) {
                $names[] = $service['carrierName'];
            }
        }
        return array_unique($names);
    }

    /**
     * Get service names
     */
    public function getServiceNames(): array
    {
        $names = [];
        foreach ($this->getServices() as $service) {
            if (isset($service['serviceName'])) {
                $names[] = $service['serviceName'];
            }
        }
        return $names;
    }

    /**
     * Calculate total fees including required insurance
     */
    public function calculateTotalFees(): array
    {
        $result = [];
        foreach ($this->getServices() as $service) {
            $totalFee = ($service['shipFee'] ?? 0) + ($service['codFee'] ?? 0);

            // Add insurance fee if required
            if (isset($service['isRequiredInsurance']) && $service['isRequiredInsurance']) {
                $totalFee += ($service['declaredFee'] ?? 0);
            }

            $result[] = array_merge($service, ['totalFee' => $totalFee]);
        }
        return $result;
    }
}
