<?php

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Illuminate\Support\Collection;

/**
 * Shipping Fee Self Connect Response Entity
 *
 * Entity này đại diện cho response từ API tính phí vận chuyển tự kết nối
 * Chứa danh sách các dịch vụ vận chuyển tự kết nối và phí tương ứng
 */
class ShippingFeeSelfConnectResponse extends AbstractEntity
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

            // Validate required fields for self-connect service
            $requiredFields = ['carrierAccountId', 'carrierAccountName', 'carrierName', 'serviceCode', 'serviceName'];
            foreach ($requiredFields as $field) {
                if (!isset($service[$field])) {
                    $this->addError("services.{$index}.{$field}", "Trường {$field} là bắt buộc");
                }
            }

            // Validate numeric fields
            $numericFields = ['carrierAccountId', 'carrierShopId', 'customerShipFee', 'totalFee', 'shipFee'];
            foreach ($numericFields as $field) {
                if (isset($service[$field]) && !is_numeric($service[$field])) {
                    $this->addError("services.{$index}.{$field}", "Trường {$field} phải là số");
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
     * Find service by carrier account ID
     */
    public function findServiceByCarrierAccountId(int $carrierAccountId): ?array
    {
        foreach ($this->getServices() as $service) {
            if (isset($service['carrierAccountId']) && $service['carrierAccountId'] == $carrierAccountId) {
                return $service;
            }
        }
        return null;
    }

    /**
     * Find services by carrier name
     */
    public function findServicesByCarrierName(string $carrierName): array
    {
        return array_filter($this->getServices(), function ($service) use ($carrierName) {
            return isset($service['carrierName']) && strcasecmp($service['carrierName'], $carrierName) === 0;
        });
    }

    /**
     * Find service by service code
     */
    public function findServiceByCode(string $serviceCode): ?array
    {
        foreach ($this->getServices() as $service) {
            if (isset($service['serviceCode']) && $service['serviceCode'] === $serviceCode) {
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
            return ($a['totalFee'] ?? $a['shipFee'] ?? 0) <=> ($b['totalFee'] ?? $b['shipFee'] ?? 0);
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
     * Get carrier account names
     */
    public function getCarrierAccountNames(): array
    {
        $names = [];
        foreach ($this->getServices() as $service) {
            if (isset($service['carrierAccountName'])) {
                $names[] = $service['carrierAccountName'];
            }
        }
        return array_unique($names);
    }

    /**
     * Get total fees summary
     */
    public function getFeesSummary(): array
    {
        $summary = [
            'minFee' => null,
            'maxFee' => null,
            'avgFee' => 0,
            'totalServices' => $this->getTotalServices()
        ];

        $services = $this->getServices();
        if (empty($services)) {
            return $summary;
        }

        $fees = [];
        foreach ($services as $service) {
            $fee = $service['totalFee'] ?? $service['shipFee'] ?? 0;
            $fees[] = $fee;
        }

        $summary['minFee'] = min($fees);
        $summary['maxFee'] = max($fees);
        $summary['avgFee'] = array_sum($fees) / count($fees);

        return $summary;
    }

    /**
     * Group services by carrier
     */
    public function getServicesGroupedByCarrier(): array
    {
        $grouped = [];
        foreach ($this->getServices() as $service) {
            $carrierName = $service['carrierName'] ?? 'Unknown';
            if (!isset($grouped[$carrierName])) {
                $grouped[$carrierName] = [];
            }
            $grouped[$carrierName][] = $service;
        }
        return $grouped;
    }
}
