<?php

namespace Puleeno\NhanhVn\Entities\Order;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Order Add Response Entity
 *
 * Entity này đại diện cho response từ API thêm đơn hàng trong Nhanh.vn
 */
class OrderAddResponse extends AbstractEntity
{
    protected array $fillable = [
        'code', 'message', 'data', 'orderId', 'orderCode', 'status', 'createdAt'
    ];

    protected array $rules = [
        'code' => 'required|integer',
        'message' => 'required|string',
        'data' => 'nullable|array',
        'orderId' => 'nullable|integer',
        'orderCode' => 'nullable|string',
        'status' => 'nullable|string',
        'createdAt' => 'nullable|string'
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
        }

        return true;
    }

    // Getters
    public function getCode(): int { return $this->get('code'); }
    public function getMessage(): string { return $this->get('message'); }
    public function getData(): ?array { return $this->get('data'); }
    public function getOrderId(): ?int { return $this->get('orderId'); }
    public function getOrderCode(): ?string { return $this->get('orderCode'); }
    public function getStatus(): ?string { return $this->get('status'); }
    public function getCreatedAt(): ?string { return $this->get('createdAt'); }

    /**
     * Kiểm tra API call có thành công không
     */
    public function isSuccess(): bool
    {
        return $this->getCode() === 1;
    }

    /**
     * Kiểm tra có lỗi không
     */
    public function hasError(): bool
    {
        return $this->getCode() !== 1;
    }

    /**
     * Lấy thông tin đơn hàng từ data
     */
    public function getOrderInfo(): ?array
    {
        $data = $this->getData();
        if (empty($data) || !is_array($data)) {
            return null;
        }

        return $data;
    }

    /**
     * Lấy ID đơn hàng từ response
     */
    public function getNhanhOrderId(): ?int
    {
        // Thử lấy từ orderId trước
        if ($this->has('orderId')) {
            return $this->getOrderId();
        }

        // Thử lấy từ data nếu có
        $data = $this->getData();
        if (isset($data['id'])) {
            return (int)$data['id'];
        }

        return null;
    }

    /**
     * Lấy mã đơn hàng từ response
     */
    public function getNhanhOrderCode(): ?string
    {
        // Thử lấy từ orderCode trước
        if ($this->has('orderCode')) {
            return $this->getOrderCode();
        }

        // Thử lấy từ data nếu có
        $data = $this->getData();
        if (isset($data['code'])) {
            return $data['code'];
        }

        return null;
    }
}
