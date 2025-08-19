<?php

namespace Puleeno\NhanhVn\Entities\Order;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Order Add Request Entity
 * 
 * Entity này đại diện cho request thêm đơn hàng mới trong Nhanh.vn API
 * Hỗ trợ đầy đủ các tham số theo tài liệu API /api/order/add
 */
class OrderAddRequest extends AbstractEntity
{
    protected array $fillable = [
        'id', 'depotId', 'type', 'customerName', 'customerMobile', 'customerEmail',
        'customerAddress', 'customerCityName', 'customerDistrictName', 'customerWardLocationName',
        'moneyDiscount', 'moneyTransfer', 'moneyTransferAccountId', 'moneyDeposit',
        'moneyDepositAccountId', 'paymentMethod', 'paymentCode', 'paymentGateway',
        'carrierId', 'carrierServiceId', 'customerShipFee', 'deliveryDate', 'status',
        'description', 'privateDescription', 'trafficSource', 'productList', 'couponCode',
        'allowTest', 'saleId', 'autoSend', 'sendCarrierType', 'carrierAccountId',
        'carrierShopId', 'carrierServiceCode', 'utmCampaign', 'utmSource', 'utmMedium',
        'affiliate', 'usedPoints', 'isPartDelivery'
    ];

    protected array $rules = [
        'id' => 'required|string|max:36',
        'depotId' => 'nullable|integer',
        'type' => 'nullable|in:Shipping,Shopping,PreOrder',
        'customerName' => 'required|string|max:255',
        'customerMobile' => 'required|string|max:255',
        'customerEmail' => 'nullable|string|max:255|email',
        'customerAddress' => 'nullable|string|max:255',
        'customerCityName' => 'nullable|string|max:255',
        'customerDistrictName' => 'nullable|string|max:255',
        'customerWardLocationName' => 'nullable|string',
        'moneyDiscount' => 'nullable|numeric|min:0',
        'moneyTransfer' => 'nullable|numeric|min:0',
        'moneyTransferAccountId' => 'nullable|integer',
        'moneyDeposit' => 'nullable|numeric|min:0',
        'moneyDepositAccountId' => 'nullable|integer',
        'paymentMethod' => 'nullable|in:COD,Store,Gateway,Online',
        'paymentCode' => 'nullable|string|max:255',
        'paymentGateway' => 'nullable|string|max:255',
        'carrierId' => 'nullable|integer',
        'carrierServiceId' => 'nullable|integer',
        'customerShipFee' => 'nullable|integer|min:0',
        'deliveryDate' => 'nullable|date_format:Y-m-d',
        'status' => 'nullable|in:New,Confirming,Confirmed',
        'description' => 'nullable|string|max:255',
        'privateDescription' => 'nullable|string|max:255',
        'trafficSource' => 'nullable|string',
        'productList' => 'nullable|array',
        'couponCode' => 'nullable|string',
        'allowTest' => 'nullable|in:1,2,3,4',
        'saleId' => 'nullable|integer',
        'autoSend' => 'nullable|in:0,1',
        'sendCarrierType' => 'nullable|in:1,2',
        'carrierAccountId' => 'nullable|integer',
        'carrierShopId' => 'nullable|integer',
        'carrierServiceCode' => 'nullable|string',
        'utmCampaign' => 'nullable|string',
        'utmSource' => 'nullable|string',
        'utmMedium' => 'nullable|string',
        'affiliate' => 'nullable|array',
        'usedPoints' => 'nullable|integer|min:0',
        'isPartDelivery' => 'nullable|in:0,1'
    ];

