<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Illuminate\Support\Collection;
use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Collection entity
 */
class ProductCollection extends AbstractEntity
{
    protected function validate(): void
    {
        // Không cần validation cho collection
    }

    // Basic getters
    public function getId(): mixed
    {
        return $this->getAttribute('id', 'collection');
    }

    public function getProducts(): Collection
    {
        $products = $this->getAttribute('products', []);

        if (is_array($products)) {
            $productEntities = [];
            foreach ($products as $productData) {
                if (is_array($productData)) {
                    $productEntities[] = new Product($productData);
                } elseif ($productData instanceof Product) {
                    $productEntities[] = $productData;
                }
            }
            return new Collection($productEntities);
        }

        if ($products instanceof Collection) {
            return $products;
        }

        return new Collection();
    }

    public function getTotalPages(): ?int
    {
        return $this->getAttribute('totalPages');
    }

    public function getCurrentPage(): ?int
    {
        return $this->getAttribute('currentPage', 1);
    }

    public function getPerPage(): ?int
    {
        return $this->getAttribute('perPage', 100);
    }

    public function getTotal(): ?int
    {
        return $this->getAttribute('total');
    }

    public function getFrom(): ?int
    {
        return $this->getAttribute('from');
    }

    public function getTo(): ?int
    {
        return $this->getAttribute('to');
    }

    public function getLastPage(): ?int
    {
        return $this->getAttribute('lastPage');
    }

    public function getHasMorePages(): bool
    {
        return $this->getAttribute('hasMorePages', false);
    }

    public function getFilters(): array
    {
        return $this->getAttribute('filters', []);
    }

    public function getSort(): array
    {
        return $this->getAttribute('sort', []);
    }

