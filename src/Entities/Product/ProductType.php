<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Type entity
 */
class ProductType extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID loại sản phẩm không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên loại sản phẩm không được để trống');
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

    public function getCategory(): ?string
    {
        return $this->getAttribute('category');
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

    public function hasCategory(): bool
    {
        return !empty($this->getCategory());
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

    public function getTypeSummary(): string
    {
        $summary = [];

        $summary[] = $this->getName();

        if ($this->hasCategory()) {
            $summary[] = "Danh mục: {$this->getCategory()}";
        }

        if ($this->hasDescription()) {
            $summary[] = "Mô tả: {$this->getShortDescription()}";
        }

        return implode(' | ', array_filter($summary));
    }

    /**
     * Kiểm tra xem type có phải là type điện tử không
     */
    public function isElectronicsType(): bool
    {
        $name = strtolower($this->getName() ?? '');
        $electronicsKeywords = ['điện tử', 'electronics', 'điện', 'electric', 'digital', 'số'];

        foreach ($electronicsKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem type có phải là type thời trang không
     */
    public function isFashionType(): bool
    {
        $name = strtolower($this->getName() ?? '');
        $fashionKeywords = ['thời trang', 'fashion', 'quần áo', 'clothing', 'giày dép', 'shoes', 'túi xách', 'bag'];

        foreach ($fashionKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem type có phải là type gia dụng không
     */
    public function isHomeApplianceType(): bool
    {
        $name = strtolower($this->getName() ?? '');
        $homeKeywords = ['gia dụng', 'home', 'nhà bếp', 'kitchen', 'nội thất', 'furniture', 'đồ gia dụng'];

        foreach ($homeKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem type có phải là type sức khỏe không
     */
    public function isHealthType(): bool
    {
        $name = strtolower($this->getName() ?? '');
        $healthKeywords = ['sức khỏe', 'health', 'y tế', 'medical', 'thuốc', 'medicine', 'chăm sóc', 'care'];

        foreach ($healthKeywords as $keyword) {
            if (strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Lấy category chính của type
     */
    public function getMainCategory(): string
    {
        if ($this->isElectronicsType()) {
            return 'Electronics';
        }

        if ($this->isFashionType()) {
            return 'Fashion';
        }

        if ($this->isHomeApplianceType()) {
            return 'Home & Garden';
        }

        if ($this->isHealthType()) {
            return 'Health & Beauty';
        }

        return 'Other';
    }

    /**
     * Lấy màu sắc cho category
     */
    public function getCategoryColor(): string
    {
        $category = $this->getMainCategory();

        switch ($category) {
            case 'Electronics':
                return '#007bff'; // Blue
            case 'Fashion':
                return '#e83e8c'; // Pink
            case 'Home & Garden':
                return '#28a745'; // Green
            case 'Health & Beauty':
                return '#fd7e14'; // Orange
            default:
                return '#6c757d'; // Gray
        }
    }

    /**
     * Tạo product type từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo product type từ tên
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
     * Tạo nhiều product types từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $types = [];

        foreach ($data as $typeData) {
            $types[] = self::createFromArray($typeData);
        }

        return $types;
    }

    /**
     * Lọc types theo trạng thái
     */
    public static function filterByStatus(array $types, string $status): array
    {
        return array_filter($types, function (ProductType $type) use ($status) {
            return $type->getStatus() === $status;
        });
    }

    /**
     * Lọc types theo category
     */
    public static function filterByCategory(array $types, string $category): array
    {
        return array_filter($types, function (ProductType $type) use ($category) {
            return $type->getCategory() === $category;
        });
    }

    /**
     * Lọc types theo main category
     */
    public static function filterByMainCategory(array $types, string $mainCategory): array
    {
        return array_filter($types, function (ProductType $type) use ($mainCategory) {
            return $type->getMainCategory() === $mainCategory;
        });
    }

    /**
     * Lọc types điện tử
     */
    public static function filterElectronics(array $types): array
    {
        return array_filter($types, function (ProductType $type) {
            return $type->isElectronicsType();
        });
    }

    /**
     * Lọc types thời trang
     */
    public static function filterFashion(array $types): array
    {
        return array_filter($types, function (ProductType $type) {
            return $type->isFashionType();
        });
    }

    /**
     * Lọc types gia dụng
     */
    public static function filterHomeAppliance(array $types): array
    {
        return array_filter($types, function (ProductType $type) {
            return $type->isHomeApplianceType();
        });
    }

    /**
     * Lọc types sức khỏe
     */
    public static function filterHealth(array $types): array
    {
        return array_filter($types, function (ProductType $type) {
            return $type->isHealthType();
        });
    }

    /**
     * Sắp xếp types theo tên
     */
    public static function sortByName(array $types, bool $ascending = true): array
    {
        usort($types, function (ProductType $a, ProductType $b) use ($ascending) {
            $nameA = $a->getName() ?? '';
            $nameB = $b->getName() ?? '';

            if ($ascending) {
                return strcmp($nameA, $nameB);
            }

            return strcmp($nameB, $nameA);
        });

        return $types;
    }

    /**
     * Sắp xếp types theo code
     */
    public static function sortByCode(array $types, bool $ascending = true): array
    {
        usort($types, function (ProductType $a, ProductType $b) use ($ascending) {
            $codeA = $a->getCode() ?? '';
            $codeB = $b->getCode() ?? '';

            if ($ascending) {
                return strcmp($codeA, $codeB);
            }

            return strcmp($codeB, $codeA);
        });

        return $types;
    }

    /**
     * Tìm types theo tên (partial match)
     */
    public static function searchByName(array $types, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($types, function (ProductType $type) use ($searchTerm) {
            $name = strtolower($type->getName() ?? '');
            return strpos($name, $searchTerm) !== false;
        });
    }

    /**
     * Tìm types theo code (partial match)
     */
    public static function searchByCode(array $types, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($types, function (ProductType $type) use ($searchTerm) {
            $code = strtolower($type->getCode() ?? '');
            return strpos($code, $searchTerm) !== false;
        });
    }

    /**
     * Tìm types theo category (partial match)
     */
    public static function searchByCategory(array $types, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($types, function (ProductType $type) use ($searchTerm) {
            $category = strtolower($type->getCategory() ?? '');
            return strpos($category, $searchTerm) !== false;
        });
    }

    /**
     * Lấy danh sách categories unique
     */
    public static function getUniqueCategories(array $types): array
    {
        $categories = [];

        foreach ($types as $type) {
            $category = $type->getCategory();
            if ($category && !in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        sort($categories);
        return $categories;
    }

    /**
     * Lấy danh sách main categories unique
     */
    public static function getUniqueMainCategories(array $types): array
    {
        $mainCategories = [];

        foreach ($types as $type) {
            $mainCategory = $type->getMainCategory();
            if (!in_array($mainCategory, $mainCategories)) {
                $mainCategories[] = $mainCategory;
            }
        }

        sort($mainCategories);
        return $mainCategories;
    }

    /**
     * Đếm số types theo category
     */
    public static function countByCategory(array $types): array
    {
        $counts = [];

        foreach ($types as $type) {
            $category = $type->getCategory() ?: 'Unknown';

            if (!isset($counts[$category])) {
                $counts[$category] = 0;
            }

            $counts[$category]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Đếm số types theo main category
     */
    public static function countByMainCategory(array $types): array
    {
        $counts = [];

        foreach ($types as $type) {
            $mainCategory = $type->getMainCategory();

            if (!isset($counts[$mainCategory])) {
                $counts[$mainCategory] = 0;
            }

            $counts[$mainCategory]++;
        }

        arsort($counts);
        return $counts;
    }
}
