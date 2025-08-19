<?php

namespace Puleeno\NhanhVn\Entities\Customer;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Puleeno\NhanhVn\Exceptions\InvalidDataException;

/**
 * CustomerAddResponse - Entity đại diện cho response từ API thêm/sửa khách hàng
 *
 * Entity này chứa thông tin phản hồi từ API /api/customer/add
 * bao gồm trạng thái thành công/thất bại và các thông tin liên quan
 *
 * @package Puleeno\NhanhVn\Entities\Customer
 */
class CustomerAddResponse extends AbstractEntity
{
    // Response codes
    public const SUCCESS_CODE = 1;     // Thành công
    public const ERROR_CODE = 0;       // Thất bại

    /**
     * Validate dữ liệu response
     *
     * @throws InvalidDataException Khi dữ liệu không hợp lệ
     */
    protected function validate(): void
    {
        $code = $this->getAttribute('code');
        if (!in_array($code, [self::SUCCESS_CODE, self::ERROR_CODE])) {
            throw new InvalidDataException('Mã phản hồi không hợp lệ');
        }

        // Nếu có lỗi, phải có messages
        if ($code === self::ERROR_CODE && !$this->hasAttribute('messages')) {
            throw new InvalidDataException('Response lỗi phải có messages');
        }
    }

    /**
     * Tạo response thành công từ API response
     *
     * @param array $apiResponse Response từ API Nhanh.vn
     * @return self
     */
    public static function createFromApiResponse(array $apiResponse): self
    {
        return new self($apiResponse);
    }

    /**
     * Tạo response thành công với dữ liệu tùy chỉnh
     *
     * @param array $data Dữ liệu response
     * @return self
     */
    public static function createSuccessResponse(array $data): self
    {
        $responseData = [
            'code' => self::SUCCESS_CODE,
            'messages' => [],
            'data' => $data
        ];

        return new self($responseData);
    }

    /**
     * Tạo response lỗi với messages
     *
     * @param array $messages Danh sách lỗi
     * @return self
     */
    public static function createErrorResponse(array $messages): self
    {
        $responseData = [
            'code' => self::ERROR_CODE,
            'messages' => $messages,
            'data' => []
        ];

        return new self($responseData);
    }

    /**
     * Kiểm tra xem request có thành công không
     *
     * @return bool True nếu thành công, false nếu thất bại
     */
    public function isSuccess(): bool
    {
        return $this->getAttribute('code') === self::SUCCESS_CODE;
    }

    /**
     * Kiểm tra xem request có thất bại không
     *
     * @return bool True nếu thất bại, false nếu thành công
     */
    public function isError(): bool
    {
        return $this->getAttribute('code') === self::ERROR_CODE;
    }

    /**
     * Lấy mã phản hồi
     *
     * @return int Mã phản hồi (1: thành công, 0: thất bại)
     */
    public function getCode(): int
    {
        return $this->getAttribute('code');
    }

    /**
     * Lấy danh sách messages
     *
     * @return array Danh sách messages (lỗi hoặc thông báo)
     */
    public function getMessages(): array
    {
        return $this->getAttribute('messages') ?? [];
    }

    /**
     * Lấy tất cả messages dưới dạng string
     *
     * @return string Messages được nối bằng dấu phẩy
     */
    public function getAllMessagesAsString(): string
    {
        $messages = $this->getMessages();
        return implode(', ', $messages);
    }

    /**
     * Lấy message đầu tiên
     *
     * @return string|null Message đầu tiên hoặc null nếu không có
     */
    public function getFirstMessage(): ?string
    {
        $messages = $this->getMessages();
        return $messages[0] ?? null;
    }

    /**
     * Lấy dữ liệu response
     *
     * @return array Dữ liệu response
     */
    public function getData(): array
    {
        return $this->getAttribute('data') ?? [];
    }

    /**
     * Lấy số lượng khách hàng đã xử lý thành công
     *
     * @return int Số lượng khách hàng thành công
     */
    public function getSuccessCount(): int
    {
        $data = $this->getData();
        return count($data);
    }

    /**
     * Lấy tổng số khách hàng được gửi
     *
     * @return int Tổng số khách hàng
     */
    public function getTotalCount(): int
    {
        // Có thể cần thêm logic để lấy tổng số từ request
        return $this->getSuccessCount();
    }

    /**
     * Lấy tỷ lệ thành công
     *
     * @return float Tỷ lệ thành công (0.0 - 1.0)
     */
    public function getSuccessRate(): float
    {
        $total = $this->getTotalCount();
        if ($total === 0) {
            return 0.0;
        }

        return $this->getSuccessCount() / $total;
    }

    /**
     * Lấy danh sách ID khách hàng đã xử lý
     *
     * @return array Danh sách ID khách hàng
     */
    public function getProcessedCustomerIds(): array
    {
        $data = $this->getData();
        $ids = [];

        foreach ($data as $customerData) {
            if (isset($customerData['id'])) {
                $ids[] = $customerData['id'];
            }
        }

        return $ids;
    }

    /**
     * Lấy ID khách hàng đầu tiên đã xử lý
     *
     * @return int|null ID khách hàng đầu tiên hoặc null
     */
    public function getFirstProcessedCustomerId(): ?int
    {
        $ids = $this->getProcessedCustomerIds();
        return $ids[0] ?? null;
    }

    /**
     * Lấy thông tin khách hàng theo ID
     *
     * @param int $customerId ID khách hàng
     * @return array|null Thông tin khách hàng hoặc null nếu không tìm thấy
     */
    public function getCustomerById(int $customerId): ?array
    {
        $data = $this->getData();

        foreach ($data as $customerData) {
            if (isset($customerData['id']) && $customerData['id'] === $customerId) {
                return $customerData;
            }
        }

        return null;
    }

    /**
     * Lấy thông tin khách hàng theo số điện thoại
     *
     * @param string $mobile Số điện thoại
     * @return array|null Thông tin khách hàng hoặc null nếu không tìm thấy
     */
    public function getCustomerByMobile(string $mobile): ?array
    {
        $data = $this->getData();

        foreach ($data as $customerData) {
            if (isset($customerData['mobile']) && $customerData['mobile'] === $mobile) {
                return $customerData;
            }
        }

        return null;
    }

    /**
     * Lấy summary thống kê
     *
     * @return array Thống kê tổng quan
     */
    public function getSummary(): array
    {
        return [
            'total_customers' => $this->getTotalCount(),
            'success_count' => $this->getSuccessCount(),
            'success_rate' => $this->getSuccessRate(),
            'processed_ids' => $this->getProcessedCustomerIds(),
            'is_success' => $this->isSuccess(),
            'code' => $this->getCode(),
            'message' => $this->getFirstMessage()
        ];
    }
}
