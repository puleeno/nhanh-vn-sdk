<?php

namespace Puleeno\NhanhVn\Entities\Customer;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Puleeno\NhanhVn\Exceptions\InvalidDataException;

/**
 * Customer Search Request DTO
 *
 * Represents the request parameters for searching customers
 *
 * @package Puleeno\NhanhVn\Entities\Customer
 */
class CustomerSearchRequest extends AbstractEntity
{
    protected const REQUIRED_FIELDS = [];
    protected const OPTIONAL_FIELDS = [
        'page', 'icpp', 'id', 'mobile', 'lastBoughtDateFrom', 'lastBoughtDateTo',
        'updatedAtFrom', 'updatedAtTo', 'type'
    ];

    /**
     * Default values
     */
    protected const DEFAULT_PAGE = 1;
    protected const DEFAULT_ICPP = 10;
    protected const MAX_ICPP = 50;

    /**
     * Customer types constants
     */
    public const TYPE_RETAIL = 1;      // Khách lẻ
    public const TYPE_WHOLESALE = 2;   // Khách buôn
    public const TYPE_AGENT = 3;       // Đại lý

    /**
     * Validate search request data
     *
     * @throws \Puleeno\NhanhVn\Exceptions\InvalidDataException
     */
    protected function validate(): void
    {
        // Validate page number
        if ($this->hasAttribute('page') && $this->getAttribute('page') < 1) {
            throw new InvalidDataException('Số trang phải lớn hơn 0');
        }

        // Validate items per page
        if ($this->hasAttribute('icpp')) {
            $icpp = (int) $this->getAttribute('icpp');
            if ($icpp < 1 || $icpp > self::MAX_ICPP) {
                throw new InvalidDataException(
                    'Số lượng khách hàng trên 1 trang phải từ 1 đến ' . self::MAX_ICPP
                );
            }
        }

        // Validate customer type
        if ($this->hasAttribute('type') && !in_array($this->getAttribute('type'), [
            self::TYPE_RETAIL, self::TYPE_WHOLESALE, self::TYPE_AGENT
        ])) {
            throw new InvalidDataException('Loại khách hàng không hợp lệ');
        }

        // Validate mobile format (if provided)
        if ($this->hasAttribute('mobile') && !preg_match('/^0\d{9}$/', $this->getAttribute('mobile'))) {
            throw new InvalidDataException('Số điện thoại không đúng định dạng');
        }

        // Validate date formats
        $this->validateDateFormat('lastBoughtDateFrom', 'Y-m-d');
        $this->validateDateFormat('lastBoughtDateTo', 'Y-m-d');
        $this->validateDateFormat('updatedAtFrom', 'Y-m-d H:i:s');
        $this->validateDateFormat('updatedAtTo', 'Y-m-d H:i:s');

        // Validate date ranges
        $this->validateDateRange('lastBoughtDateFrom', 'lastBoughtDateTo');
        $this->validateDateRange('updatedAtFrom', 'updatedAtTo');
    }

    /**
     * Validate date format
     *
     * @param string $field
     * @param string $format
     * @throws InvalidDataException
     */
    private function validateDateFormat(string $field, string $format): void
    {
        if (!$this->hasAttribute($field)) {
            return;
        }

        $date = $this->getAttribute($field);
        $parsedDate = \DateTime::createFromFormat($format, $date);

        if (!$parsedDate || $parsedDate->format($format) !== $date) {
            throw new InvalidDataException(
                "Định dạng ngày tháng không đúng cho trường {$field}. Định dạng yêu cầu: {$format}"
            );
        }
    }

    /**
     * Validate date range
     *
     * @param string $fromField
     * @param string $toField
     * @throws InvalidDataException
     */
    private function validateDateRange(string $fromField, string $toField): void
    {
        if (!$this->hasAttribute($fromField) || !$this->hasAttribute($toField)) {
            return;
        }

        $fromDate = $this->getAttribute($fromField);
        $toDate = $this->getAttribute($toField);

        if ($fromDate && $toDate && $fromDate > $toDate) {
            throw new InvalidDataException(
                "Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc cho {$fromField} và {$toField}"
            );
        }
    }

