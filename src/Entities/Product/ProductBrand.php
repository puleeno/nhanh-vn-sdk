<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Brand entity
 */
class ProductBrand extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID thương hiệu không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên thương hiệu không được để trống');
        }
    }

    // Basic getters
    public function getId(): mixed
    {
        return $this->getAttribute('id');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getLogo(): ?string
    {
        return $this->getAttribute('logo');
    }

    public function getWebsite(): ?string
    {
        return $this->getAttribute('website');
    }

    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    public function getCountry(): ?string
    {
        return $this->getAttribute('country');
    }

    public function getStatus(): ?string
    {
        return $this->getAttribute('status');
    }

    public function getCreatedAt(): ?string
    {
        return $this->getAttribute('createdAt');
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getAttribute('updatedAt');
    }

    // Business logic methods
    public function isActive(): bool
    {
        return $this->getStatus() === 'Active';
    }

    public function isInactive(): bool
    {
        return $this->getStatus() === 'Inactive';
    }

    public function hasCode(): bool
    {
        return !empty($this->getCode());
    }

    public function hasDescription(): bool
    {
        return !empty($this->getDescription());
    }

    public function hasLogo(): bool
    {
        return !empty($this->getLogo());
    }

    public function hasWebsite(): bool
    {
        return !empty($this->getWebsite());
    }

    public function hasEmail(): bool
    {
        return !empty($this->getEmail());
    }

    public function hasPhone(): bool
    {
        return !empty($this->getPhone());
    }

    public function hasAddress(): bool
    {
        return !empty($this->getAddress());
    }

    public function hasCountry(): bool
    {
        return !empty($this->getCountry());
    }

    public function hasContactInfo(): bool
    {
        return $this->hasEmail() || $this->hasPhone() || $this->hasAddress();
    }

    public function hasOnlinePresence(): bool
    {
        return $this->hasWebsite() || $this->hasEmail();
    }

    public function getDisplayName(): string
    {
        if ($this->hasCode()) {
            return "{$this->getCode()} - {$this->getName()}";
        }

        return $this->getName() ?: '';
    }

    public function getShortDescription(int $length = 100): string
    {
        $description = $this->getDescription() ?: '';
        if (strlen($description) <= $length) {
            return $description;
        }

        return substr($description, 0, $length) . '...';
    }

    public function getFormattedCreatedAt(): string
    {
        $date = $this->getCreatedAt();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getFormattedUpdatedAt(): string
    {
        $date = $this->getUpdatedAt();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getStatusText(): string
    {
        return $this->getStatus() ?: 'Unknown';
    }

    public function getBrandSummary(): string
    {
        $summary = [];

        $summary[] = $this->getName();

        if ($this->hasCountry()) {
            $summary[] = "Xuất xứ: {$this->getCountry()}";
        }

        if ($this->hasContactInfo()) {
            $summary[] = 'Có thông tin liên hệ';
        }

        if ($this->hasOnlinePresence()) {
            $summary[] = 'Có hiện diện trực tuyến';
        }

        return implode(' | ', array_filter($summary));
    }

    /**
     * Kiểm tra xem brand có phải là brand quốc tế không
     */
    public function isInternationalBrand(): bool
    {
        $country = $this->getCountry();
        if (!$country) {
            return false;
        }

        $vietnamKeywords = ['vietnam', 'việt nam', 'vn', 'viet nam'];
        $countryLower = strtolower($country);

        foreach ($vietnamKeywords as $keyword) {
            if (strpos($countryLower, $keyword) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Kiểm tra xem brand có phải là brand Việt Nam không
     */
    public function isVietnameseBrand(): bool
    {
        return !$this->isInternationalBrand();
    }

    /**
     * Lấy domain từ website
     */
    public function getWebsiteDomain(): ?string
    {
        $website = $this->getWebsite();
        if (!$website) {
            return null;
        }

        // Loại bỏ protocol
        $domain = preg_replace('/^https?:\/\//', '', $website);

        // Loại bỏ path
        $domain = strtok($domain, '/');

        return $domain;
    }

    /**
     * Validate email format
     */
    public function isValidEmail(): bool
    {
        $email = $this->getEmail();
        if (!$email) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate website format
     */
    public function isValidWebsite(): bool
    {
        $website = $this->getWebsite();
        if (!$website) {
            return false;
        }

        return filter_var($website, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Tạo brand từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo brand từ tên
     */
    public static function createFromName(string $name, string $code = ''): self
    {
        $data = ['name' => $name];

        if (!empty($code)) {
            $data['code'] = $code;
        }

        return new self($data);
    }

    /**
     * Tạo nhiều brands từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $brands = [];

        foreach ($data as $brandData) {
            $brands[] = self::createFromArray($brandData);
        }

        return $brands;
    }

    /**
     * Lọc brands theo trạng thái
     */
    public static function filterByStatus(array $brands, string $status): array
    {
        return array_filter($brands, function (ProductBrand $brand) use ($status) {
            return $brand->getStatus() === $status;
        });
    }

    /**
     * Lọc brands theo quốc gia
     */
    public static function filterByCountry(array $brands, string $country): array
    {
        return array_filter($brands, function (ProductBrand $brand) use ($country) {
            return $brand->getCountry() === $country;
        });
    }

    /**
     * Lọc brands quốc tế
     */
    public static function filterInternational(array $brands): array
    {
        return array_filter($brands, function (ProductBrand $brand) {
            return $brand->isInternationalBrand();
        });
    }

    /**
     * Lọc brands Việt Nam
     */
    public static function filterVietnamese(array $brands): array
    {
        return array_filter($brands, function (ProductBrand $brand) {
            return $brand->isVietnameseBrand();
        });
    }

    /**
     * Lọc brands có website
     */
    public static function filterWithWebsite(array $brands): array
    {
        return array_filter($brands, function (ProductBrand $brand) {
            return $brand->hasWebsite();
        });
    }

    /**
     * Lọc brands có logo
     */
    public static function filterWithLogo(array $brands): array
    {
        return array_filter($brands, function (ProductBrand $brand) {
            return $brand->hasLogo();
        });
    }

    /**
     * Sắp xếp brands theo tên
     */
    public static function sortByName(array $brands, bool $ascending = true): array
    {
        usort($brands, function (ProductBrand $a, ProductBrand $b) use ($ascending) {
            $nameA = $a->getName() ?? '';
            $nameB = $b->getName() ?? '';

            if ($ascending) {
                return strcmp($nameA, $nameB);
            }

            return strcmp($nameB, $nameA);
        });

        return $brands;
    }

    /**
     * Sắp xếp brands theo code
     */
    public static function sortByCode(array $brands, bool $ascending = true): array
    {
        usort($brands, function (ProductBrand $a, ProductBrand $b) use ($ascending) {
            $codeA = $a->getCode() ?? '';
            $codeB = $b->getCode() ?? '';

            if ($ascending) {
                return strcmp($codeA, $codeB);
            }

            return strcmp($codeB, $codeA);
        });

        return $brands;
    }

    /**
     * Tìm brands theo tên (partial match)
     */
    public static function searchByName(array $brands, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($brands, function (ProductBrand $brand) use ($searchTerm) {
            $name = strtolower($brand->getName() ?? '');
            return strpos($name, $searchTerm) !== false;
        });
    }

    /**
     * Tìm brands theo code (partial match)
     */
    public static function searchByCode(array $brands, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($brands, function (ProductBrand $brand) use ($searchTerm) {
            $code = strtolower($brand->getCode() ?? '');
            return strpos($code, $searchTerm) !== false;
        });
    }

    /**
     * Tìm brands theo quốc gia (partial match)
     */
    public static function searchByCountry(array $brands, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($brands, function (ProductBrand $brand) use ($searchTerm) {
            $country = strtolower($brand->getCountry() ?? '');
            return strpos($country, $searchTerm) !== false;
        });
    }

    /**
     * Lấy danh sách quốc gia unique
     */
    public static function getUniqueCountries(array $brands): array
    {
        $countries = [];

        foreach ($brands as $brand) {
            $country = $brand->getCountry();
            if ($country && !in_array($country, $countries)) {
                $countries[] = $country;
            }
        }

        sort($countries);
        return $countries;
    }

    /**
     * Đếm số brands theo quốc gia
     */
    public static function countByCountry(array $brands): array
    {
        $counts = [];

        foreach ($brands as $brand) {
            $country = $brand->getCountry() ?: 'Unknown';

            if (!isset($counts[$country])) {
                $counts[$country] = 0;
            }

            $counts[$country]++;
        }

        arsort($counts);
        return $counts;
    }
}
