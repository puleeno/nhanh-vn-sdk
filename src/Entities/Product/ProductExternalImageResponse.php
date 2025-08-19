<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Puleeno\NhanhVn\Exceptions\InvalidDataException;

/**
 * Product External Image Response Entity
 *
 * Entity này đại diện cho response từ API thêm ảnh sản phẩm
 * từ CDN bên ngoài vào hệ thống Nhanh.vn
 *
 * @package Puleeno\NhanhVn\Entities\Product
 * @author Puleeno
 * @since 1.0.0
 */
class ProductExternalImageResponse extends AbstractEntity
{
    /**
     * Các trường bắt buộc cho response
     */
    protected const REQUIRED_FIELDS = ['code'];

    /**
     * Các trường tùy chọn
     */
    protected const OPTIONAL_FIELDS = ['messages', 'data'];

    /**
     * Mã thành công
     */
    protected const SUCCESS_CODE = 1;

    /**
     * Mã thất bại
     */
    protected const ERROR_CODE = 0;

    /**
     * Validate dữ liệu response
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

        // Validate code
        $code = $this->getCode();
        if (!in_array($code, [self::SUCCESS_CODE, self::ERROR_CODE], true)) {
            throw new InvalidDataException("Code không hợp lệ. Hỗ trợ: " . self::SUCCESS_CODE . " hoặc " . self::ERROR_CODE);
        }

        // Validate messages nếu có lỗi
        if ($code === self::ERROR_CODE) {
            $messages = $this->getMessages();
            if (empty($messages)) {
                throw new InvalidDataException("Messages không được để trống khi có lỗi");
            }
        }

        // Validate data nếu thành công
        if ($code === self::SUCCESS_CODE) {
            $data = $this->getData();
            if (!is_array($data)) {
                throw new InvalidDataException("Data phải là mảng khi thành công");
            }
        }
    }

    /**
     * Lấy mã kết quả
     *
     * @return int Mã kết quả (1 = success, 0 = failed)
     */
    public function getCode(): int
    {
        return (int) $this->getAttribute('code');
    }

    /**
     * Lấy danh sách thông báo lỗi
     *
     * @return array Danh sách thông báo lỗi
     */
    public function getMessages(): array
    {
        return $this->getAttribute('messages', []);
    }

    /**
     * Lấy danh sách ID sản phẩm đã xử lý thành công
     *
     * @return array Danh sách ID sản phẩm
     */
    public function getData(): array
    {
        return $this->getAttribute('data', []);
    }

    /**
     * Kiểm tra xem request có thành công không
     *
     * @return bool True nếu thành công
     */
    public function isSuccess(): bool
    {
        return $this->getCode() === self::SUCCESS_CODE;
    }

    /**
     * Kiểm tra xem request có thất bại không
     *
     * @return bool True nếu thất bại
     */
    public function isError(): bool
    {
        return $this->getCode() === self::ERROR_CODE;
    }

    /**
     * Lấy thông báo lỗi đầu tiên
     *
     * @return string|null Thông báo lỗi đầu tiên hoặc null
     */
    public function getFirstMessage(): ?string
    {
        $messages = $this->getMessages();
        return !empty($messages) ? $messages[0] : null;
    }

    /**
     * Lấy tất cả thông báo lỗi dưới dạng chuỗi
     *
     * @return string Tất cả thông báo lỗi được nối bằng dấu phẩy
     */
    public function getAllMessagesAsString(): string
    {
        return implode(', ', $this->getMessages());
    }

    /**
     * Lấy số lượng sản phẩm đã xử lý thành công
     *
     * @return int Số lượng sản phẩm
     */
    public function getTotalProcessedProducts(): int
    {
        return count($this->getData());
    }

    /**
     * Kiểm tra xem có sản phẩm nào được xử lý thành công không
     *
     * @return bool True nếu có sản phẩm được xử lý
     */
    public function hasProcessedProducts(): bool
    {
        return $this->getTotalProcessedProducts() > 0;
    }

    /**
     * Lấy ID sản phẩm đầu tiên đã xử lý thành công
     *
     * @return int|null ID sản phẩm đầu tiên hoặc null
     */
    public function getFirstProcessedProductId(): ?int
    {
        $data = $this->getData();
        return !empty($data) ? (int) $data[0] : null;
    }

    /**
     * Lấy tất cả ID sản phẩm đã xử lý thành công
     *
     * @return array Mảng ID sản phẩm
     */
    public function getAllProcessedProductIds(): array
    {
        return array_map('intval', $this->getData());
    }

    /**
     * Lấy thông tin tóm tắt response
     *
     * @return array Thông tin tóm tắt
     */
    public function getSummary(): array
    {
        return [
            'success' => $this->isSuccess(),
            'code' => $this->getCode(),
            'totalProcessedProducts' => $this->getTotalProcessedProducts(),
            'hasErrors' => $this->isError(),
            'errorCount' => count($this->getMessages()),
            'firstError' => $this->getFirstMessage(),
        ];
    }

    /**
     * Tạo instance từ response API
     *
     * @param array $response Response từ API
     * @return self Instance mới
     */
    public static function createFromApiResponse(array $response): self
    {
        return new self($response);
    }

    /**
     * Tạo response thành công
     *
     * @param array $productIds Mảng ID sản phẩm đã xử lý
     * @return self Instance response thành công
     */
    public static function createSuccessResponse(array $productIds): self
    {
        return new self([
            'code' => self::SUCCESS_CODE,
            'data' => $productIds,
        ]);
    }

    /**
     * Tạo response thất bại
     *
     * @param array $messages Mảng thông báo lỗi
     * @return self Instance response thất bại
     */
    public static function createErrorResponse(array $messages): self
    {
        return new self([
            'code' => self::ERROR_CODE,
            'messages' => $messages,
        ]);
    }
}
