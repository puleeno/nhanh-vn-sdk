<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Entity đại diện cho response từ API tìm kiếm địa điểm
 *
 * @package NhanhVn\Sdk\Entities\Shipping
 * @author Nhanh.vn SDK Team
 */
class LocationSearchResponse extends AbstractEntity
{
    /**
     * Mã kết quả: 1 = thành công, 0 = thất bại
     */
    protected int $code;

    /**
     * Danh sách thông báo lỗi (nếu có)
     */
    protected array $messages = [];

    /**
     * Danh sách địa điểm tìm được
     */
    protected array $data = [];

    /**
     * Constructor
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Lấy mã kết quả
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Thiết lập mã kết quả
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Lấy danh sách thông báo lỗi
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Thiết lập danh sách thông báo lỗi
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Lấy danh sách địa điểm
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Thiết lập danh sách địa điểm
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Kiểm tra xem API call có thành công không
     */
    public function isSuccess(): bool
    {
        return $this->code === 1;
    }

    /**
     * Kiểm tra xem API call có lỗi không
     */
    public function hasError(): bool
    {
        return $this->code === 0;
    }

    /**
     * Lấy thông báo lỗi đầu tiên
     */
    public function getFirstError(): ?string
    {
        return $this->messages[0] ?? null;
    }

    /**
     * Lấy tất cả thông báo lỗi dưới dạng string
     */
    public function getErrorMessages(): string
    {
        return implode(', ', $this->messages);
    }

    /**
     * Lấy số lượng địa điểm tìm được
     */
    public function getCount(): int
    {
        return count($this->data);
    }

    /**
     * Kiểm tra xem có địa điểm nào không
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Lấy địa điểm theo ID
     */
    public function getLocationById(int $id): ?Location
    {
        foreach ($this->data as $location) {
            if ($location instanceof Location && $location->getId() === $id) {
                return $location;
            }
        }
        return null;
    }

    /**
     * Lọc địa điểm theo tên (tìm kiếm gần đúng)
     */
    public function filterByName(string $name): array
    {
        $filtered = [];
        $searchName = strtolower(trim($name));

        foreach ($this->data as $location) {
            if (
                $location instanceof Location &&
                str_contains(strtolower($location->getName()), $searchName)
            ) {
                $filtered[] = $location;
            }
        }

        return $filtered;
    }

    /**
     * Chuyển đổi thành array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'messages' => $this->messages,
            'data' => array_map(fn($location) => $location->toArray(), $this->data),
        ];
    }

    /**
     * Tạo response thành công
     */
    public static function createSuccess(array $locations): self
    {
        return (new self())
            ->setCode(1)
            ->setMessages([])
            ->setData($locations);
    }

    /**
     * Tạo response lỗi
     */
    public static function createError(array $messages): self
    {
        return (new self())
            ->setCode(0)
            ->setMessages($messages)
            ->setData([]);
    }

    /**
     * Tạo response trống
     */
    public static function createEmpty(): self
    {
        return (new self())
            ->setCode(1)
            ->setMessages([])
            ->setData([]);
    }

    /**
     * Validate dữ liệu entity
     */
    protected function validate(): void
    {
        if (!isset($this->code)) {
            $this->addError('code', 'Code response không được để trống');
        }

        if (!is_array($this->messages)) {
            $this->addError('messages', 'Messages phải là array');
        }

        if (!is_array($this->data)) {
            $this->addError('data', 'Data phải là array');
        }
    }
}
