<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Add Response Entity
 * 
 * Entity này đại diện cho response khi thêm/sửa sản phẩm
 * theo API specification của Nhanh.vn
 * 
 * @package Puleeno\NhanhVn\Entities\Product
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @since 2.0.0
 */
class ProductAddResponse extends AbstractEntity
{
    /**
     * Validate product add response data
     */
    protected function validate(): void
    {
        // Response phải có ít nhất một trong hai field: ids hoặc barcodes
        if (empty($this->getIds()) && empty($this->getBarcodes())) {
            $this->addError('response', 'Response phải có ít nhất một trong hai field: ids hoặc barcodes');
        }
    }

    /**
     * Get product IDs mapping
     * 
     * @return array Mapping từ ID hệ thống riêng sang ID Nhanh.vn
     */
    public function getIds(): array
    {
        return $this->getAttribute('ids', []);
    }

    /**
     * Get product barcodes mapping
     * 
     * @return array Mapping từ ID hệ thống riêng sang barcode Nhanh.vn
     */
    public function getBarcodes(): array
    {
        return $this->getAttribute('barcodes', []);
    }

    /**
     * Get Nhanh.vn ID by system ID
     * 
     * @param string $systemId ID sản phẩm trên hệ thống riêng
     * @return int|null ID sản phẩm trên Nhanh.vn
     */
    public function getNhanhId(string $systemId): ?int
    {
        $ids = $this->getIds();
        return $ids[$systemId] ?? null;
    }

    /**
     * Get barcode by system ID
     * 
     * @param string $systemId ID sản phẩm trên hệ thống riêng
     * @return string|null Barcode sản phẩm trên Nhanh.vn
     */
    public function getBarcode(string $systemId): ?string
    {
        $barcodes = $this->getBarcodes();
        return $barcodes[$systemId] ?? null;
    }

    /**
     * Get all system IDs
     * 
     * @return array Danh sách tất cả ID hệ thống riêng
     */
    public function getSystemIds(): array
    {
        return array_keys($this->getIds());
    }

    /**
     * Get all Nhanh.vn IDs
     * 
     * @return array Danh sách tất cả ID Nhanh.vn
     */
    public function getNhanhIds(): array
    {
        return array_values($this->getIds());
    }

    /**
     * Get all barcodes
     * 
     * @return array Danh sách tất cả barcode
     */
    public function getAllBarcodes(): array
    {
        return array_values($this->getBarcodes());
    }

    /**
     * Check if system ID exists in response
     * 
     * @param string $systemId ID sản phẩm trên hệ thống riêng
     * @return bool True nếu ID tồn tại
     */
    public function hasSystemId(string $systemId): bool
    {
        return isset($this->getIds()[$systemId]);
    }

    /**
     * Check if Nhanh.vn ID exists in response
     * 
     * @param int $nhanhId ID sản phẩm trên Nhanh.vn
     * @return bool True nếu ID tồn tại
     */
    public function hasNhanhId(int $nhanhId): bool
    {
        return in_array($nhanhId, $this->getNhanhIds());
    }

    /**
     * Get system ID by Nhanh.vn ID
     * 
     * @param int $nhanhId ID sản phẩm trên Nhanh.vn
     * @return string|null ID sản phẩm trên hệ thống riêng
     */
    public function getSystemIdByNhanhId(int $nhanhId): ?string
    {
        $ids = $this->getIds();
        
        foreach ($ids as $systemId => $nhanhIdValue) {
            if ($nhanhIdValue == $nhanhId) {
                return $systemId;
            }
        }

        return null;
    }

    /**
     * Get total products processed
     * 
     * @return int Tổng số sản phẩm đã xử lý
     */
    public function getTotalProducts(): int
    {
        return count($this->getIds());
    }

    /**
     * Get successful products count
     * 
     * @return int Số sản phẩm xử lý thành công
     */
    public function getSuccessCount(): int
    {
        return count(array_filter($this->getIds()));
    }

    /**
     * Get failed products count
     * 
     * @return int Số sản phẩm xử lý thất bại
     */
    public function getFailedCount(): int
    {
        return count(array_filter($this->getIds(), function($value) {
            return empty($value);
        }));
    }

    /**
     * Check if all products were processed successfully
     * 
     * @return bool True nếu tất cả sản phẩm đều thành công
     */
    public function isAllSuccess(): bool
    {
        return $this->getFailedCount() === 0;
    }

    /**
     * Check if any product failed
     * 
     * @return bool True nếu có ít nhất một sản phẩm thất bại
     */
    public function hasFailures(): bool
    {
        return $this->getFailedCount() > 0;
    }

    /**
     * Get success rate percentage
     * 
     * @return float Tỷ lệ thành công (0-100)
     */
    public function getSuccessRate(): float
    {
        $total = $this->getTotalProducts();
        
        if ($total === 0) {
            return 0.0;
        }

        return round(($this->getSuccessCount() / $total) * 100, 2);
    }

    /**
     * Get summary information
     * 
     * @return array Thông tin tổng quan về response
     */
    public function getSummary(): array
    {
        return [
            'total_products' => $this->getTotalProducts(),
            'success_count' => $this->getSuccessCount(),
            'failed_count' => $this->getFailedCount(),
            'success_rate' => $this->getSuccessRate(),
            'is_all_success' => $this->isAllSuccess(),
            'has_failures' => $this->hasFailures()
        ];
    }

    /**
     * Create from API response
     * 
     * @param array $response API response từ Nhanh.vn
     * @return self
     */
    public static function createFromApiResponse(array $response): self
    {
        return new self($response);
    }

    /**
     * Create empty response
     * 
     * @return self
     */
    public static function createEmpty(): self
    {
        return new self([
            'ids' => [],
            'barcodes' => []
        ]);
    }
}
