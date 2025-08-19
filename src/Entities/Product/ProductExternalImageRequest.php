<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Puleeno\NhanhVn\Exceptions\InvalidDataException;

/**
 * Product External Image Request Entity
 *
 * Entity này đại diện cho request thêm ảnh sản phẩm từ CDN bên ngoài
 * vào hệ thống Nhanh.vn
 *
 * @package Puleeno\NhanhVn\Entities\Product
 * @author Puleeno
 * @since 1.0.0
 */
class ProductExternalImageRequest extends AbstractEntity
{
    /**
     * Các trường bắt buộc cho request
     */
    protected const REQUIRED_FIELDS = ['productId', 'externalImages'];

    /**
     * Các trường tùy chọn
     */
    protected const OPTIONAL_FIELDS = ['mode'];

    /**
     * Các mode hỗ trợ
     */
    protected const SUPPORTED_MODES = ['update', 'deleteall'];

    /**
     * Validate dữ liệu request
     *
     * @throws InvalidDataException Khi dữ liệu không hợp lệ
     */
    protected function validate(): void
    {
        // Kiểm tra các trường bắt buộc
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!$this->hasAttribute($field)) {
                throw new InvalidDataException("Trường '{$field}' là bắt buộc");
            }
        }

        // Validate productId
        $productId = $this->getProductId();
        if (!is_numeric($productId) || $productId <= 0) {
            throw new InvalidDataException("productId phải là số nguyên dương");
        }

        // Validate externalImages
        $externalImages = $this->getExternalImages();
        if (!is_array($externalImages) || empty($externalImages)) {
            throw new InvalidDataException("externalImages phải là mảng không rỗng");
        }

        if (count($externalImages) > 20) {
            throw new InvalidDataException("Mỗi sản phẩm tối đa 20 ảnh");
        }

        foreach ($externalImages as $imageUrl) {
            if (!is_string($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                throw new InvalidDataException("externalImages phải chứa các URL hợp lệ");
            }
        }

        // Validate mode nếu có
        $mode = $this->getMode();
        if ($mode !== null && !in_array($mode, self::SUPPORTED_MODES, true)) {
            throw new InvalidDataException("Mode không hợp lệ. Hỗ trợ: " . implode(', ', self::SUPPORTED_MODES));
        }
    }

    /**
     * Lấy ID sản phẩm trên Nhanh.vn
     *
     * @return int ID sản phẩm
     */
    public function getProductId(): int
    {
        return (int) $this->getAttribute('productId');
    }

    /**
     * Lấy danh sách ảnh từ CDN bên ngoài
     *
     * @return array Danh sách URL ảnh
     */
    public function getExternalImages(): array
    {
        return $this->getAttribute('externalImages', []);
    }

    /**
     * Lấy mode xử lý ảnh
     *
     * @return string|null Mode xử lý (update hoặc deleteall)
     */
    public function getMode(): ?string
    {
        return $this->getAttribute('mode', 'update');
    }

    /**
     * Kiểm tra xem có phải mode update không
     *
     * @return bool True nếu là mode update
     */
    public function isUpdateMode(): bool
    {
        return $this->getMode() === 'update';
    }

    /**
     * Kiểm tra xem có phải mode deleteall không
     *
     * @return bool True nếu là mode deleteall
     */
    public function isDeleteAllMode(): bool
    {
        return $this->getMode() === 'deleteall';
    }

    /**
     * Lấy số lượng ảnh
     *
     * @return int Số lượng ảnh
     */
    public function getImageCount(): int
    {
        return count($this->getExternalImages());
    }

    /**
     * Kiểm tra xem có ảnh nào không
     *
     * @return bool True nếu có ảnh
     */
    public function hasImages(): bool
    {
        return $this->getImageCount() > 0;
    }

    /**
     * Chuyển đổi thành định dạng API
     *
     * @return array Dữ liệu theo định dạng API
     */
    public function toApiFormat(): array
    {
        $data = [
            'productId' => $this->getProductId(),
            'externalImages' => $this->getExternalImages(),
        ];

        $mode = $this->getMode();
        if ($mode !== null && $mode !== 'update') {
            $data['mode'] = $mode;
        }

        return $data;
    }

    /**
     * Tạo instance từ mảng dữ liệu
     *
     * @param array $data Dữ liệu sản phẩm
     * @return self Instance mới
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều instance từ mảng dữ liệu
     *
     * @param array $productsData Mảng dữ liệu sản phẩm
     * @return array Mảng các instance
     */
    public static function createMultipleFromArray(array $productsData): array
    {
        $requests = [];
        foreach ($productsData as $productData) {
            $requests[] = self::createFromArray($productData);
        }
        return $requests;
    }

    /**
     * Validate nhiều request cùng lúc
     *
     * @param array $requests Mảng các request
     * @return array Mảng các lỗi validation
     */
    public static function validateMultiple(array $requests): array
    {
        $errors = [];
        foreach ($requests as $index => $request) {
            try {
                if ($request instanceof self) {
                    $request->validate();
                } else {
                    $errors[] = "Request tại index {$index} không phải instance hợp lệ";
                }
            } catch (InvalidDataException $e) {
                $errors[] = "Request tại index {$index}: " . $e->getMessage();
            }
        }
        return $errors;
    }

    /**
     * Kiểm tra xem có thể gửi request không (tối đa 10 sản phẩm)
     *
     * @param array $requests Mảng các request
     * @return bool True nếu có thể gửi
     */
    public static function canSendBatch(array $requests): bool
    {
        return count($requests) <= 10;
    }
}
