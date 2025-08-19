<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Internal Category entity
 */
class ProductInternalCategory extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID danh mục nội bộ không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên danh mục nội bộ không được để trống');
        }
    }

    // Basic getters
    public function getId(): mixed
    {
        return $this->getAttribute('id');
    }

    public function getParentId(): ?int
    {
        return $this->getAttribute('parentId');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function getChildren(): array
    {
        return $this->getAttribute('childs', []);
    }

    // Business logic methods
    public function isRoot(): bool
    {
        return $this->getParentId() === 0 || $this->getParentId() === null;
    }

    public function hasParent(): bool
    {
        return !$this->isRoot();
    }

    public function hasChildren(): bool
    {
        return !empty($this->getChildren());
    }

    public function getChildrenCount(): int
    {
        return count($this->getChildren());
    }

    public function getLevel(): int
    {
        if ($this->isRoot()) {
            return 0;
        }

        // Đây là logic đơn giản, trong thực tế có thể cần traverse parent chain
        return 1;
    }

    public function getFullPath(): string
    {
        $path = [$this->getName()];

        // Trong thực tế, cần traverse parent chain để build full path
        // Hiện tại chỉ trả về tên hiện tại

        return implode(' > ', array_filter($path));
    }

    public function hasCode(): bool
    {
        return !empty($this->getCode());
    }

    public function getDisplayName(): string
    {
        if ($this->hasCode()) {
            return "{$this->getCode()} - {$this->getName()}";
        }

        return $this->getName() ?: '';
    }

    /**
     * Tạo internal category tree từ array data
     */
    public static function createTree(array $categories): array
    {
        $tree = [];
        $lookup = [];

        // Tạo lookup table
        foreach ($categories as $category) {
            $lookup[$category['id']] = $category;
        }

        // Build tree
        foreach ($categories as $category) {
            if ($category['parentId'] === 0 || $category['parentId'] === null) {
                $tree[] = self::buildInternalCategoryNode($category, $lookup);
            }
        }

        return $tree;
    }

    /**
     * Build internal category node với children
     */
    private static function buildInternalCategoryNode(array $category, array $lookup): array
    {
        $node = $category;
        $children = [];

        // Tìm children
        foreach ($lookup as $id => $cat) {
            if ($cat['parentId'] === $category['id']) {
                $children[] = self::buildInternalCategoryNode($cat, $lookup);
            }
        }

        if (!empty($children)) {
            $node['childs'] = $children;
        }

        return $node;
    }

    /**
     * Flatten tree thành flat array
     */
    public static function flattenTree(array $tree): array
    {
        $flat = [];

        foreach ($tree as $node) {
            $flat[] = $node;

            if (isset($node['childs'])) {
                $flat = array_merge($flat, self::flattenTree($node['childs']));
            }
        }

        return $flat;
    }

    /**
     * Tìm internal category theo ID trong tree
     */
    public static function findInTree(array $tree, int $id): ?array
    {
        foreach ($tree as $node) {
            if ($node['id'] === $id) {
                return $node;
            }

            if (isset($node['childs'])) {
                $found = self::findInTree($node['childs'], $id);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Lấy tất cả parent categories
     */
    public static function getParentCategories(array $categories): array
    {
        $parents = [];

        foreach ($categories as $category) {
            if (isset($category['parentId']) && $category['parentId'] > 0) {
                $parentId = $category['parentId'];
                if (!isset($parents[$parentId])) {
                    $parents[$parentId] = $category;
                }
            }
        }

        return array_values($parents);
    }

    /**
     * Lấy tất cả child categories của một parent
     */
    public static function getChildCategories(array $categories, int $parentId): array
    {
        $children = [];

        foreach ($categories as $category) {
            if (isset($category['parentId']) && $category['parentId'] === $parentId) {
                $children[] = $category;
            }
        }

        return $children;
    }

    /**
     * Lấy tất cả root categories
     */
    public static function getRootCategories(array $categories): array
    {
        return array_filter($categories, function ($category) {
            return $category['parentId'] === 0 || $category['parentId'] === null;
        });
    }

    /**
     * Lấy tất cả leaf categories (không có children)
     */
    public static function getLeafCategories(array $categories): array
    {
        $leafIds = [];
        $parentIds = [];

        // Lấy tất cả parent IDs
        foreach ($categories as $category) {
            if (isset($category['parentId']) && $category['parentId'] > 0) {
                $parentIds[] = $category['parentId'];
            }
        }

        // Lấy categories không phải là parent
        foreach ($categories as $category) {
            if (!in_array($category['id'], $parentIds)) {
                $leafIds[] = $category;
            }
        }

        return $leafIds;
    }

    /**
     * Tính độ sâu của tree
     */
    public static function getTreeDepth(array $tree): int
    {
        $maxDepth = 0;

        foreach ($tree as $node) {
            $depth = self::calculateNodeDepth($node);
            $maxDepth = max($maxDepth, $depth);
        }

        return $maxDepth;
    }

    /**
     * Tính độ sâu của một node
     */
    private static function calculateNodeDepth(array $node, int $currentDepth = 1): int
    {
        if (!isset($node['childs']) || empty($node['childs'])) {
            return $currentDepth;
        }

        $maxChildDepth = $currentDepth;
        foreach ($node['childs'] as $child) {
            $childDepth = self::calculateNodeDepth($child, $currentDepth + 1);
            $maxChildDepth = max($maxChildDepth, $childDepth);
        }

        return $maxChildDepth;
    }

    /**
     * Sắp xếp categories theo tên
     */
    public static function sortByName(array $categories, bool $ascending = true): array
    {
        usort($categories, function ($a, $b) use ($ascending) {
            $nameA = $a['name'] ?? '';
            $nameB = $b['name'] ?? '';

            if ($ascending) {
                return strcmp($nameA, $nameB);
            }

            return strcmp($nameB, $nameA);
        });

        return $categories;
    }

    /**
     * Sắp xếp categories theo code
     */
    public static function sortByCode(array $categories, bool $ascending = true): array
    {
        usort($categories, function ($a, $b) use ($ascending) {
            $codeA = $a['code'] ?? '';
            $codeB = $b['code'] ?? '';

            if ($ascending) {
                return strcmp($codeA, $codeB);
            }

            return strcmp($codeB, $codeA);
        });

        return $categories;
    }

    /**
     * Tìm categories theo tên (partial match)
     */
    public static function searchByName(array $categories, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($categories, function ($category) use ($searchTerm) {
            $name = strtolower($category['name'] ?? '');
            return strpos($name, $searchTerm) !== false;
        });
    }

    /**
     * Tìm categories theo code (partial match)
     */
    public static function searchByCode(array $categories, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($categories, function ($category) use ($searchTerm) {
            $code = strtolower($category['code'] ?? '');
            return strpos($code, $searchTerm) !== false;
        });
    }

    /**
     * Tạo internal category từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo nhiều internal categories từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $categories = [];

        foreach ($data as $categoryData) {
            $categories[] = self::createFromArray($categoryData);
        }

        return $categories;
    }
}
