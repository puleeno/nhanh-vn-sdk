<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Entity đại diện cho thông tin địa chỉ (thành phố, quận huyện, phường xã)
 *
 * @package NhanhVn\Sdk\Entities\Shipping
 * @author Nhanh.vn SDK Team
 */
class Location extends AbstractEntity
{
    /**
     * ID của địa điểm
     */
    protected int $id;

    /**
     * Tên địa điểm
     */
    protected string $name;

    /**
     * ID của địa điểm cha (dùng cho DISTRICT và WARD)
     */
    protected ?int $parentId = null;

    /**
     * Loại địa điểm: CITY, DISTRICT, WARD
     */
    protected string $type = 'CITY';

    /**
     * Constructor
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Lấy ID của địa điểm
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Thiết lập ID của địa điểm
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Lấy tên địa điểm
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Thiết lập tên địa điểm
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Lấy ID của địa điểm cha
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * Thiết lập ID của địa điểm cha
     */
    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * Lấy loại địa điểm
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Thiết lập loại địa điểm
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Kiểm tra xem có phải là thành phố không
     */
    public function isCity(): bool
    {
        return $this->type === 'CITY';
    }

    /**
     * Kiểm tra xem có phải là quận huyện không
     */
    public function isDistrict(): bool
    {
        return $this->type === 'DISTRICT';
    }

    /**
     * Kiểm tra xem có phải là phường xã không
     */
    public function isWard(): bool
    {
        return $this->type === 'WARD';
    }

    /**
     * Chuyển đổi thành array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parentId' => $this->parentId,
            'type' => $this->type,
        ];
    }

    /**
     * Tạo từ dữ liệu API response
     */
    public static function fromApiResponse(array $data, string $type = 'CITY'): self
    {
        $location = new self();
        $location->setId($data['id']);
        $location->setName($data['name']);
        $location->setType($type);

        if (isset($data['parentId'])) {
            $location->setParentId($data['parentId']);
        }

        return $location;
    }

    /**
     * Validate dữ liệu entity
     */
    protected function validate(): void
    {
        if (empty($this->id)) {
            $this->addError('id', 'ID địa điểm không được để trống');
        }

        if (empty($this->name)) {
            $this->addError('name', 'Tên địa điểm không được để trống');
        }

        if (!in_array($this->type, ['CITY', 'DISTRICT', 'WARD'])) {
            $this->addError('type', 'Loại địa điểm không hợp lệ');
        }
    }
}
