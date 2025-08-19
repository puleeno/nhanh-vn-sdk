<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Import Type entity
 */
class ProductImportType extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID kiểu nhập kho không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên kiểu nhập kho không được để trống');
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

    public function getStatus(): ?string
    {
        return $this->getAttribute('status');
    }

    public function getType(): ?string
    {
        return $this->getAttribute('type');
    }

    public function getCategory(): ?string
    {
        return $this->getAttribute('category');
    }

    public function getRequireSupplier(): bool
    {
        return (bool) $this->getAttribute('requireSupplier', false);
    }

    public function getRequireInvoice(): bool
    {
        return (bool) $this->getAttribute('requireInvoice', false);
    }

    public function getRequireApproval(): bool
    {
        return (bool) $this->getAttribute('requireApproval', false);
    }

    public function getAutoGenerateCode(): bool
    {
        return (bool) $this->getAttribute('autoGenerateCode', false);
    }

    public function getDefaultStatus(): ?string
    {
        return $this->getAttribute('defaultStatus');
    }

    public function getSortOrder(): ?int
    {
        return $this->getAttribute('sortOrder');
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

    public function hasType(): bool
    {
        return !empty($this->getType());
    }

    public function hasCategory(): bool
    {
        return !empty($this->getCategory());
    }

    public function hasSortOrder(): bool
    {
        return $this->getSortOrder() !== null;
    }

    public function getDisplayName(): string
    {
        if ($this->hasCode()) {
            return "{$this->getCode()} - {$this->getName()}";
        }

        return $this->getName() ?: '';
    }

    public function getFormattedDescription(): string
    {
        $description = $this->getDescription();
        if (!$description) {
            return 'N/A';
        }

        return $description;
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

    public function getTypeText(): string
    {
        return $this->getType() ?: 'Unknown';
    }

    public function getCategoryText(): string
    {
        return $this->getCategory() ?: 'Unknown';
    }

    public function getImportTypeSummary(): string
    {
        $summary = [];

        $summary[] = $this->getName();

        if ($this->hasCategory()) {
            $summary[] = "Danh mục: {$this->getCategory()}";
        }

        if ($this->hasDescription()) {
            $summary[] = "Mô tả: {$this->getFormattedDescription()}";
        }

        return implode(' | ', array_filter($summary));
    }

    /**
     * Kiểm tra xem import type có phải là nhập từ nhà cung cấp không
     */
    public function isSupplierImport(): bool
    {
        $type = strtolower($this->getType() ?? '');
        $name = strtolower($this->getName() ?? '');

        $supplierKeywords = ['nhà cung cấp', 'supplier', 'vendor', 'nhập hàng', 'purchase'];

        foreach ($supplierKeywords as $keyword) {
            if (strpos($type, $keyword) !== false || strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem import type có phải là nhập từ kho khác không
     */
    public function isTransferImport(): bool
    {
        $type = strtolower($this->getType() ?? '');
        $name = strtolower($this->getName() ?? '');

        $transferKeywords = ['chuyển kho', 'transfer', 'di chuyển', 'move', 'nhập chuyển'];

        foreach ($transferKeywords as $keyword) {
            if (strpos($type, $keyword) !== false || strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem import type có phải là nhập từ khách hàng không
     */
    public function isCustomerImport(): bool
    {
        $type = strtolower($this->getType() ?? '');
        $name = strtolower($this->getName() ?? '');

        $customerKeywords = ['khách hàng', 'customer', 'trả hàng', 'return', 'nhập trả'];

        foreach ($customerKeywords as $keyword) {
            if (strpos($type, $keyword) !== false || strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem import type có phải là nhập từ sản xuất không
     */
    public function isProductionImport(): bool
    {
        $type = strtolower($this->getType() ?? '');
        $name = strtolower($this->getName() ?? '');

        $productionKeywords = ['sản xuất', 'production', 'manufacturing', 'nhập sx', 'nhập sản xuất'];

        foreach ($productionKeywords as $keyword) {
            if (strpos($type, $keyword) !== false || strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra xem import type có phải là nhập từ kiểm kho không
     */
    public function isInventoryImport(): bool
    {
        $type = strtolower($this->getType() ?? '');
        $name = strtolower($this->getName() ?? '');

        $inventoryKeywords = ['kiểm kho', 'inventory', 'check', 'nhập kiểm', 'nhập bù'];

        foreach ($inventoryKeywords as $keyword) {
            if (strpos($type, $keyword) !== false || strpos($name, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Lấy category chính của import type
     */
    public function getMainCategory(): string
    {
        if ($this->isSupplierImport()) {
            return 'Supplier';
        }

        if ($this->isTransferImport()) {
            return 'Transfer';
        }

        if ($this->isCustomerImport()) {
            return 'Customer';
        }

        if ($this->isProductionImport()) {
            return 'Production';
        }

        if ($this->isInventoryImport()) {
            return 'Inventory';
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
            case 'Supplier':
                return '#28a745'; // Green
            case 'Transfer':
                return '#007bff'; // Blue
            case 'Customer':
                return '#fd7e14'; // Orange
            case 'Production':
                return '#6f42c1'; // Purple
            case 'Inventory':
                return '#20c997'; // Teal
            default:
                return '#6c757d'; // Gray
        }
    }

    /**
     * Kiểm tra xem import type có yêu cầu nhà cung cấp không
     */
    public function requiresSupplier(): bool
    {
        return $this->getRequireSupplier();
    }

    /**
     * Kiểm tra xem import type có yêu cầu hóa đơn không
     */
    public function requiresInvoice(): bool
    {
        return $this->getRequireInvoice();
    }

    /**
     * Kiểm tra xem import type có yêu cầu phê duyệt không
     */
    public function requiresApproval(): bool
    {
        return $this->getRequireApproval();
    }

    /**
     * Kiểm tra xem import type có tự động tạo mã không
     */
    public function autoGeneratesCode(): bool
    {
        return $this->getAutoGenerateCode();
    }

    /**
     * Lấy trạng thái mặc định
     */
    public function getDefaultStatusText(): string
    {
        $status = $this->getDefaultStatus();
        if (!$status) {
            return 'N/A';
        }

        return $status;
    }

    /**
     * Lấy thông tin yêu cầu đầy đủ
     */
    public function getFullRequirements(): array
    {
        return [
            'supplier' => $this->requiresSupplier(),
            'invoice' => $this->requiresInvoice(),
            'approval' => $this->requiresApproval(),
            'autoCode' => $this->autoGeneratesCode(),
            'defaultStatus' => $this->getDefaultStatusText()
        ];
    }

    /**
     * Lấy thông tin category đầy đủ
     */
    public function getFullCategoryInfo(): array
    {
        return [
            'category' => $this->getCategory(),
            'mainCategory' => $this->getMainCategory(),
            'categoryColor' => $this->getCategoryColor(),
            'isSupplierImport' => $this->isSupplierImport(),
            'isTransferImport' => $this->isTransferImport(),
            'isCustomerImport' => $this->isCustomerImport(),
            'isProductionImport' => $this->isProductionImport(),
            'isInventoryImport' => $this->isInventoryImport()
        ];
    }

    /**
     * Tạo import type từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo import type từ tên
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
     * Tạo nhiều import types từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $importTypes = [];

        foreach ($data as $importTypeData) {
            $importTypes[] = self::createFromArray($importTypeData);
        }

        return $importTypes;
    }

    /**
     * Lọc import types theo trạng thái
     */
    public static function filterByStatus(array $importTypes, string $status): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) use ($status) {
            return $importType->getStatus() === $status;
        });
    }

    /**
     * Lọc import types theo type
     */
    public static function filterByType(array $importTypes, string $type): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) use ($type) {
            return $importType->getType() === $type;
        });
    }

    /**
     * Lọc import types theo category
     */
    public static function filterByCategory(array $importTypes, string $category): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) use ($category) {
            return $importType->getCategory() === $category;
        });
    }

    /**
     * Lọc import types theo main category
     */
    public static function filterByMainCategory(array $importTypes, string $mainCategory): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) use ($mainCategory) {
            return $importType->getMainCategory() === $mainCategory;
        });
    }

    /**
     * Lọc import types từ nhà cung cấp
     */
    public static function filterSupplierImports(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->isSupplierImport();
        });
    }

    /**
     * Lọc import types chuyển kho
     */
    public static function filterTransferImports(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->isTransferImport();
        });
    }

    /**
     * Lọc import types từ khách hàng
     */
    public static function filterCustomerImports(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->isCustomerImport();
        });
    }

    /**
     * Lọc import types từ sản xuất
     */
    public static function filterProductionImports(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->isProductionImport();
        });
    }

    /**
     * Lọc import types từ kiểm kho
     */
    public static function filterInventoryImports(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->isInventoryImport();
        });
    }

    /**
     * Lọc import types yêu cầu nhà cung cấp
     */
    public static function filterRequireSupplier(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->requiresSupplier();
        });
    }

    /**
     * Lọc import types yêu cầu hóa đơn
     */
    public static function filterRequireInvoice(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->requiresInvoice();
        });
    }

    /**
     * Lọc import types yêu cầu phê duyệt
     */
    public static function filterRequireApproval(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->requiresApproval();
        });
    }

    /**
     * Lọc import types tự động tạo mã
     */
    public static function filterAutoGenerateCode(array $importTypes): array
    {
        return array_filter($importTypes, function (ProductImportType $importType) {
            return $importType->autoGeneratesCode();
        });
    }

    /**
     * Sắp xếp import types theo tên
     */
    public static function sortByName(array $importTypes, bool $ascending = true): array
    {
        usort($importTypes, function (ProductImportType $a, ProductImportType $b) use ($ascending) {
            $nameA = $a->getName() ?? '';
            $nameB = $b->getName() ?? '';

            if ($ascending) {
                return strcmp($nameA, $nameB);
            }

            return strcmp($nameB, $nameA);
        });

        return $importTypes;
    }

    /**
     * Sắp xếp import types theo code
     */
    public static function sortByCode(array $importTypes, bool $ascending = true): array
    {
        usort($importTypes, function (ProductImportType $a, ProductImportType $b) use ($ascending) {
            $codeA = $a->getCode() ?? '';
            $codeB = $b->getCode() ?? '';

            if ($ascending) {
                return strcmp($codeA, $codeB);
            }

            return strcmp($codeB, $codeA);
        });

        return $importTypes;
    }

    /**
     * Sắp xếp import types theo sort order
     */
    public static function sortByOrder(array $importTypes, bool $ascending = true): array
    {
        usort($importTypes, function (ProductImportType $a, ProductImportType $b) use ($ascending) {
            $orderA = $a->getSortOrder() ?? 0;
            $orderB = $b->getSortOrder() ?? 0;

            if ($ascending) {
                return $orderA <=> $orderB;
            }

            return $orderB <=> $orderA;
        });

        return $importTypes;
    }

    /**
     * Tìm import types theo tên (partial match)
     */
    public static function searchByName(array $importTypes, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($importTypes, function (ProductImportType $importType) use ($searchTerm) {
            $name = strtolower($importType->getName() ?? '');
            return strpos($name, $searchTerm) !== false;
        });
    }

    /**
     * Tìm import types theo code (partial match)
     */
    public static function searchByCode(array $importTypes, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($importTypes, function (ProductImportType $importType) use ($searchTerm) {
            $code = strtolower($importType->getCode() ?? '');
            return strpos($code, $searchTerm) !== false;
        });
    }

    /**
     * Tìm import types theo category (partial match)
     */
    public static function searchByCategory(array $importTypes, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($importTypes, function (ProductImportType $importType) use ($searchTerm) {
            $category = strtolower($importType->getCategory() ?? '');
            return strpos($category, $searchTerm) !== false;
        });
    }

    /**
     * Lấy danh sách categories unique
     */
    public static function getUniqueCategories(array $importTypes): array
    {
        $categories = [];

        foreach ($importTypes as $importType) {
            $category = $importType->getCategory();
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
    public static function getUniqueMainCategories(array $importTypes): array
    {
        $mainCategories = [];

        foreach ($importTypes as $importType) {
            $mainCategory = $importType->getMainCategory();
            if (!in_array($mainCategory, $mainCategories)) {
                $mainCategories[] = $mainCategory;
            }
        }

        sort($mainCategories);
        return $mainCategories;
    }

    /**
     * Đếm số import types theo category
     */
    public static function countByCategory(array $importTypes): array
    {
        $counts = [];

        foreach ($importTypes as $importType) {
            $category = $importType->getCategory() ?: 'Unknown';

            if (!isset($counts[$category])) {
                $counts[$category] = 0;
            }

            $counts[$category]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Đếm số import types theo main category
     */
    public static function countByMainCategory(array $importTypes): array
    {
        $counts = [];

        foreach ($importTypes as $importType) {
            $mainCategory = $importType->getMainCategory();

            if (!isset($counts[$mainCategory])) {
                $counts[$mainCategory] = 0;
            }

            $counts[$mainCategory]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Đếm số import types theo status
     */
    public static function countByStatus(array $importTypes): array
    {
        $counts = [];

        foreach ($importTypes as $importType) {
            $status = $importType->getStatus() ?: 'Unknown';

            if (!isset($counts[$status])) {
                $counts[$status] = 0;
            }

            $counts[$status]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Lấy thống kê import types
     */
    public static function getStatistics(array $importTypes): array
    {
        $stats = [
            'total' => count($importTypes),
            'active' => count(self::filterByStatus($importTypes, 'Active')),
            'inactive' => count(self::filterByStatus($importTypes, 'Inactive')),
            'supplier' => count(self::filterSupplierImports($importTypes)),
            'transfer' => count(self::filterTransferImports($importTypes)),
            'customer' => count(self::filterCustomerImports($importTypes)),
            'production' => count(self::filterProductionImports($importTypes)),
            'inventory' => count(self::filterInventoryImports($importTypes)),
            'requireSupplier' => count(self::filterRequireSupplier($importTypes)),
            'requireInvoice' => count(self::filterRequireInvoice($importTypes)),
            'requireApproval' => count(self::filterRequireApproval($importTypes)),
            'autoGenerateCode' => count(self::filterAutoGenerateCode($importTypes)),
            'withCode' => count(array_filter($importTypes, function (ProductImportType $it) {
                return $it->hasCode();
            })),
            'withDescription' => count(array_filter($importTypes, function (ProductImportType $it) {
                return $it->hasDescription();
            })),
            'withSortOrder' => count(array_filter($importTypes, function (ProductImportType $it) {
                return $it->hasSortOrder();
            }))
        ];

        return $stats;
    }
}
