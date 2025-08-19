<?php

namespace Puleeno\NhanhVn\Entities\Order;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Entity cho phản hồi cập nhật đơn hàng
 * 
 * @package Puleeno\NhanhVn\Entities\Order
 * @author Puleeno
 * @since 2.0.0
 */
class OrderUpdateResponse extends AbstractEntity
{
    /**
     * Validate response structure
     * 
     * @throws \InvalidArgumentException Khi response không hợp lệ
     */
    protected function validate(): void
    {
        // Validate response structure
        if (!$this->hasAttribute('code')) {
            $this->addError('code', 'Thiếu mã phản hồi');
        }

        if (!$this->hasAttribute('success')) {
            $this->addError('success', 'Thiếu trạng thái thành công');
        }
    }

    // Static factory methods
    public static function createSuccessResponse(array $data): self
    {
        return new self([
            'code' => 1,
            'success' => true,
            'data' => $data
        ]);
    }

    public static function createErrorResponse(array $messages): self
    {
        return new self([
            'code' => 0,
            'success' => false,
            'messages' => $messages
        ]);
    }

    public static function createFromApiResponse(array $apiResponse): self
    {
        if (isset($apiResponse['code']) && $apiResponse['code'] === 1) {
            return self::createSuccessResponse($apiResponse['data'] ?? []);
        } else {
            $messages = $apiResponse['messages'] ?? ['Lỗi không xác định'];
            return self::createErrorResponse($messages);
        }
    }

    // Basic response methods
    public function isSuccess(): bool
    {
        return $this->getAttribute('success') === true;
    }

    public function isError(): bool
    {
        return !$this->isSuccess();
    }

    public function getCode(): int
    {
        return (int) $this->getAttribute('code');
    }

    public function getMessages(): array
    {
        return $this->getAttribute('messages') ?? [];
    }

    public function getAllMessagesAsString(string $separator = '; '): string
    {
        return implode($separator, $this->getMessages());
    }

    public function getFirstMessage(): ?string
    {
        $messages = $this->getMessages();
        return !empty($messages) ? $messages[0] : null;
    }

    // Order update specific methods
    public function getOrderId(): ?int
    {
        if (!$this->isSuccess()) {
            return null;
        }
        
        $data = $this->getAttribute('data');
        $orderId = $data['orderId'] ?? null;
        return $orderId !== null ? (int) $orderId : null;
    }

    public function getStatus(): ?string
    {
        if (!$this->isSuccess()) {
            return null;
        }
        
        $data = $this->getAttribute('data');
        return $data['status'] ?? null;
    }

    public function getShipFee(): ?float
    {
        if (!$this->isSuccess()) {
            return null;
        }
        
        $data = $this->getAttribute('data');
        $fee = $data['shipFee'] ?? null;
        return $fee !== null ? (float) $fee : null;
    }

    public function getCodFee(): ?float
    {
        if (!$this->isSuccess()) {
            return null;
        }
        
        $data = $this->getAttribute('data');
        $fee = $data['codFee'] ?? null;
        return $fee !== null ? (float) $fee : null;
    }

    public function getShipFeeDiscount(): ?float
    {
        if (!$this->isSuccess()) {
            return null;
        }
        
        $data = $this->getAttribute('data');
        $discount = $data['shipFeeDiscount'] ?? null;
        return $discount !== null ? (float) $discount : null;
    }

    public function getCodFeeDiscount(): ?float
    {
        if (!$this->isSuccess()) {
            return null;
        }
        
        $data = $this->getAttribute('data');
        $discount = $data['codFeeDiscount'] ?? null;
        return $discount !== null ? (float) $discount : null;
    }

    public function getCarrierCode(): ?string
    {
        if (!$this->isSuccess()) {
            return null;
        }
        
        $data = $this->getAttribute('data');
        return $data['carrierCode'] ?? null;
    }

    // Business logic methods
    public function hasOrderId(): bool
    {
        return $this->getOrderId() !== null;
    }

    public function hasStatus(): bool
    {
        return $this->getStatus() !== null;
    }

    public function hasShipFee(): bool
    {
        return $this->getShipFee() !== null;
    }

    public function hasCodFee(): bool
    {
        return $this->getCodFee() !== null;
    }

    public function hasCarrierCode(): bool
    {
        return $this->getCarrierCode() !== null;
    }

    public function hasDiscounts(): bool
    {
        return $this->getShipFeeDiscount() > 0 || $this->getCodFeeDiscount() > 0;
    }

    public function getActualShipFee(): ?float
    {
        if (!$this->hasShipFee()) {
            return null;
        }
        
        $shipFee = $this->getShipFee();
        $discount = $this->getShipFeeDiscount() ?? 0;
        
        return max(0, $shipFee - $discount);
    }

    public function getActualCodFee(): ?float
    {
        if (!$this->hasCodFee()) {
            return null;
        }
        
        $codFee = $this->getCodFee();
        $discount = $this->getCodFeeDiscount() ?? 0;
        
        return max(0, $codFee - $discount);
    }

    public function getTotalFees(): ?float
    {
        $actualShipFee = $this->getActualShipFee() ?? 0;
        $actualCodFee = $this->getActualCodFee() ?? 0;
        
        return $actualShipFee + $actualCodFee;
    }

    public function getTotalDiscounts(): float
    {
        $shipDiscount = $this->getShipFeeDiscount() ?? 0;
        $codDiscount = $this->getCodFeeDiscount() ?? 0;
        
        return $shipDiscount + $codDiscount;
    }

    // Summary methods
    public function getSummary(): array
    {
        if (!$this->isSuccess()) {
            return [
                'success' => false,
                'code' => $this->getCode(),
                'messages' => $this->getMessages(),
                'error_count' => count($this->getMessages())
            ];
        }

        return [
            'success' => true,
            'code' => $this->getCode(),
            'order_id' => $this->getOrderId(),
            'status' => $this->getStatus(),
            'ship_fee' => $this->getShipFee(),
            'cod_fee' => $this->getCodFee(),
            'ship_fee_discount' => $this->getShipFeeDiscount(),
            'cod_fee_discount' => $this->getCodFeeDiscount(),
            'actual_ship_fee' => $this->getActualShipFee(),
            'actual_cod_fee' => $this->getActualCodFee(),
            'total_fees' => $this->getTotalFees(),
            'total_discounts' => $this->getTotalDiscounts(),
            'carrier_code' => $this->getCarrierCode(),
            'has_discounts' => $this->hasDiscounts(),
            'has_carrier_code' => $this->hasCarrierCode(),
            'update_type' => $this->getUpdateType()
        ];
    }

    /**
     * Xác định loại cập nhật dựa trên response data
     * 
     * @return string
     */
    private function getUpdateType(): string
    {
        if ($this->hasCarrierCode()) {
            return 'shipping';
        } elseif ($this->hasStatus()) {
            return 'status';
        } elseif ($this->hasShipFee() || $this->hasCodFee()) {
            return 'fees';
        } else {
            return 'general';
        }
    }
}
