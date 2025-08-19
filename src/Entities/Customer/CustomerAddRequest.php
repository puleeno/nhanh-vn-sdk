<?php

namespace Puleeno\NhanhVn\Entities\Customer;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Puleeno\NhanhVn\Exceptions\InvalidDataException;

/**
 * CustomerAddRequest - Entity đại diện cho request thêm/sửa khách hàng
 *
 * Entity này chứa dữ liệu cần thiết để thêm mới hoặc cập nhật khách hàng
 * trên hệ thống Nhanh.vn thông qua API /api/customer/add
 *
 * @package Puleeno\NhanhVn\Entities\Customer
 */
class CustomerAddRequest extends AbstractEntity
{
    // Customer types
    public const TYPE_RETAIL = 1;      // Khách lẻ
    public const TYPE_WHOLESALE = 2;   // Khách buôn

    // Gender constants
    public const GENDER_MALE = 1;      // Nam
    public const GENDER_FEMALE = 2;    // Nữ

    /**
     * Validate dữ liệu request
     *
     * @throws InvalidDataException Khi dữ liệu không hợp lệ
     */
    protected function validate(): void
    {
        // Validate required fields
        if (!$this->hasAttribute('name') || empty($this->getAttribute('name'))) {
            throw new InvalidDataException('Tên khách hàng không được để trống');
        }

        if (!$this->hasAttribute('mobile') || empty($this->getAttribute('mobile'))) {
            throw new InvalidDataException('Số điện thoại không được để trống');
        }

        // Validate mobile format (basic validation)
        $mobile = $this->getAttribute('mobile');
        if (!preg_match('/^[0-9]{10,11}$/', $mobile)) {
            throw new InvalidDataException('Số điện thoại không đúng định dạng');
        }

        // Validate customer type
        if ($this->hasAttribute('type')) {
            $type = $this->getAttribute('type');
            if (!in_array($type, [self::TYPE_RETAIL, self::TYPE_WHOLESALE])) {
                throw new InvalidDataException('Loại khách hàng không hợp lệ');
            }
        }

        // Validate gender
        if ($this->hasAttribute('gender')) {
            $gender = $this->getAttribute('gender');
            if (!in_array($gender, [self::GENDER_MALE, self::GENDER_FEMALE])) {
                throw new InvalidDataException('Giới tính không hợp lệ');
            }
        }

        // Validate birthday format
        if ($this->hasAttribute('birthday')) {
            $birthday = $this->getAttribute('birthday');
            if (!empty($birthday) && !strtotime($birthday)) {
                throw new InvalidDataException('Ngày sinh không đúng định dạng');
            }
        }

        // Validate email format
        if ($this->hasAttribute('email')) {
            $email = $this->getAttribute('email');
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidDataException('Email không đúng định dạng');
            }
        }

        // Validate points (must be non-negative)
        if ($this->hasAttribute('points')) {
            $points = $this->getAttribute('points');
            if ($points < 0) {
                throw new InvalidDataException('Điểm tích lũy không được âm');
            }
        }
    }

    /**
     * Convert entity data sang format API Nhanh.vn
     *
     * @return array Dữ liệu đã được format theo chuẩn API
     */
    public function toApiFormat(): array
    {
        $apiData = [];

        // Required fields
        $apiData['name'] = $this->getAttribute('name');
        $apiData['mobile'] = $this->getAttribute('mobile');

        // Optional fields
        if ($this->hasAttribute('type')) {
            $apiData['type'] = $this->getAttribute('type');
        }

        if ($this->hasAttribute('address')) {
            $apiData['address'] = $this->getAttribute('address');
        }

        if ($this->hasAttribute('businessName')) {
            $apiData['businessName'] = $this->getAttribute('businessName');
        }

        if ($this->hasAttribute('taxCode')) {
            $apiData['taxCode'] = $this->getAttribute('taxCode');
        }

        if ($this->hasAttribute('points')) {
            $apiData['points'] = $this->getAttribute('points');
        }

        if ($this->hasAttribute('gender')) {
            $apiData['gender'] = $this->getAttribute('gender');
        }

        if ($this->hasAttribute('birthday')) {
            $apiData['birthday'] = $this->getAttribute('birthday');
        }

        if ($this->hasAttribute('cityName')) {
            $apiData['cityName'] = $this->getAttribute('cityName');
        }

        if ($this->hasAttribute('districtName')) {
            $apiData['districtName'] = $this->getAttribute('districtName');
        }

        if ($this->hasAttribute('wardName')) {
            $apiData['wardName'] = $this->getAttribute('wardName');
        }

        if ($this->hasAttribute('email')) {
            $apiData['email'] = $this->getAttribute('email');
        }

        if ($this->hasAttribute('pid')) {
            $apiData['pid'] = $this->getAttribute('pid');
        }

        if ($this->hasAttribute('description')) {
            $apiData['description'] = $this->getAttribute('description');
        }

        if ($this->hasAttribute('facebookLink')) {
            $apiData['facebookLink'] = $this->getAttribute('facebookLink');
        }

        if ($this->hasAttribute('groupId')) {
            $apiData['groupId'] = $this->getAttribute('groupId');
        }

        if ($this->hasAttribute('fromCustomer')) {
            $apiData['fromCustomer'] = $this->getAttribute('fromCustomer');
        }

        if ($this->hasAttribute('saleName')) {
            $apiData['saleName'] = $this->getAttribute('saleName');
        }

        return $apiData;
    }

    // Getters cho các trường chính
    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getMobile(): string
    {
        return $this->getAttribute('mobile');
    }

    public function getType(): ?int
    {
        return $this->getAttribute('type');
    }

    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }

    public function getBusinessName(): ?string
    {
        return $this->getAttribute('businessName');
    }

    public function getTaxCode(): ?string
    {
        return $this->getAttribute('taxCode');
    }

    public function getPoints(): ?int
    {
        return $this->getAttribute('points');
    }

    public function getGender(): ?int
    {
        return $this->getAttribute('gender');
    }

    public function getBirthday(): ?string
    {
        return $this->getAttribute('birthday');
    }

    public function getCityName(): ?string
    {
        return $this->getAttribute('cityName');
    }

    public function getDistrictName(): ?string
    {
        return $this->getAttribute('districtName');
    }

    public function getWardName(): ?string
    {
        return $this->getAttribute('wardName');
    }

    public function getPid(): ?string
    {
        return $this->getAttribute('pid');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getFacebookLink(): ?string
    {
        return $this->getAttribute('facebookLink');
    }

    public function getGroupId(): ?int
    {
        return $this->getAttribute('groupId');
    }

    public function getFromCustomer(): ?string
    {
        return $this->getAttribute('fromCustomer');
    }

    public function getSaleName(): ?string
    {
        return $this->getAttribute('saleName');
    }

    // Helper methods
    public function isRetail(): bool
    {
        return $this->getType() === self::TYPE_RETAIL;
    }

    public function isWholesale(): bool
    {
        return $this->getType() === self::TYPE_WHOLESALE;
    }

    public function isMale(): bool
    {
        return $this->getGender() === self::GENDER_MALE;
    }

    public function isFemale(): bool
    {
        return $this->getGender() === self::GENDER_FEMALE;
    }

    public function hasReferral(): bool
    {
        return $this->hasAttribute('fromCustomer') && !empty($this->getAttribute('fromCustomer'));
    }

    public function hasSalesPerson(): bool
    {
        return $this->hasAttribute('saleName') && !empty($this->getAttribute('saleName'));
    }
}
