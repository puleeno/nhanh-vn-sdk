<?php

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Entity cho yêu cầu vận chuyển
 */
class ShippingRequest extends AbstractEntity
{
    protected function validate(): void
    {
        // Validate các trường bắt buộc
        if (empty($this->getAttribute('orderId'))) {
            $this->addError('orderId', 'ID đơn hàng là bắt buộc');
        }

        if (empty($this->getAttribute('carrierId'))) {
            $this->addError('carrierId', 'ID hãng vận chuyển là bắt buộc');
        }

        if (empty($this->getAttribute('carrierServiceId'))) {
            $this->addError('carrierServiceId', 'ID dịch vụ vận chuyển là bắt buộc');
        }

        // Validate trọng lượng
        $weight = $this->getAttribute('weight');
        if ($weight !== null && (!is_numeric($weight) || $weight <= 0)) {
            $this->addError('weight', 'Trọng lượng phải là số dương');
        }

        // Validate kích thước
        $dimensions = ['length', 'width', 'height'];
        foreach ($dimensions as $dimension) {
            $value = $this->getAttribute($dimension);
            if ($value !== null && (!is_numeric($value) || $value <= 0)) {
                $this->addError($dimension, ucfirst($dimension) . ' phải là số dương');
            }
        }
    }

    // Getters cho các trường chính
    public function getOrderId(): string
    {
        return $this->getAttribute('orderId');
    }

    public function getCarrierId(): int
    {
        return (int) $this->getAttribute('carrierId');
    }

    public function getCarrierServiceId(): int
    {
        return (int) $this->getAttribute('carrierServiceId');
    }

    public function getWeight(): ?float
    {
        $weight = $this->getAttribute('weight');
        return $weight !== null ? (float) $weight : null;
    }

    public function getLength(): ?float
    {
        $length = $this->getAttribute('length');
        return $length !== null ? (float) $length : null;
    }

    public function getWidth(): ?float
    {
        $width = $this->getAttribute('width');
        return $width !== null ? (float) $width : null;
    }

    public function getHeight(): ?float
    {
        $height = $this->getAttribute('height');
        return $height !== null ? (float) $height : null;
    }

    public function getCodAmount(): ?float
    {
        $codAmount = $this->getAttribute('codAmount');
        return $codAmount !== null ? (float) $codAmount : null;
    }

    public function getInsuranceAmount(): ?float
    {
        $insuranceAmount = $this->getAttribute('insuranceAmount');
        return $insuranceAmount !== null ? (float) $insuranceAmount : null;
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getCustomerName(): ?string
    {
        return $this->getAttribute('customerName');
    }

    public function getCustomerPhone(): ?string
    {
        return $this->getAttribute('customerPhone');
    }

    public function getCustomerAddress(): ?string
    {
        return $this->getAttribute('customerAddress');
    }

    public function getCustomerCity(): ?string
    {
        return $this->getAttribute('customerCity');
    }

    public function getCustomerDistrict(): ?string
    {
        return $this->getAttribute('customerDistrict');
    }

    public function getCustomerWard(): ?string
    {
        return $this->getAttribute('customerWard');
    }

    // Business logic methods
    public function hasCod(): bool
    {
        return $this->getCodAmount() > 0;
    }

    public function hasInsurance(): bool
    {
        return $this->getInsuranceAmount() > 0;
    }

    public function getTotalVolume(): float
    {
        $length = $this->getLength() ?? 0;
        $width = $this->getWidth() ?? 0;
        $height = $this->getHeight() ?? 0;

        return $length * $width * $height;
    }

    public function isOversized(): bool
    {
        $maxDimension = 150; // cm
        return $this->getLength() > $maxDimension ||
               $this->getWidth() > $maxDimension ||
               $this->getHeight() > $maxDimension;
    }

    public function isOverweight(): bool
    {
        $maxWeight = 50; // kg
        return $this->getWeight() > $maxWeight;
    }

    // Convert to API format
    public function toApiFormat(): array
    {
        return [
            'orderId' => $this->getOrderId(),
            'carrierId' => $this->getCarrierId(),
            'carrierServiceId' => $this->getCarrierServiceId(),
            'weight' => $this->getWeight(),
            'length' => $this->getLength(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'codAmount' => $this->getCodAmount(),
            'insuranceAmount' => $this->getInsuranceAmount(),
            'description' => $this->getDescription(),
            'customerName' => $this->getCustomerName(),
            'customerPhone' => $this->getCustomerPhone(),
            'customerAddress' => $this->getCustomerAddress(),
            'customerCity' => $this->getCustomerCity(),
            'customerDistrict' => $this->getCustomerDistrict(),
            'customerWard' => $this->getCustomerWard()
        ];
    }
}
