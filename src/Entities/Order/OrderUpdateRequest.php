<?php

namespace Puleeno\NhanhVn\Entities\Order;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Entity cho yêu cầu cập nhật đơn hàng
 *
 * Sử dụng để cập nhật thông tin đơn hàng khi:
 * - Khách hàng thực hiện chuyển khoản online
 * - Khách hàng hủy đơn hàng
 * - Gửi đơn hàng qua các hãng vận chuyển
 *
 * @package Puleeno\NhanhVn\Entities\Order
 * @author Puleeno
 * @since 2.0.0
 */
class OrderUpdateRequest extends AbstractEntity
{
    /**
     * Validate dữ liệu đầu vào
     *
     * @throws \InvalidArgumentException Khi dữ liệu không hợp lệ
     */
    protected function validate(): void
    {
        // Kiểm tra ít nhất một trong hai trường id hoặc orderId
        $id = $this->getAttribute('id');
        $orderId = $this->getAttribute('orderId');

        if (empty($id) && empty($orderId)) {
            $this->addError('identification', 'Phải cung cấp ít nhất một trong hai giá trị: id hoặc orderId');
        }

        // Validate autoSend nếu có
        $autoSend = $this->getAttribute('autoSend');
        if ($autoSend !== null && !in_array($autoSend, [0, 1])) {
            $this->addError('autoSend', 'autoSend phải là 0 hoặc 1');
        }

        // Validate moneyTransfer nếu có
        $moneyTransfer = $this->getAttribute('moneyTransfer');
        if ($moneyTransfer !== null && (!is_numeric($moneyTransfer) || $moneyTransfer < 0)) {
            $this->addError('moneyTransfer', 'Số tiền chuyển khoản phải là số không âm');
        }

        // Validate status nếu có
        $status = $this->getAttribute('status');
        if ($status !== null && !in_array($status, ['Success', 'Confirmed', 'Canceled', 'Aborted'])) {
            $this->addError('status', 'Trạng thái không hợp lệ. Chỉ chấp nhận: Success, Confirmed, Canceled, Aborted');
        }

        // Validate customerShipFee nếu có
        $customerShipFee = $this->getAttribute('customerShipFee');
        if ($customerShipFee !== null && (!is_numeric($customerShipFee) || $customerShipFee < 0)) {
            $this->addError('customerShipFee', 'Phí ship báo khách phải là số không âm');
        }
    }

    // Getters cho các trường chính
    public function getId(): ?string
    {
        return $this->getAttribute('id');
    }

    public function getOrderId(): ?string
    {
        return $this->getAttribute('orderId');
    }

    public function getAutoSend(): ?int
    {
        $autoSend = $this->getAttribute('autoSend');
        return $autoSend !== null ? (int) $autoSend : null;
    }

    public function getMoneyTransfer(): ?float
    {
        $moneyTransfer = $this->getAttribute('moneyTransfer');
        return $moneyTransfer !== null ? (float) $moneyTransfer : null;
    }

    public function getMoneyTransferAccountId(): ?int
    {
        $accountId = $this->getAttribute('moneyTransferAccountId');
        return $accountId !== null ? (int) $accountId : null;
    }

    public function getPaymentCode(): ?string
    {
        return $this->getAttribute('paymentCode');
    }

    public function getPaymentGateway(): ?string
    {
        return $this->getAttribute('paymentGateway');
    }

