<?php

declare(strict_types=1);

namespace Puleeno\NhanhVn\Entities\Shipping;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Entity đại diện cho request tìm kiếm địa điểm (thành phố, quận huyện, phường xã)
 * 
 * @package NhanhVn\Sdk\Entities\Shipping
 * @author Nhanh.vn SDK Team
 */
class LocationSearchRequest extends AbstractEntity
{
    /**
     * Loại địa điểm cần tìm: CITY, DISTRICT, WARD
     */
    protected string $type = 'CITY';

    /**
     * ID của địa điểm cha (dùng cho DISTRICT và WARD)
     */
    protected ?int $parentId = null;

    /**
     * Constructor
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Lấy loại địa điểm cần tìm
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Thiết lập loại địa điểm cần tìm
     */
    public function setType(string $type): self
    {
        $this->type = $type;
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
     * Tạo request tìm kiếm thành phố
     */
    public static function createCitySearch(): self
    {
        return (new self())->setType('CITY');
    }

    /**
     * Tạo request tìm kiếm quận huyện theo thành phố
     */
    public static function createDistrictSearch(int $cityId): self
    {
        return (new self())
            ->setType('DISTRICT')
            ->setParentId($cityId);
    }

    /**
     * Tạo request tìm kiếm phường xã theo quận huyện
     */
    public static function createWardSearch(int $districtId): self
    {
        return (new self())
            ->setType('WARD')
            ->setParentId($districtId);
    }

    /**
     * Chuyển đổi thành array để gửi API
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
        ];

        if ($this->parentId !== null) {
            $data['parentId'] = $this->parentId;
        }

        return $data;
    }

    /**
     * Validate dữ liệu request
     */
    protected function validate(): void
    {
        if (!in_array($this->type, ['CITY', 'DISTRICT', 'WARD'])) {
            $this->addError('type', 'Loại địa điểm không hợp lệ. Chỉ chấp nhận: CITY, DISTRICT, WARD');
        }

        if (in_array($this->type, ['DISTRICT', 'WARD']) && $this->parentId === null) {
            $this->addError('parentId', 'ID địa điểm cha không được để trống khi tìm kiếm ' . strtolower($this->type));
        }
    }

    /**
     * Lấy danh sách lỗi validation
     */
    public function getValidationErrors(): array
    {
        $errors = [];
        foreach ($this->getErrors() as $field => $messages) {
            $errors = array_merge($errors, $messages);
        }
        return $errors;
    }
}
