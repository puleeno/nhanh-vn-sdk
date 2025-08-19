<?php

namespace Puleeno\NhanhVn\Entities\Customer;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Customer Entity
 *
 * Represents a customer in the Nhanh.vn system
 *
 * @package Puleeno\NhanhVn\Entities\Customer
 */
class Customer extends AbstractEntity
{
    protected const REQUIRED_FIELDS = ['id', 'name'];
    protected const OPTIONAL_FIELDS = [
        'type', 'mobile', 'email', 'gender', 'address', 'birthday',
        'code', 'level', 'group', 'totalMoney', 'points', 'cityLocationId',
        'districtLocationId', 'wardLocationId', 'saleName', 'startedDate',
        'taxCode', 'businessName', 'businessAddress'
    ];

    /**
     * Customer types constants
     */
    public const TYPE_RETAIL = 1;      // Khách lẻ
    public const TYPE_WHOLESALE = 2;   // Khách buôn
    public const TYPE_AGENT = 3;       // Đại lý

    /**
     * Gender constants
     */
    public const GENDER_MALE = 1;      // Nam
    public const GENDER_FEMALE = 2;    // Nữ
    public const GENDER_UNKNOWN = null; // Chưa có thông tin

    /**
     * Validate customer data
     *
     * @throws \Puleeno\NhanhVn\Exceptions\InvalidDataException
     */
    protected function validate(): void
    {
        // Validate customer type
        if (
            $this->hasAttribute('type') && !in_array($this->getAttribute('type'), [
            self::TYPE_RETAIL, self::TYPE_WHOLESALE, self::TYPE_AGENT
            ])
        ) {
            throw new \Puleeno\NhanhVn\Exceptions\InvalidDataException(
                'Loại khách hàng không hợp lệ'
            );
        }

        // Validate gender
        if (
            $this->hasAttribute('gender') && !in_array($this->getAttribute('gender'), [
            self::GENDER_MALE, self::GENDER_FEMALE, self::GENDER_UNKNOWN
            ])
        ) {
            throw new \Puleeno\NhanhVn\Exceptions\InvalidDataException(
                'Giới tính không hợp lệ'
            );
        }

        // Validate mobile format (if provided)
        if ($this->hasAttribute('mobile') && !preg_match('/^0\d{9}$/', $this->getAttribute('mobile'))) {
            throw new \Puleeno\NhanhVn\Exceptions\InvalidDataException(
                'Số điện thoại không đúng định dạng'
            );
        }

        // Validate email format (if provided)
        if ($this->hasAttribute('email') && !filter_var($this->getAttribute('email'), FILTER_VALIDATE_EMAIL)) {
            throw new \Puleeno\NhanhVn\Exceptions\InvalidDataException(
                'Email không đúng định dạng'
            );
        }
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->getAttribute('id');
    }