    /**
     * Get page number
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->hasAttribute('page') ? (int) $this->getAttribute('page') : self::DEFAULT_PAGE;
    }

    /**
     * Get items per page
     *
     * @return int
     */
    public function getIcpp(): int
    {
        return $this->hasAttribute('icpp') ? (int) $this->getAttribute('icpp') : self::DEFAULT_ICPP;
    }

    /**
     * Get customer ID filter
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->hasAttribute('id') ? (int) $this->getAttribute('id') : null;
    }

    /**
     * Get mobile filter
     *
     * @return string|null
     */
    public function getMobile(): ?string
    {
        return $this->getAttribute('mobile');
    }

    /**
     * Get last bought date from filter
     *
     * @return string|null
     */
    public function getLastBoughtDateFrom(): ?string
    {
        return $this->getAttribute('lastBoughtDateFrom');
    }

    /**
     * Get last bought date to filter
     *
     * @return string|null
     */
    public function getLastBoughtDateTo(): ?string
    {
        return $this->getAttribute('lastBoughtDateTo');
    }

    /**
     * Get updated at from filter
     *
     * @return string|null
     */
    public function getUpdatedAtFrom(): ?string
    {
        return $this->getAttribute('updatedAtFrom');
    }

    /**
     * Get updated at to filter
     *
     * @return string|null
     */
    public function getUpdatedAtTo(): ?string
    {
        return $this->getAttribute('updatedAtTo');
    }

    /**
     * Get customer type filter
     *
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->hasAttribute('type') ? (int) $this->getAttribute('type') : null;
    }

    /**
     * Check if has ID filter
     *
     * @return bool
     */
    public function hasIdFilter(): bool
    {
        return $this->hasAttribute('id');
    }

    /**
     * Check if has mobile filter
     *
     * @return bool
     */
    public function hasMobileFilter(): bool
    {
        return $this->hasAttribute('mobile');
    }

    /**
     * Check if has date filters
     *
     * @return bool
     */
    public function hasDateFilters(): bool
    {
        return $this->hasAttribute('lastBoughtDateFrom') ||
               $this->hasAttribute('lastBoughtDateTo') ||
               $this->hasAttribute('updatedAtFrom') ||
               $this->hasAttribute('updatedAtTo');
    }

    /**
     * Check if has type filter
     *
     * @return bool
     */
    public function hasTypeFilter(): bool
    {
        return $this->hasAttribute('type');
    }

    /**
     * Check if this is a search by ID
     *
     * @return bool
     */
    public function isSearchById(): bool
    {
        return $this->hasIdFilter();
    }

    /**
     * Check if this is a search by mobile
     *
     * @return bool
     */
    public function isSearchByMobile(): bool
    {
        return $this->hasMobileFilter();
    }

    /**
     * Check if this is a general search
     *
     * @return bool
     */
    public function isGeneralSearch(): bool
    {
        return !$this->hasIdFilter() && !$this->hasMobileFilter();
    }

    /**
     * Convert to API format
     *
     * @return array
     */
    public function toApiFormat(): array
    {
        $data = [];

        if ($this->hasAttribute('page')) {
            $data['page'] = $this->getPage();
        }

        if ($this->hasAttribute('icpp')) {
            $data['icpp'] = $this->getIcpp();
        }

        if ($this->hasAttribute('id')) {
            $data['id'] = $this->getId();
        }

        if ($this->hasAttribute('mobile')) {
            $data['mobile'] = $this->getMobile();
        }

        if ($this->hasAttribute('lastBoughtDateFrom')) {
            $data['lastBoughtDateFrom'] = $this->getLastBoughtDateFrom();
        }

        if ($this->hasAttribute('lastBoughtDateTo')) {
            $data['lastBoughtDateTo'] = $this->getLastBoughtDateTo();
        }

        if ($this->hasAttribute('updatedAtFrom')) {
            $data['updatedAtFrom'] = $this->getUpdatedAtFrom();
        }

        if ($this->hasAttribute('updatedAtTo')) {
            $data['updatedAtTo'] = $this->getUpdatedAtTo();
        }

        if ($this->hasAttribute('type')) {
            $data['type'] = $this->getType();
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param array $data
     * @return self
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Get customer type label
     *
     * @return string|null
     */
    public function getTypeLabel(): ?string
    {
        $type = $this->getType();

        if (!$type) {
            return null;
        }

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
}
