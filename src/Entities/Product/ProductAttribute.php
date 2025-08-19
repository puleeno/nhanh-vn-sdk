<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Attribute entity
 */
class ProductAttribute extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID thuộc tính không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên thuộc tính không được để trống');
        }
    }

    // Basic getters
    public function getId(): mixed
    {
        return $this->getAttribute('id');
    }

    public function getAttributeName(): ?string
    {
        return $this->getAttribute('attributeName');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getOrder(): ?int
    {
        return $this->getAttribute('order');
    }

    public function getParent(): ?array
    {
        return $this->getAttribute('parent');
    }

    // Business logic methods
    public function hasParent(): bool
    {
        return !empty($this->getParent());
    }

    public function isParent(): bool
    {
        return !$this->hasParent();
    }

    public function getParentId(): ?int
    {
        $parent = $this->getParent();
        return $parent['id'] ?? null;
    }

    public function getParentName(): ?string
    {
        $parent = $this->getParent();
        return $parent['name'] ?? null;
    }

    public function getFullName(): string
    {
        if ($this->hasParent()) {
            return $this->getParentName() . ' - ' . $this->getName();
        }

        return $this->getName() ?: '';
    }

    public function getDisplayName(): string
    {
        return $this->getAttributeName() ?: $this->getName() ?: '';
    }

    public function isOrdered(): bool
    {
        return $this->getOrder() !== null;
    }

    public function getSortOrder(): int
    {
        return $this->getOrder() ?? 0;
    }

    /**
     * Tạo attribute tree từ array data
     */
    public static function createTree(array $attributes): array
    {
        $tree = [];
        $lookup = [];

        // Tạo lookup table
        foreach ($attributes as $attribute) {
            $lookup[$attribute['id']] = $attribute;
        }

        // Build tree
        foreach ($attributes as $attribute) {
            if (!isset($attribute['parent']) || empty($attribute['parent'])) {
                $tree[] = self::buildAttributeNode($attribute, $lookup);
            }
        }

        return $tree;
    }

    /**
     * Build attribute node với children
     */
    private static function buildAttributeNode(array $attribute, array $lookup): array
    {
        $node = $attribute;
        $children = [];

        // Tìm children
        foreach ($lookup as $id => $attr) {
            if (isset($attr['parent']['id']) && $attr['parent']['id'] === $attribute['id']) {
                $children[] = self::buildAttributeNode($attr, $lookup);
            }
        }

        if (!empty($children)) {
            $node['children'] = $children;
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

            if (isset($node['children'])) {
                $flat = array_merge($flat, self::flattenTree($node['children']));
            }
        }

        return $flat;
    }

    /**
     * Tìm attribute theo ID trong tree
     */
    public static function findInTree(array $tree, int $id): ?array
    {
        foreach ($tree as $node) {
            if ($node['id'] === $id) {
                return $node;
            }

            if (isset($node['children'])) {
                $found = self::findInTree($node['children'], $id);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Lấy tất cả parent attributes
     */
    public static function getParentAttributes(array $attributes): array
    {
        $parents = [];

        foreach ($attributes as $attribute) {
            if (isset($attribute['parent']) && !empty($attribute['parent'])) {
                $parentId = $attribute['parent']['id'];
                if (!isset($parents[$parentId])) {
                    $parents[$parentId] = $attribute['parent'];
                }
            }
        }

        return array_values($parents);
    }

    /**
     * Lấy tất cả child attributes của một parent
     */
    public static function getChildAttributes(array $attributes, int $parentId): array
    {
        $children = [];

        foreach ($attributes as $attribute) {
            if (isset($attribute['parent']['id']) && $attribute['parent']['id'] === $parentId) {
                $children[] = $attribute;
            }
        }

        return $children;
    }

    /**
     * Sắp xếp attributes theo order
     */
    public static function sortByOrder(array $attributes): array
    {
        usort($attributes, function ($a, $b) {
            $orderA = $a['order'] ?? 0;
            $orderB = $b['order'] ?? 0;

            if ($orderA === $orderB) {
                return strcmp($a['name'] ?? '', $b['name'] ?? '');
            }

            return $orderA <=> $orderB;
        });

        return $attributes;
    }
}