    /**
     * Validate entity theo rules
     */
    protected function validate(): void
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rule) {
            if (!$this->has($field)) {
                continue; // Skip validation for missing fields
            }

            $value = $this->get($field);
            $this->validateField($field, $value, $rule);
        }

        // Validate productList nếu có
        if ($this->has('productList') && is_array($this->get('productList'))) {
            $this->validateProductList($this->get('productList'));
        }

        // Validate affiliate nếu có
        if ($this->has('affiliate') && is_array($this->get('affiliate'))) {
            $this->validateAffiliate($this->get('affiliate'));
        }

        // Validate sendCarrierType dependencies
        if ($this->get('sendCarrierType') == 2) {
            if (!$this->get('carrierAccountId')) {
                $this->addError('carrierAccountId', 'carrierAccountId là bắt buộc khi sendCarrierType = 2');
            }
            if (!$this->get('carrierServiceCode')) {
                $this->addError('carrierServiceCode', 'carrierServiceCode là bắt buộc khi sendCarrierType = 2');
            }
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

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, "Trường {$field} phải là số");
                    return false;
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Trường {$field} phải là email hợp lệ");
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

            case 'min':
                if (is_numeric($value) && $value < (float)$ruleValue) {
                    $this->addError($field, "Trường {$field} phải lớn hơn hoặc bằng {$ruleValue}");
                    return false;
                }
                break;

            case 'in':
                $allowedValues = explode(',', $ruleValue);
                if (!in_array($value, $allowedValues)) {
                    $this->addError($field, "Trường {$field} phải là một trong các giá trị: " . implode(', ', $allowedValues));
                    return false;
                }
                break;

            case 'date_format':
                $date = \DateTime::createFromFormat($ruleValue, $value);
                if (!$date || $date->format($ruleValue) !== $value) {
                    $this->addError($field, "Trường {$field} phải có định dạng {$ruleValue}");
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Validate productList
     */
    private function validateProductList(array $productList): void
    {
        foreach ($productList as $index => $product) {
            if (!is_array($product)) {
                $this->addError("productList.{$index}", "Sản phẩm phải là mảng");
                continue;
            }

            // Validate required fields
            if (empty($product['id'])) {
                $this->addError("productList.{$index}.id", "ID sản phẩm là bắt buộc");
            }

            if (empty($product['quantity'])) {
                $this->addError("productList.{$index}.quantity", "Số lượng sản phẩm là bắt buộc");
            }

            if (empty($product['name'])) {
                $this->addError("productList.{$index}.name", "Tên sản phẩm là bắt buộc");
            }

            if (empty($product['price'])) {
                $this->addError("productList.{$index}.price", "Giá sản phẩm là bắt buộc");
            }

            // Validate quantity
            if (isset($product['quantity']) && (!is_numeric($product['quantity']) || $product['quantity'] <= 0)) {
                $this->addError("productList.{$index}.quantity", "Số lượng phải là số dương");
            }

            // Validate price
            if (isset($product['price']) && (!is_numeric($product['price']) || $product['price'] < 0)) {
                $this->addError("productList.{$index}.price", "Giá sản phẩm phải là số không âm");
            }

            // Validate gifts nếu có
            if (isset($product['gifts']) && is_array($product['gifts'])) {
                $this->validateGifts($product['gifts'], $index);
            }
        }
    }

    /**
     * Validate gifts
     */
    private function validateGifts(array $gifts, int $productIndex): void
    {
        foreach ($gifts as $giftIndex => $gift) {
            if (!is_array($gift)) {
                $this->addError("productList.{$productIndex}.gifts.{$giftIndex}", "Quà tặng phải là mảng");
                continue;
            }

            if (empty($gift['id'])) {
                $this->addError("productList.{$productIndex}.gifts.{$giftIndex}.id", "ID quà tặng là bắt buộc");
            }

            if (empty($gift['quantity']) || !is_numeric($gift['quantity']) || $gift['quantity'] <= 0) {
                $this->addError("productList.{$productIndex}.gifts.{$giftIndex}.quantity", "Số lượng quà tặng phải là số dương");
            }

            if (isset($gift['value']) && (!is_numeric($gift['value']) || $gift['value'] < 0)) {
                $this->addError("productList.{$productIndex}.gifts.{$giftIndex}.value", "Giá quà tặng phải là số không âm");
            }
        }
    }

    /**
     * Validate affiliate
     */
    private function validateAffiliate(array $affiliate): void
    {
        if (isset($affiliate['code']) && !is_string($affiliate['code'])) {
            $this->addError('affiliate.code', 'Mã affiliate phải là chuỗi');
        }

        if (isset($affiliate['discount']) && (!is_numeric($affiliate['discount']) || $affiliate['discount'] < 0)) {
            $this->addError('affiliate.discount', 'Tiền chiết khấu affiliate phải là số không âm');
        }

        if (isset($affiliate['bonus']) && (!is_numeric($affiliate['bonus']) || $affiliate['bonus'] < 0)) {
            $this->addError('affiliate.bonus', 'Tiền hoa hồng affiliate phải là số không âm');
        }
    }

    // Getters cho các field chính
    public function getId(): string { return $this->get('id'); }
    public function getDepotId(): ?int { return $this->get('depotId'); }
    public function getType(): string { return $this->get('type') ?: 'Shipping'; }
    public function getCustomerName(): string { return $this->get('customerName'); }
    public function getCustomerMobile(): string { return $this->get('customerMobile'); }
    public function getCustomerEmail(): ?string { return $this->get('customerEmail'); }
    public function getCustomerAddress(): ?string { return $this->get('customerAddress'); }
    public function getCustomerCityName(): ?string { return $this->get('customerCityName'); }
    public function getCustomerDistrictName(): ?string { return $this->get('customerDistrictName'); }
    public function getCustomerWardLocationName(): ?string { return $this->get('customerWardLocationName'); }
    public function getMoneyDiscount(): ?float { return $this->get('moneyDiscount'); }
    public function getMoneyTransfer(): ?float { return $this->get('moneyTransfer'); }
    public function getMoneyTransferAccountId(): ?int { return $this->get('moneyTransferAccountId'); }
    public function getMoneyDeposit(): ?float { return $this->get('moneyDeposit'); }
    public function getMoneyDepositAccountId(): ?int { return $this->get('moneyDepositAccountId'); }
    public function getPaymentMethod(): ?string { return $this->get('paymentMethod'); }
    public function getPaymentCode(): ?string { return $this->get('paymentCode'); }
    public function getPaymentGateway(): ?string { return $this->get('paymentGateway'); }
    public function getCarrierId(): ?int { return $this->get('carrierId'); }
    public function getCarrierServiceId(): ?int { return $this->get('carrierServiceId'); }
    public function getCustomerShipFee(): ?int { return $this->get('customerShipFee'); }
    public function getDeliveryDate(): ?string { return $this->get('deliveryDate'); }
    public function getStatus(): string { return $this->get('status') ?: 'New'; }
    public function getDescription(): ?string { return $this->get('description'); }
    public function getPrivateDescription(): ?string { return $this->get('privateDescription'); }
    public function getTrafficSource(): ?string { return $this->get('trafficSource'); }
    public function getProductList(): ?array { return $this->get('productList'); }
    public function getCouponCode(): ?string { return $this->get('couponCode'); }
    public function getAllowTest(): ?int { return $this->get('allowTest'); }
    public function getSaleId(): ?int { return $this->get('saleId'); }
    public function getAutoSend(): ?int { return $this->get('autoSend'); }
    public function getSendCarrierType(): ?int { return $this->get('sendCarrierType'); }
    public function getCarrierAccountId(): ?int { return $this->get('carrierAccountId'); }
    public function getCarrierShopId(): ?int { return $this->get('carrierShopId'); }
    public function getCarrierServiceCode(): ?string { return $this->get('carrierServiceCode'); }
    public function getUtmCampaign(): ?string { return $this->get('utmCampaign'); }
    public function getUtmSource(): ?string { return $this->get('utmSource'); }
    public function getUtmMedium(): ?string { return $this->get('utmMedium'); }
    public function getAffiliate(): ?array { return $this->get('affiliate'); }
    public function getUsedPoints(): ?int { return $this->get('usedPoints'); }
    public function getIsPartDelivery(): ?int { return $this->get('isPartDelivery'); }

    /**
     * Kiểm tra có sử dụng vận chuyển không
     */
    public function hasShipping(): bool
    {
        return $this->getType() === 'Shipping';
    }

    /**
     * Kiểm tra có sử dụng bảng giá vận chuyển của Nhanh.vn không
     */
    public function usesNhanhCarrierPricing(): bool
    {
        return $this->getSendCarrierType() === 1;
    }

    /**
     * Kiểm tra có sử dụng bảng giá vận chuyển riêng không
     */
    public function usesSelfConnectCarrierPricing(): bool
    {
        return $this->getSendCarrierType() === 2;
    }

    /**
     * Kiểm tra có gửi tự động sang hãng vận chuyển không
     */
    public function isAutoSend(): bool
    {
        return $this->getAutoSend() === 1;
    }

    /**
     * Kiểm tra có sản phẩm không
     */
    public function hasProducts(): bool
    {
        $productList = $this->getProductList();
        return !empty($productList) && is_array($productList);
    }

    /**
     * Lấy tổng số lượng sản phẩm
     */
    public function getTotalProductQuantity(): int
    {
        $productList = $this->getProductList();
        if (empty($productList)) {
            return 0;
        }

        $total = 0;
        foreach ($productList as $product) {
            if (isset($product['quantity'])) {
                $total += (int)$product['quantity'];
            }
        }

        return $total;
    }

    /**
     * Lấy tổng giá trị sản phẩm
     */
    public function getTotalProductValue(): float
    {
        $productList = $this->getProductList();
        if (empty($productList)) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($productList as $product) {
            if (isset($product['price']) && isset($product['quantity'])) {
                $total += (float)$product['price'] * (int)$product['quantity'];
            }
        }

        return $total;
    }

    /**
     * Chuyển đổi thành format API
     */
    public function toApiFormat(): array
    {
        $data = [];
        
        // Chỉ gửi các field có giá trị
        foreach ($this->fillable as $field) {
            if ($this->has($field)) {
                $data[$field] = $this->get($field);
            }
        }

        return $data;
    }

    /**
     * Validate dữ liệu thêm đơn hàng
     */
    public function validateForAdd(): bool
    {
        // Kiểm tra các field bắt buộc
        if (!$this->getId() || !$this->getCustomerName() || !$this->getCustomerMobile()) {
            return false;
        }

        // Kiểm tra productList nếu có
        if ($this->hasProducts()) {
            $productList = $this->getProductList();
            if (empty($productList)) {
                return false;
            }
        }

        return $this->isValid();
    }

    /**
     * Lấy danh sách các field đã được set
     */
    public function getSetFields(): array
    {
        $setFields = [];
        foreach ($this->fillable as $field) {
            if ($this->has($field)) {
                $setFields[] = $field;
            }
        }
        return $setFields;
    }
}
