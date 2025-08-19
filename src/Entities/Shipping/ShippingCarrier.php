<?php

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Shipping Carrier Entity
 *
 * Entity này đại diện cho hãng vận chuyển trong Nhanh.vn API
 * Chứa thông tin về hãng vận chuyển và các dịch vụ của họ
 */
class ShippingCarrier extends AbstractEntity
{
    protected array $fillable = [
        'id', 'name', 'logo', 'services'
    ];

    protected array $rules = [
        'id' => 'required|integer',
        'name' => 'required|string|max:255',
        'logo' => 'nullable|string',
        'services' => 'nullable|array'
    ];

    /**
     * Validate entity theo rules
     */
    protected function validate(): void
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rule) {
            if (!$this->has($field)) {
                continue;
            }

            $value = $this->get($field);
            $this->validateField($field, $value, $rule);
        }

        // Validate services nếu có
        if ($this->has('services') && is_array($this->get('services'))) {
            $this->validateServices($this->get('services'));
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

            case 'max':
                if (is_string($value) && strlen($value) > (int)$ruleValue) {
                    $this->addError($field, "Trường {$field} không được vượt quá {$ruleValue} ký tự");
                    return false;
                }
                break;
        }

        return true;
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

            if (empty($service['id'])) {
                $this->addError("services.{$index}.id", "ID dịch vụ là bắt buộc");
            }

            if (empty($service['name'])) {
                $this->addError("services.{$index}.name", "Tên dịch vụ là bắt buộc");
            }

            if (isset($service['id']) && (!is_numeric($service['id']) || (int)$service['id'] != $service['id'])) {
                $this->addError("services.{$index}.id", "ID dịch vụ phải là số nguyên");
            }

            if (isset($service['name']) && !is_string($service['name'])) {
                $this->addError("services.{$index}.name", "Tên dịch vụ phải là chuỗi");
            }
        }
    }

    // Getters
    public function getId(): int
    {
        return $this->get('id');
    }
    public function getName(): string
    {
        return $this->get('name');
    }
    public function getLogo(): ?string
    {
        return $this->get('logo');
    }
    public function getServices(): ?array
    {
        return $this->get('services');
    }

    /**
     * Lấy danh sách tên dịch vụ
     */
    public function getServiceNames(): array
    {
        $services = $this->getServices();
        if (empty($services) || !is_array($services)) {
            return [];
        }

        $names = [];
        foreach ($services as $service) {
            if (isset($service['name'])) {
                $names[] = $service['name'];
            }
        }

        return $names;
    }

    /**
     * Lấy danh sách ID dịch vụ
     */
    public function getServiceIds(): array
    {
        $services = $this->getServices();
        if (empty($services) || !is_array($services)) {
            return [];
        }

        $ids = [];
        foreach ($services as $service) {
            if (isset($service['id'])) {
                $ids[] = (int)$service['id'];
            }
        }

        return $ids;
    }

    /**
     * Kiểm tra có logo không
     */
    public function hasLogo(): bool
    {
        return !empty($this->getLogo());
    }

    /**
     * Kiểm tra có dịch vụ nào không
     */
    public function hasServices(): bool
    {
        $services = $this->getServices();
        return !empty($services) && is_array($services);
    }

    /**
     * Lấy số lượng dịch vụ
     */
    public function getServiceCount(): int
    {
        $services = $this->getServices();
        return empty($services) ? 0 : count($services);
    }

    /**
     * Tìm dịch vụ theo ID
     */
    public function findServiceById(int $serviceId): ?array
    {
        $services = $this->getServices();
        if (empty($services) || !is_array($services)) {
            return null;
        }

        foreach ($services as $service) {
            if (isset($service['id']) && (int)$service['id'] === $serviceId) {
                return $service;
            }
        }

        return null;
    }

    /**
     * Tìm dịch vụ theo tên
     */
    public function findServiceByName(string $serviceName): ?array
    {
        $services = $this->getServices();
        if (empty($services) || !is_array($services)) {
            return null;
        }

        foreach ($services as $service) {
            if (isset($service['name']) && $service['name'] === $serviceName) {
                return $service;
            }
        }

        return null;
    }
}