    public function getStatus(): ?string
    {
        return $this->getAttribute('status');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getPrivateDescription(): ?string
    {
        return $this->getAttribute('privateDescription');
    }

    public function getCustomerShipFee(): ?float
    {
        $shipFee = $this->getAttribute('customerShipFee');
        return $shipFee !== null ? (float) $shipFee : null;
    }

    // Business logic methods
    public function hasId(): bool
    {
        return !empty($this->getId());
    }

    public function hasOrderId(): bool
    {
        return !empty($this->getOrderId());
    }

    public function hasValidIdentification(): bool
    {
        return $this->hasId() || $this->hasOrderId();
    }

    public function isAutoSend(): bool
    {
        return $this->getAutoSend() === 1;
    }

    public function hasMoneyTransfer(): bool
    {
        return $this->getMoneyTransfer() > 0;
    }

    public function hasPaymentInfo(): bool
    {
        return !empty($this->getPaymentCode()) || !empty($this->getPaymentGateway());
    }

    public function isStatusUpdate(): bool
    {
        return !empty($this->getStatus());
    }

    public function isPaymentUpdate(): bool
    {
        return $this->hasMoneyTransfer() || $this->hasPaymentInfo();
    }

    public function isShippingUpdate(): bool
    {
        return $this->isAutoSend() || $this->getCustomerShipFee() !== null;
    }

    public function getUpdateType(): string
    {
        if ($this->isStatusUpdate()) {
            return 'status';
        } elseif ($this->isPaymentUpdate()) {
            return 'payment';
        } elseif ($this->isShippingUpdate()) {
            return 'shipping';
        } else {
            return 'general';
        }
    }

    // Convert to API format
    public function toApiFormat(): array
    {
        $data = [];

        // Chỉ thêm các trường có giá trị
        if ($this->hasId()) {
            $data['id'] = $this->getId();
        }

        if ($this->hasOrderId()) {
            $data['orderId'] = $this->getOrderId();
        }

        if ($this->getAutoSend() !== null) {
            $data['autoSend'] = $this->getAutoSend();
        }

        if ($this->getMoneyTransfer() !== null) {
            $data['moneyTransfer'] = $this->getMoneyTransfer();
        }

        if ($this->getMoneyTransferAccountId() !== null) {
            $data['moneyTransferAccountId'] = $this->getMoneyTransferAccountId();
        }

        if ($this->getPaymentCode() !== null) {
            $data['paymentCode'] = $this->getPaymentCode();
        }

        if ($this->getPaymentGateway() !== null) {
            $data['paymentGateway'] = $this->getPaymentGateway();
        }

        if ($this->getStatus() !== null) {
            $data['status'] = $this->getStatus();
        }

        if ($this->getDescription() !== null) {
            $data['description'] = $this->getDescription();
        }

        if ($this->getPrivateDescription() !== null) {
            $data['privateDescription'] = $this->getPrivateDescription();
        }

        if ($this->getCustomerShipFee() !== null) {
            $data['customerShipFee'] = $this->getCustomerShipFee();
        }

        return $data;
    }

    /**
     * Tạo request cập nhật trạng thái đơn hàng
     *
     * @param string $orderId ID đơn hàng Nhanh.vn
     * @param string $status Trạng thái mới
     * @param string|null $description Ghi chú khách hàng
     * @param string|null $privateDescription Ghi chú nội bộ
     * @return self
     */
    public static function createStatusUpdate(string $orderId, string $status, ?string $description = null, ?string $privateDescription = null): self
    {
        return new self([
            'orderId' => $orderId,
            'status' => $status,
            'description' => $description,
            'privateDescription' => $privateDescription
        ]);
    }

    /**
     * Tạo request cập nhật thông tin thanh toán
     *
     * @param string $orderId ID đơn hàng Nhanh.vn
     * @param float $moneyTransfer Số tiền chuyển khoản
     * @param string $paymentCode Mã giao dịch
     * @param string $paymentGateway Tên cổng thanh toán
     * @param int|null $accountId ID tài khoản nhận tiền
     * @return self
     */
    public static function createPaymentUpdate(string $orderId, float $moneyTransfer, string $paymentCode, string $paymentGateway, ?int $accountId = null): self
    {
        return new self([
            'orderId' => $orderId,
            'moneyTransfer' => $moneyTransfer,
            'paymentCode' => $paymentCode,
            'paymentGateway' => $paymentGateway,
            'moneyTransferAccountId' => $accountId
        ]);
    }

    /**
     * Tạo request gửi đơn hàng sang hãng vận chuyển
     *
     * @param string $orderId ID đơn hàng Nhanh.vn
     * @param float|null $customerShipFee Phí ship báo khách
     * @return self
     */
    public static function createShippingUpdate(string $orderId, ?float $customerShipFee = null): self
    {
        return new self([
            'orderId' => $orderId,
            'autoSend' => 1,
            'customerShipFee' => $customerShipFee
        ]);
    }
}
