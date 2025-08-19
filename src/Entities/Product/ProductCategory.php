<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Category entity
 */
class ProductCategory extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID danh mục không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên danh mục không được để trống');
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

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getOrder(): ?int
    {
        return $this->getAttribute('order');
    }

    public function getImage(): ?string
    {
        return $this->getAttribute('image');
    }

    public function getContent(): ?string
    {
        return $this->getAttribute('content');
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description') ?: $this->getAttribute('content');
    }

    public function getStatus(): ?int
    {
        return $this->getAttribute('status');
    }

    public function getChildren(): array
    {
        return $this->getAttribute('childs', []);
    }

    // Business logic methods
    public function isActive(): bool
    {
        return $this->getStatus() === 1;
    }

    public function isInactive(): bool
    {
        return $this->getStatus() === 2;
    }

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

    public function getProductCount(): ?int
    {
        return $this->getAttribute('productCount') ?: $this->getAttribute('product_count') ?: 0;
    }

    public function getSlug(): ?string
    {
        return $this->getAttribute('slug') ?: $this->getAttribute('url') ?: $this->generateSlug();
    }

    public function getMetaTitle(): ?string
    {
        return $this->getAttribute('metaTitle') ?: $this->getAttribute('meta_title') ?: $this->getName();
    }

    public function getMetaDescription(): ?string
    {
        return $this->getAttribute('metaDescription') ?: $this->getAttribute('meta_description') ?: $this->getDescription();
    }

    public function getMetaKeywords(): ?string
    {
        return $this->getAttribute('metaKeywords') ?: $this->getAttribute('meta_keywords') ?: '';
    }

    /**
     * Tạo slug từ tên category
     */
    private function generateSlug(): string
    {
        $name = $this->getName() ?: '';
        if (empty($name)) {
            return '';
        }

        // Chuyển về lowercase và thay thế dấu cách bằng dấu gạch ngang
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
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

    public function hasImage(): bool
    {
        return !empty($this->getImage());
    }

    public function hasContent(): bool
    {
        return !empty($this->getContent());
    }

    public function getShortContent(int $length = 100): string
    {
        $content = $this->getContent() ?: '';
        if (strlen($content) <= $length) {
            return $content;
        }

        return substr($content, 0, $length) . '...';
    }

    /**
     * Tạo category tree từ array data
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
                $tree[] = self::buildCategoryNode($category, $lookup);
            }
        }

        return $tree;
    }

    /**
     * Build category node với children
     */
    private static function buildCategoryNode(array $category, array $lookup): array
    {
        $node = $category;
        $children = [];

        // Tìm children
        foreach ($lookup as $id => $cat) {
            if ($cat['parentId'] === $category['id']) {
                $children[] = self::buildCategoryNode($cat, $lookup);
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
     * Tìm category theo ID trong tree
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
}
