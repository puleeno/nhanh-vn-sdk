<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product IMEI History entity
 */
class ProductImeiHistory extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getStep()) {
            $this->addError('step', 'Step không được để trống');
        }

        if (!$this->getImei()) {
            $this->addError('imei', 'IMEI không được để trống');
        }
    }

    // Basic getters
    public function getStep(): ?int
    {
        return $this->getAttribute('step');
    }

    public function getStepName(): ?string
    {
        return $this->getAttribute('stepName');
    }

    public function getItemType(): ?string
    {
        return $this->getAttribute('itemType');
    }

    public function getItemId(): ?int
    {
        return $this->getAttribute('itemId');
    }

    public function getProductName(): ?string
    {
        return $this->getAttribute('productName');
    }

    public function getProductCode(): ?string
    {
        return $this->getAttribute('productCode');
    }

    public function getProductBarcode(): ?string
    {
        return $this->getAttribute('productBarcode');
    }

    public function getSupplier(): ?string
    {
        return $this->getAttribute('supplier');
    }

    public function getSale(): ?string
    {
        return $this->getAttribute('sale');
    }

    public function getCreatedBy(): ?string
    {
        return $this->getAttribute('createdBy');
    }

    public function getCreatedDateTime(): ?string
    {
        return $this->getAttribute('createdDateTime');
    }

    public function getImei(): ?string
    {
        return $this->getAttribute('IMEI');
    }

    // Business logic methods
    public function isNew(): bool
    {
        return $this->getStep() === 1;
    }

    public function isSold(): bool
    {
        return $this->getStep() === 2;
    }

    public function isActivated(): bool
    {
        return $this->getStep() === 3;
    }

    public function isUnderWarranty(): bool
    {
        return $this->getStep() === 4;
    }

    public function isWarrantyReturned(): bool
    {
        return $this->getStep() === 5;
    }

    public function isWarrantyCreated(): bool
    {
        return $this->getStep() === 6;
    }

    public function isActivatedConfirmed(): bool
    {
        return $this->getStep() === 7;
    }

    public function isActivationCancelled(): bool
    {
        return $this->getStep() === 8;
    }

    public function isReturned(): bool
    {
        return $this->getStep() === 9;
    }

    public function isSupplierReturned(): bool
    {
        return $this->getStep() === 10;
    }

    public function isTransferring(): bool
    {
        return $this->getStep() === 12;
    }

    public function isInternalSale(): bool
    {
        return $this->getStep() === 13;
    }

    public function isUsedPurchase(): bool
    {
        return $this->getStep() === 14;
    }

    public function isExchanged(): bool
    {
        return $this->getStep() === 15;
    }

    public function isTransferringIn(): bool
    {
        return $this->getStep() === 17;
    }

    public function isOtherImport(): bool
    {
        return $this->getStep() === 18;
    }

    public function isInformationUpdated(): bool
    {
        return $this->getStep() === 19;
    }

    public function isStockAdjustment(): bool
    {
        return $this->getStep() === 20;
    }

    public function isStockAdjustmentOut(): bool
    {
        return $this->getStep() === 21;
    }

    public function isOrderAdded(): bool
    {
        return $this->getStep() === 22;
    }

    public function isOrderUpdated(): bool
    {
        return $this->getStep() === 23;
    }

    public function isOrderConfirmed(): bool
    {
        return $this->getStep() === 24;
    }

    public function isPicked(): bool
    {
        return $this->getStep() === 25;
    }

    public function isOrderStatusUpdated(): bool
    {
        return $this->getStep() === 26;
    }

    public function isOrderItemRemoved(): bool
    {
        return $this->getStep() === 27;
    }

    public function isOrderRemoved(): bool
    {
        return $this->getStep() === 28;
    }

    public function isImeiChangedInInvoice(): bool
    {
        return $this->getStep() === 29;
    }

    public function isImeiChangedInOrder(): bool
    {
        return $this->getStep() === 30;
    }

    public function isWarrantyCenterTransfer(): bool
    {
        return $this->getStep() === 31;
    }

    public function isWarrantyCenterReceived(): bool
    {
        return $this->getStep() === 32;
    }

    public function isTransferRequest(): bool
    {
        return $this->getStep() === 33;
    }

    public function isTransferRequestCancelled(): bool
    {
        return $this->getStep() === 34;
    }

    public function isStatusChanged(): bool
    {
        return $this->getStep() === 35;
    }

    public function isWarrantyUpdated(): bool
    {
        return $this->getStep() === 36;
    }

    public function isWarrantyNotReturned(): bool
    {
        return $this->getStep() === 37;
    }

    public function isTransferRequestApproved(): bool
    {
        return $this->getStep() === 38;
    }

    public function isNoteUpdated(): bool
    {
        return $this->getStep() === 39;
    }

    public function getStepDescription(): string
    {
        $step = $this->getStep();

        $descriptions = [
            1 => 'Mới',
            2 => 'Bán hàng',
            3 => 'Kích hoạt',
            4 => 'Bảo hành',
            5 => 'Trả bảo hành',
            6 => 'NVBH lập phiếu',
            7 => 'Đã kích hoạt',
            8 => 'Hủy kích hoạt',
            9 => 'Khách trả lại hàng',
            10 => 'Nhập nhà cung cấp',
            11 => 'Trả nhà cung cấp',
            12 => 'Xuất chuyển kho',
            13 => 'Bán hàng nội bộ',
            14 => 'Mua máy cũ',
            15 => 'Đổi sản phẩm',
            17 => 'Nhập chuyển kho',
            18 => 'XNK khác',
            19 => 'Sửa thông tin',
            20 => 'Nhập bù trừ kiểm kho',
            21 => 'Xuất bù trừ kiểm kho',
            22 => 'Thêm đơn hàng',
            23 => 'Sửa đơn hàng',
            24 => 'Xác nhận đơn hàng',
            25 => 'Nhặt hàng',
            26 => 'Cập nhật trạng thái đơn hàng',
            27 => 'Xóa sản phẩm trong đơn hàng',
            28 => 'Xóa đơn hàng',
            29 => 'Đổi IMEI trong hóa đơn',
            30 => 'Đổi IMEI trong đơn hàng',
            31 => 'Chuyển trung tâm bảo hành',
            32 => 'Nhận từ trung tâm bảo hành',
            33 => 'Yêu cầu chuyển kho',
            34 => 'Hủy yêu cầu chuyển kho',
            35 => 'Đổi trạng thái',
            36 => 'Sửa bảo hành',
            37 => 'Chưa trả bảo hành',
            38 => 'Duyệt yêu cầu chuyển kho',
            39 => 'Sửa ghi chú'
        ];

        return $descriptions[$step] ?? 'Không xác định';
    }

    public function getStepCategory(): string
    {
        $step = $this->getStep();

        if (in_array($step, [1, 10, 11, 17, 18, 20])) {
            return 'Import';
        }

        if (in_array($step, [2, 12, 13, 21])) {
            return 'Export';
        }

        if (in_array($step, [3, 7, 8])) {
            return 'Activation';
        }

        if (in_array($step, [4, 5, 6, 9, 31, 32, 36, 37])) {
            return 'Warranty';
        }

        if (in_array($step, [14, 15])) {
            return 'Exchange';
        }

        if (in_array($step, [22, 23, 24, 25, 26, 27, 28, 29, 30])) {
            return 'Order';
        }

        if (in_array($step, [33, 34, 38])) {
            return 'Transfer';
        }

        if (in_array($step, [19, 35, 39])) {
            return 'Update';
        }

        return 'Other';
    }

    public function getStepColor(): string
    {
        $category = $this->getStepCategory();

        switch ($category) {
            case 'Import':
                return '#28a745'; // Green
            case 'Export':
                return '#dc3545'; // Red
            case 'Activation':
                return '#007bff'; // Blue
            case 'Warranty':
                return '#ffc107'; // Yellow
            case 'Exchange':
                return '#6f42c1'; // Purple
            case 'Order':
                return '#17a2b8'; // Cyan
            case 'Transfer':
                return '#fd7e14'; // Orange
            case 'Update':
                return '#6c757d'; // Gray
            default:
                return '#6c757d'; // Gray
        }
    }

    public function getFormattedCreatedDateTime(): string
    {
        $date = $this->getCreatedDateTime();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i:s', strtotime($date));
    }

    public function getFormattedImei(): string
    {
        $imei = $this->getImei();
        return $imei ?: 'N/A';
    }

    public function hasProduct(): bool
    {
        return !empty($this->getProductName());
    }

    public function hasSupplier(): bool
    {
        return !empty($this->getSupplier());
    }

    public function hasSale(): bool
    {
        return !empty($this->getSale());
    }

    public function hasCreatedBy(): bool
    {
        return !empty($this->getCreatedBy());
    }

    public function getHistorySummary(): string
    {
        $summary = [];

        $summary[] = $this->getStepDescription();

        if ($this->hasProduct()) {
            $summary[] = $this->getProductName();
        }

        if ($this->getImei()) {
            $summary[] = "IMEI: {$this->getImei()}";
        }

        if ($this->hasSupplier()) {
            $summary[] = "NCC: {$this->getSupplier()}";
        }

        if ($this->hasSale()) {
            $summary[] = "NV: {$this->getSale()}";
        }

        if ($this->hasCreatedBy()) {
            $summary[] = "Tạo bởi: {$this->getCreatedBy()}";
        }

        $summary[] = $this->getFormattedCreatedDateTime();

        return implode(' - ', array_filter($summary));
    }

    /**
     * Tạo product IMEI history từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều product IMEI history từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $histories = [];

        foreach ($data as $historyData) {
            $histories[] = self::createFromArray($historyData);
        }

        return $histories;
    }

    /**
     * Lọc histories theo step
     */
    public static function filterByStep(array $histories, int $step): array
    {
        return array_filter($histories, function (ProductImeiHistory $history) use ($step) {
            return $history->getStep() === $step;
        });
    }

    /**
     * Lọc histories theo category
     */
    public static function filterByCategory(array $histories, string $category): array
    {
        return array_filter($histories, function (ProductImeiHistory $history) use ($category) {
            return $history->getStepCategory() === $category;
        });
    }

    /**
     * Lọc histories theo IMEI
     */
    public static function filterByImei(array $histories, string $imei): array
    {
        return array_filter($histories, function (ProductImeiHistory $history) use ($imei) {
            return $history->getImei() === $imei;
        });
    }

    /**
     * Sắp xếp histories theo thời gian tạo
     */
    public static function sortByCreatedDateTime(array $histories, bool $ascending = true): array
    {
        usort($histories, function (ProductImeiHistory $a, ProductImeiHistory $b) use ($ascending) {
            $dateA = strtotime($a->getCreatedDateTime() ?? '');
            $dateB = strtotime($b->getCreatedDateTime() ?? '');

            if ($dateA === $dateB) {
                return 0;
            }

            if ($ascending) {
                return $dateA <=> $dateB;
            }

            return $dateB <=> $dateA;
        });

        return $histories;
    }

    /**
     * Sắp xếp histories theo step
     */
    public static function sortByStep(array $histories, bool $ascending = true): array
    {
        usort($histories, function (ProductImeiHistory $a, ProductImeiHistory $b) use ($ascending) {
            $stepA = $a->getStep() ?? 0;
            $stepB = $b->getStep() ?? 0;

            if ($stepA === $stepB) {
                // Nếu cùng step, sắp xếp theo thời gian
                $dateA = strtotime($a->getCreatedDateTime() ?? '');
                $dateB = strtotime($b->getCreatedDateTime() ?? '');

                return $dateA <=> $dateB;
            }

            if ($ascending) {
                return $stepA <=> $stepB;
            }

            return $stepB <=> $stepA;
        });

        return $histories;
    }
}