    public function getSearchTerm(): ?string
    {
        return $this->getAttribute('searchTerm');
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
    public function count(): int
    {
        return $this->getProducts()->count();
    }

    public function isEmpty(): bool
    {
        return $this->getProducts()->isEmpty();
    }

    public function isNotEmpty(): bool
    {
        return $this->getProducts()->isNotEmpty();
    }

    public function hasProducts(): bool
    {
        return $this->isNotEmpty();
    }

    public function first(): ?Product
    {
        return $this->getProducts()->first();
    }

    public function last(): ?Product
    {
        return $this->getProducts()->last();
    }

    public function get(int $index): ?Product
    {
        return $this->getProducts()->get($index);
    }

    public function getById(int $id): ?Product
    {
        return $this->getProducts()->firstWhere('id', $id);
    }

    public function getByCode(string $code): ?Product
    {
        return $this->getProducts()->firstWhere('code', $code);
    }

    public function getByName(string $name): ?Product
    {
        return $this->getProducts()->firstWhere('name', $name);
    }

    public function getByBarcode(string $barcode): ?Product
    {
        return $this->getProducts()->firstWhere('barcode', $barcode);
    }

    public function getByCategory(int $categoryId): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($categoryId) {
            return $product->getCategoryId() === $categoryId;
        });
    }

    public function getByBrand(int $brandId): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($brandId) {
            return $product->getBrandId() === $brandId;
        });
    }

    public function getByStatus(string $status): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($status) {
            return $product->getStatus() === $status;
        });
    }

    public function getActive(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isActive();
        });
    }

    public function getInactive(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isInactive();
        });
    }

    public function getOutOfStock(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isOutOfStock();
        });
    }

    public function getInStock(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isInStock();
        });
    }

    public function getLowStock(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isLowStock();
        });
    }

    public function getHot(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isHot();
        });
    }

    public function getNew(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isNew();
        });
    }

    public function getHome(): Collection
    {
        return $this->getProducts()->filter(function (Product $product) {
            return $product->isHome();
        });
    }

    public function getByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($minPrice, $maxPrice) {
            $price = $product->getPrice();
            return $price >= $minPrice && $price <= $maxPrice;
        });
    }

    public function getByPriceGreaterThan(float $price): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($price) {
            return $product->getPrice() > $price;
        });
    }

    public function getByPriceLessThan(float $price): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($price) {
            return $product->getPrice() < $price;
        });
    }

    public function getByInventoryGreaterThan(int $quantity): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($quantity) {
            return $product->getAvailableStock() > $quantity;
        });
    }

    public function getByInventoryLessThan(int $quantity): Collection
    {
        return $this->getProducts()->filter(function (Product $product) use ($quantity) {
            return $product->getAvailableStock() < $quantity;
        });
    }

    public function search(string $term): Collection
    {
        $term = strtolower($term);

        return $this->getProducts()->filter(function (Product $product) use ($term) {
            $name = strtolower($product->getName() ?? '');
            $code = strtolower($product->getCode() ?? '');
            $barcode = strtolower($product->getBarcode() ?? '');
            $description = strtolower($product->getDescription() ?? '');

            return strpos($name, $term) !== false ||
                   strpos($code, $term) !== false ||
                   strpos($barcode, $term) !== false ||
                   strpos($description, $term) !== false;
        });
    }

    public function sortBy(string $field, bool $ascending = true): Collection
    {
        $products = $this->getProducts();

        switch ($field) {
            case 'name':
                return $ascending ? $products->sortBy('name') : $products->sortByDesc('name');
            case 'code':
                return $ascending ? $products->sortBy('code') : $products->sortByDesc('code');
            case 'price':
                return $ascending ? $products->sortBy('price') : $products->sortByDesc('price');
            case 'importPrice':
                return $ascending ? $products->sortBy('importPrice') : $products->sortByDesc('importPrice');
            case 'wholesalePrice':
                return $ascending ? $products->sortBy('wholesalePrice') : $products->sortByDesc('wholesalePrice');
            case 'availableStock':
                return $ascending ? $products->sortBy('availableStock') : $products->sortByDesc('availableStock');
            case 'createdAt':
                return $ascending ? $products->sortBy('createdAt') : $products->sortByDesc('createdAt');
            case 'updatedAt':
                return $ascending ? $products->sortBy('updatedAt') : $products->sortByDesc('updatedAt');
            default:
                return $products;
        }
    }

    public function sortByName(bool $ascending = true): Collection
    {
        return $this->sortBy('name', $ascending);
    }

    public function sortByPrice(bool $ascending = true): Collection
    {
        return $this->sortBy('price', $ascending);
    }

    public function sortByStock(bool $ascending = true): Collection
    {
        return $this->sortBy('availableStock', $ascending);
    }

    public function sortByDate(bool $ascending = true): Collection
    {
        return $this->sortBy('createdAt', $ascending);
    }

    public function paginate(int $page = 1, int $perPage = 20): array
    {
        $products = $this->getProducts();
        $total = $products->count();
        $lastPage = ceil($total / $perPage);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        $items = $products->slice($offset, $perPage);

        return [
            'data' => $items->values(),
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
            'has_more_pages' => $page < $lastPage
        ];
    }

    public function chunk(int $size): Collection
    {
        return $this->getProducts()->chunk($size);
    }

    public function take(int $limit): Collection
    {
        return $this->getProducts()->take($limit);
    }

    public function skip(int $count): Collection
    {
        return $this->getProducts()->skip($count);
    }

    public function limit(int $limit): Collection
    {
        return $this->getProducts()->limit($limit);
    }

    public function offset(int $offset): Collection
    {
        return $this->getProducts()->offset($offset);
    }

    public function random(int $number = 1): Collection
    {
        return $this->getProducts()->random($number);
    }

    public function shuffle(): Collection
    {
        return $this->getProducts()->shuffle();
    }

    public function reverse(): Collection
    {
        return $this->getProducts()->reverse();
    }

    public function unique(string $key = null): Collection
    {
        return $this->getProducts()->unique($key);
    }

    public function pluck(string $value, string $key = null): Collection
    {
        return $this->getProducts()->pluck($value, $key);
    }

    public function map(callable $callback): Collection
    {
        return $this->getProducts()->map($callback);
    }

    public function filter(callable $callback = null): Collection
    {
        return $this->getProducts()->filter($callback);
    }

    public function reject(callable $callback): Collection
    {
        return $this->getProducts()->reject($callback);
    }

    public function where(string $key, $value): Collection
    {
        return $this->getProducts()->where($key, $value);
    }

    public function whereIn(string $key, array $values): Collection
    {
        return $this->getProducts()->whereIn($key, $values);
    }

    public function whereNotIn(string $key, array $values): Collection
    {
        return $this->getProducts()->whereNotIn($key, $values);
    }

    public function whereBetween(string $key, array $values): Collection
    {
        return $this->getProducts()->whereBetween($key, $values);
    }

    public function whereNotBetween(string $key, array $values): Collection
    {
        return $this->getProducts()->whereNotBetween($key, $values);
    }

    public function whereNull(string $key): Collection
    {
        return $this->getProducts()->whereNull($key);
    }

    public function whereNotNull(string $key): Collection
    {
        return $this->getProducts()->whereNotNull($key);
    }

    public function groupBy(string $key): Collection
    {
        return $this->getProducts()->groupBy($key);
    }

    public function keyBy(string $key): Collection
    {
        return $this->getProducts()->keyBy($key);
    }

    public function countBy(string $key): Collection
    {
        return $this->getProducts()->countBy($key);
    }

    public function sum(string $key = null): float
    {
        return $this->getProducts()->sum($key);
    }

    public function avg(string $key = null): float
    {
        return $this->getProducts()->avg($key);
    }

    public function min(string $key = null): mixed
    {
        return $this->getProducts()->min($key);
    }

    public function max(string $key = null): mixed
    {
        return $this->getProducts()->max($key);
    }

    public function median(string $key = null): mixed
    {
        return $this->getProducts()->median($key);
    }

    public function mode(string $key = null): mixed
    {
        return $this->getProducts()->mode($key);
    }

    public function getTotalValue(): float
    {
        return $this->getProducts()->sum(function (Product $product) {
            return $product->getPrice() * $product->getAvailableStock();
        });
    }

    public function getAveragePrice(): float
    {
        return $this->getProducts()->avg('price');
    }

    public function getMinPrice(): float
    {
        return $this->getProducts()->min('price');
    }

    public function getMaxPrice(): float
    {
        return $this->getProducts()->max('price');
    }

    public function getTotalStock(): int
    {
        return $this->getProducts()->sum('availableStock');
    }

    public function getAverageStock(): float
    {
        return $this->getProducts()->avg('availableStock');
    }

    public function getMinStock(): int
    {
        return $this->getProducts()->min('availableStock');
    }

    public function getMaxStock(): int
    {
        return $this->getProducts()->max('availableStock');
    }

    public function getStatistics(): array
    {
        $products = $this->getProducts();

        return [
            'total' => $products->count(),
            'active' => $products->filter(function (Product $p) {
                return $p->isActive();
            })->count(),
            'inactive' => $products->filter(function (Product $p) {
                return $p->isInactive();
            })->count(),
            'outOfStock' => $products->filter(function (Product $p) {
                return $p->isOutOfStock();
            })->count(),
            'inStock' => $products->filter(function (Product $p) {
                return $p->isInStock();
            })->count(),
            'lowStock' => $products->filter(function (Product $p) {
                return $p->isLowStock();
            })->count(),
            'hot' => $products->filter(function (Product $p) {
                return $p->isHot();
            })->count(),
            'new' => $products->filter(function (Product $p) {
                return $p->isNew();
            })->count(),
            'home' => $products->filter(function (Product $p) {
                return $p->isHome();
            })->count(),
            'totalValue' => $this->getTotalValue(),
            'averagePrice' => $this->getAveragePrice(),
            'minPrice' => $this->getMinPrice(),
            'maxPrice' => $this->getMaxPrice(),
            'totalStock' => $this->getTotalStock(),
            'averageStock' => $this->getAverageStock(),
            'minStock' => $this->getMinStock(),
            'maxStock' => $this->getMaxStock()
        ];
    }

    public function getPaginationInfo(): array
    {
        return [
            'totalPages' => $this->getTotalPages(),
            'currentPage' => $this->getCurrentPage(),
            'perPage' => $this->getPerPage(),
            'total' => $this->getTotal(),
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'lastPage' => $this->getLastPage(),
            'hasMorePages' => $this->getHasMorePages()
        ];
    }

    public function getFiltersInfo(): array
    {
        return [
            'filters' => $this->getFilters(),
            'sort' => $this->getSort(),
            'searchTerm' => $this->getSearchTerm()
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'products' => $this->getProducts()->toArray(),
            'pagination' => $this->getPaginationInfo(),
            'filters' => $this->getFiltersInfo(),
            'statistics' => $this->getStatistics(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function isValid(): bool
    {
        return true; // Collection luôn valid
    }

    public function getErrors(): array
    {
        return []; // Collection không có errors
    }

    /**
     * Tạo collection từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo collection từ array products
     */
    public static function createFromProducts(array $products, array $pagination = []): self
    {
        $data = [
            'products' => $products,
            'pagination' => $pagination
        ];

        return new self($data);
    }

    /**
     * Tạo collection từ Collection object
     */
    public static function createFromCollection(Collection $products, array $pagination = []): self
    {
        $data = [
            'products' => $products,
            'pagination' => $pagination
        ];

        return new self($data);
    }

    /**
     * Tạo collection rỗng
     */
    public static function createEmpty(): self
    {
        return new self(['products' => []]);
    }
}