    /**
     * Get customer type
     *
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->hasAttribute('type') ? (int) $this->getAttribute('type') : null;
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    /**
     * Get customer mobile
     *
     * @return string|null
     */
    public function getMobile(): ?string
    {
        return $this->getAttribute('mobile');
    }

    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }

    /**
     * Get customer gender
     *
     * @return int|null
     */
    public function getGender(): ?int
    {
        return $this->hasAttribute('gender') ? (int) $this->getAttribute('gender') : null;
    }

    /**
     * Get customer address
     *
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    /**
     * Get customer birthday
     *
     * @return string|null
     */
    public function getBirthday(): ?string
    {
        return $this->getAttribute('birthday');
    }

    /**
     * Get customer code
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    /**
     * Get customer level
     *
     * @return string|null
     */
    public function getLevel(): ?string
    {
        return $this->getAttribute('level');
    }

    /**
     * Get customer group
     *
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->getAttribute('group');
    }

    /**
     * Get total money spent
     *
     * @return float|null
     */
    public function getTotalMoney(): ?float
    {
        return $this->hasAttribute('totalMoney') ? (float) $this->getAttribute('totalMoney') : null;
    }

    /**
     * Get customer points
     *
     * @return int|null
     */
    public function getPoints(): ?int
    {
        return $this->hasAttribute('points') ? (int) $this->getAttribute('points') : null;
    }

    /**
     * Get city location ID
     *
     * @return int|null
     */
    public function getCityLocationId(): ?int
    {
        return $this->hasAttribute('cityLocationId') ? (int) $this->getAttribute('cityLocationId') : null;
    }

    /**
     * Get district location ID
     *
     * @return int|null
     */
    public function getDistrictLocationId(): ?int
    {
        return $this->hasAttribute('districtLocationId') ? (int) $this->getAttribute('districtLocationId') : null;
    }

    /**
     * Get ward location ID
     *
     * @return int|null
     */
    public function getWardLocationId(): ?int
    {
        return $this->hasAttribute('wardLocationId') ? (int) $this->getAttribute('wardLocationId') : null;
    }

    /**
     * Get sale name
     *
     * @return string|null
     */
    public function getSaleName(): ?string
    {
        return $this->getAttribute('saleName');
    }

    /**
     * Get started date
     *
     * @return string|null
     */
    public function getStartedDate(): ?string
    {
        return $this->getAttribute('startedDate');
    }

    /**
     * Get tax code
     *
     * @return string|null
     */
    public function getTaxCode(): ?string
    {
        return $this->getAttribute('taxCode');
    }

    /**
     * Get business name
     *
     * @return string|null
     */
    public function getBusinessName(): ?string
    {
        return $this->getAttribute('businessName');
    }

    /**
     * Get business address
     *
     * @return string|null
     */
    public function getBusinessAddress(): ?string
    {
        return $this->getAttribute('businessAddress');
    }

    /**
     * Check if customer is retail type
     *
     * @return bool
     */
    public function isRetail(): bool
    {
        return $this->getType() === self::TYPE_RETAIL;
    }

    /**
     * Check if customer is wholesale type
     *
     * @return bool
     */
    public function isWholesale(): bool
    {
        return $this->getType() === self::TYPE_WHOLESALE;
    }

    /**
     * Check if customer is agent type
     *
     * @return bool
     */
    public function isAgent(): bool
    {
        return $this->getType() === self::TYPE_AGENT;
    }

    /**
     * Check if customer is male
     *
     * @return bool
     */
    public function isMale(): bool
    {
        return $this->getGender() === self::GENDER_MALE;
    }

    /**
     * Check if customer is female
     *
     * @return bool
     */
    public function isFemale(): bool
    {
        return $this->getGender() === self::GENDER_FEMALE;
    }

    /**
     * Check if customer has mobile number
     *
     * @return bool
     */
    public function hasMobile(): bool
    {
        return !empty($this->getMobile());
    }

    /**
     * Check if customer has email
     *
     * @return bool
     */
    public function hasEmail(): bool
    {
        return !empty($this->getEmail());
    }

    /**
     * Check if customer has address
     *
     * @return bool
     */
    public function hasAddress(): bool
    {
        return !empty($this->getAddress());
    }

    /**
     * Get customer type label
     *
     * @return string
     */
    public function getTypeLabel(): string
    {
        $type = $this->getType();

        switch ($type) {
            case self::TYPE_RETAIL:
                return 'Khách lẻ';
            case self::TYPE_WHOLESALE:
                return 'Khách buôn';
            case self::TYPE_AGENT:
                return 'Đại lý';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Get gender label
     *
     * @return string
     */
    public function getGenderLabel(): string
    {
        $gender = $this->getGender();

        switch ($gender) {
            case self::GENDER_MALE:
                return 'Nam';
            case self::GENDER_FEMALE:
                return 'Nữ';
            default:
                return 'Chưa có thông tin';
        }
    }
}
