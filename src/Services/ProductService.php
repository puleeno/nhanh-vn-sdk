<?php

namespace Puleeno\NhanhVn\Services;

use Illuminate\Support\Collection;
use Puleeno\NhanhVn\Entities\Product\Product;
use Puleeno\NhanhVn\Entities\Product\ProductCollection;
use Puleeno\NhanhVn\Entities\Product\ProductCategory;
use Puleeno\NhanhVn\Entities\Product\ProductBrand;
use Puleeno\NhanhVn\Entities\Product\ProductType;
use Puleeno\NhanhVn\Entities\Product\ProductSupplier;
use Puleeno\NhanhVn\Entities\Product\ProductDepot;
use Puleeno\NhanhVn\Entities\Product\ProductImportType;
use Puleeno\NhanhVn\Entities\Product\ProductAttribute;
use Puleeno\NhanhVn\Entities\Product\ProductUnit;
use Puleeno\NhanhVn\Entities\Product\ProductGift;
use Puleeno\NhanhVn\Entities\Product\ProductImei;
use Puleeno\NhanhVn\Entities\Product\ProductExpiry;
use Puleeno\NhanhVn\Entities\Product\ProductImeiHistory;
use Puleeno\NhanhVn\Entities\Product\ProductImeiSold;
use Puleeno\NhanhVn\Entities\Product\ProductInternalCategory;
use Puleeno\NhanhVn\Entities\Product\ProductWebsiteInfo;
use Puleeno\NhanhVn\Entities\Product\ProductWarranty;
use Puleeno\NhanhVn\Entities\Product\ProductAddRequest;
use Puleeno\NhanhVn\Entities\Product\ProductAddResponse;
use Puleeno\NhanhVn\Entities\Product\ProductExternalImageRequest;
use Puleeno\NhanhVn\Entities\Product\ProductExternalImageResponse;
use Puleeno\NhanhVn\Repositories\ProductRepository;
use Puleeno\NhanhVn\Services\CacheService;

/**
 * Product Service
 */
class ProductService
{
    protected ProductRepository $productRepository;
    protected CacheService $cacheService;

    public function __construct(ProductRepository $productRepository, CacheService $cacheService)
    {
        $this->productRepository = $productRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Tìm kiếm sản phẩm theo nhiều tiêu chí
     */
    public function searchProducts(array $criteria): ProductCollection
    {
        $products = [];

        // Xử lý tìm kiếm theo từng tiêu chí
        if (isset($criteria['keyword'])) {
            $products = $this->searchByKeyword($criteria['keyword']);
        }

        if (isset($criteria['categoryId'])) {
            $products = $this->filterByCategory($products, $criteria['categoryId']);
        }

        if (isset($criteria['brandId'])) {
            $products = $this->filterByBrand($products, $criteria['brandId']);
        }

        if (isset($criteria['minPrice']) || isset($criteria['maxPrice'])) {
            $minPrice = $criteria['minPrice'] ?? 0;
            $maxPrice = $criteria['maxPrice'] ?? PHP_FLOAT_MAX;
            $products = $this->filterByPriceRange($products, $minPrice, $maxPrice);
        }

        if (isset($criteria['status'])) {
            $products = $this->filterByStatus($products, $criteria['status']);
        }

        if (isset($criteria['inStock'])) {
            $products = $criteria['inStock'] ? $this->filterInStock($products) : $this->filterOutOfStock($products);
        }

        // Sắp xếp kết quả
        $sortField = $criteria['sortBy'] ?? 'name';
        $sortOrder = $criteria['sortOrder'] ?? 'asc';
        $products = $this->sortProducts($products, $sortField, $sortOrder === 'asc');

        // Phân trang
        $page = $criteria['page'] ?? 1;
        $perPage = $criteria['perPage'] ?? 20;
        $paginatedProducts = $this->paginateProducts($products, $page, $perPage);

        return $this->productRepository->createProductCollectionFromProducts(
            $paginatedProducts['data'],
            $paginatedProducts
        );
    }

    /**
     * Lấy sản phẩm theo danh mục
     */
    public function getProductsByCategory(int $categoryId, array $options = []): ProductCollection
    {
        $products = $this->getAllProducts();
        $filteredProducts = $this->filterByCategory($products, $categoryId);

        // Áp dụng các tùy chọn bổ sung
        if (isset($options['status'])) {
            $filteredProducts = $this->filterByStatus($filteredProducts, $options['status']);
        }

        if (isset($options['sortBy'])) {
            $sortOrder = $options['sortOrder'] ?? 'asc';
            $filteredProducts = $this->sortProducts($filteredProducts, $options['sortBy'], $sortOrder === 'asc');
        }

        // Phân trang
        $page = $options['page'] ?? 1;
        $perPage = $options['perPage'] ?? 20;
        $paginatedProducts = $this->paginateProducts($filteredProducts, $page, $perPage);

        return $this->productRepository->createProductCollectionFromProducts(
            $paginatedProducts['data'],
            $paginatedProducts
        );
    }

    /**
     * Lấy sản phẩm theo thương hiệu
     */
    public function getProductsByBrand(int $brandId, array $options = []): ProductCollection
    {
        $products = $this->getAllProducts();
        $filteredProducts = $this->filterByBrand($products, $brandId);

        // Áp dụng các tùy chọn bổ sung
        if (isset($options['status'])) {
            $filteredProducts = $this->filterByStatus($filteredProducts, $options['status']);
        }

        if (isset($options['sortBy'])) {
            $sortOrder = $options['sortOrder'] ?? 'asc';
            $filteredProducts = $this->sortProducts($filteredProducts, $options['sortBy'], $sortOrder === 'asc');
        }

        // Phân trang
        $page = $options['page'] ?? 1;
        $perPage = $options['perPage'] ?? 20;
        $paginatedProducts = $this->paginateProducts($filteredProducts, $page, $perPage);

        return $this->productRepository->createProductCollectionFromProducts(
            $paginatedProducts['data'],
            $paginatedProducts
        );
    }

    /**
     * Lấy sản phẩm nổi bật
     */
    public function getHotProducts(int $limit = 10): ProductCollection
    {
        $products = $this->getAllProducts();
        $hotProducts = $this->filterHotProducts($products);

        // Sắp xếp theo mức độ nổi bật
        $hotProducts = $this->sortProducts($hotProducts, 'hotScore', false);

        // Giới hạn số lượng
        $limitedProducts = array_slice($hotProducts, 0, $limit);

        return $this->productRepository->createProductCollectionFromProducts($limitedProducts);
    }

    /**
     * Lấy sản phẩm mới
     */
    public function getNewProducts(int $limit = 10): ProductCollection
    {
        $products = $this->getAllProducts();
        $newProducts = $this->filterNewProducts($products);

        // Sắp xếp theo ngày tạo
        $newProducts = $this->sortProducts($newProducts, 'createdAt', false);

        // Giới hạn số lượng
        $limitedProducts = array_slice($newProducts, 0, $limit);

        return $this->productRepository->createProductCollectionFromProducts($limitedProducts);
    }

    /**
     * Lấy sản phẩm trang chủ
     */
    public function getHomeProducts(int $limit = 20): ProductCollection
    {
        $products = $this->getAllProducts();
        $homeProducts = $this->filterHomeProducts($products);

        // Sắp xếp theo thứ tự hiển thị
        $homeProducts = $this->sortProducts($homeProducts, 'homeOrder', true);

        // Giới hạn số lượng
        $limitedProducts = array_slice($homeProducts, 0, $limit);

        return $this->productRepository->createProductCollectionFromProducts($limitedProducts);
    }

    /**
     * Lấy sản phẩm sắp hết hàng
     */
    public function getLowStockProducts(int $threshold = 10): ProductCollection
    {
        $products = $this->getAllProducts();
        $lowStockProducts = $this->filterLowStockProducts($products);

        // Sắp xếp theo số lượng tồn kho
        $lowStockProducts = $this->sortProducts($lowStockProducts, 'availableStock', true);

        return $this->productRepository->createProductCollectionFromProducts($lowStockProducts);
    }

    /**
     * Lấy sản phẩm hết hàng
     */
    public function getOutOfStockProducts(): ProductCollection
    {
        $products = $this->getAllProducts();
        $outOfStockProducts = $this->filterOutOfStockProducts($products);

        return $this->productRepository->createProductCollectionFromProducts($outOfStockProducts);
    }

    /**
     * Lấy thống kê sản phẩm
     */
    public function getProductStatistics(): array
    {
        $products = $this->getAllProducts();

        $stats = [
            'total' => count($products),
            'active' => count($this->filterActiveProducts($products)),
            'inactive' => count($this->filterInactiveProducts($products)),
            'outOfStock' => count($this->filterOutOfStockProducts($products)),
            'lowStock' => count($this->filterLowStockProducts($products)),
            'inStock' => count($this->filterInStockProducts($products)),
            'hot' => count($this->filterHotProducts($products)),
            'new' => count($this->filterNewProducts($products)),
            'home' => count($this->filterHomeProducts($products))
        ];

        // Thống kê theo danh mục
        $categories = $this->getAllCategories();
        $categoryStats = [];
        foreach ($categories as $category) {
            $categoryProducts = $this->filterByCategory($products, $category->getId());
            $categoryStats[$category->getName()] = count($categoryProducts);
        }
        $stats['byCategory'] = $categoryStats;

        // Thống kê theo thương hiệu
        $brands = $this->getAllBrands();
        $brandStats = [];
        foreach ($brands as $brand) {
            $brandProducts = $this->filterByBrand($products, $brand->getId());
            $brandStats[$brand->getName()] = count($brandProducts);
        }
        $stats['byBrand'] = $brandStats;

        // Thống kê giá
        if (!empty($products)) {
            $prices = array_column($products, 'price');
            $stats['priceStats'] = [
                'min' => min($prices),
                'max' => max($prices),
                'average' => array_sum($prices) / count($prices)
            ];
        }

        return $stats;
    }

    /**
     * Lấy sản phẩm liên quan
     */
    public function getRelatedProducts(Product $product, int $limit = 5): ProductCollection
    {
        $products = $this->getAllProducts();
        $relatedProducts = [];

        foreach ($products as $relatedProduct) {
            if ($relatedProduct->getId() === $product->getId()) {
                continue;
            }

            $score = 0;

            // Tính điểm dựa trên danh mục
            if ($relatedProduct->getCategoryId() === $product->getCategoryId()) {
                $score += 3;
            }

            // Tính điểm dựa trên thương hiệu
            if ($relatedProduct->getBrandId() === $product->getBrandId()) {
                $score += 2;
            }

            // Tính điểm dựa trên giá (cùng khoảng giá)
            $priceDiff = abs($relatedProduct->getPrice() - $product->getPrice()) / $product->getPrice();
            if ($priceDiff <= 0.2) {
                $score += 1;
            }

            if ($score > 0) {
                $relatedProducts[] = [
                    'product' => $relatedProduct,
                    'score' => $score
                ];
            }
        }

        // Sắp xếp theo điểm số
        usort($relatedProducts, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Lấy top products
        $topProducts = array_slice($relatedProducts, 0, $limit);
        $products = array_column($topProducts, 'product');

        return $this->productRepository->createProductCollectionFromProducts($products);
    }

    /**
     * Lấy sản phẩm bán chạy
     */
    public function getBestSellingProducts(int $limit = 10): ProductCollection
    {
        $products = $this->getAllProducts();

        // Sắp xếp theo số lượng bán
        $sortedProducts = $this->sortProducts($products, 'soldQuantity', false);

        // Giới hạn số lượng
        $limitedProducts = array_slice($sortedProducts, 0, $limit);

        return $this->productRepository->createProductCollectionFromProducts($limitedProducts);
    }

    /**
     * Lấy sản phẩm giảm giá
     */
    public function getDiscountedProducts(int $limit = 10): ProductCollection
    {
        $products = $this->getAllProducts();
        $discountedProducts = [];

        foreach ($products as $product) {
            if ($product->hasDiscount()) {
                $discountedProducts[] = $product;
            }
        }

        // Sắp xếp theo mức giảm giá
        usort($discountedProducts, function ($a, $b) {
            return $b->getDiscountPercentage() <=> $a->getDiscountPercentage();
        });

        // Giới hạn số lượng
        $limitedProducts = array_slice($discountedProducts, 0, $limit);

        return $this->productRepository->createProductCollectionFromProducts($limitedProducts);
    }

    /**
     * Lấy sản phẩm theo khoảng giá
     */
    public function getProductsByPriceRange(float $minPrice, float $maxPrice, array $options = []): ProductCollection
    {
        $products = $this->getAllProducts();
        $filteredProducts = $this->filterByPriceRange($products, $minPrice, $maxPrice);

        // Áp dụng các tùy chọn bổ sung
        if (isset($options['status'])) {
            $filteredProducts = $this->filterByStatus($filteredProducts, $options['status']);
        }

        if (isset($options['sortBy'])) {
            $sortOrder = $options['sortOrder'] ?? 'asc';
            $filteredProducts = $this->sortProducts($filteredProducts, $options['sortBy'], $sortOrder === 'asc');
        }

        // Phân trang
        $page = $options['page'] ?? 1;
        $perPage = $options['perPage'] ?? 20;
        $paginatedProducts = $this->paginateProducts($filteredProducts, $page, $perPage);

        return $this->productRepository->createProductCollectionFromProducts(
            $paginatedProducts['data'],
            $paginatedProducts
        );
    }

    /**
     * Lấy sản phẩm theo từ khóa
     */
    public function searchByKeyword(string $keyword): array
    {
        $products = $this->getAllProducts();
        $results = [];

        $keyword = strtolower($keyword);

        foreach ($products as $product) {
            $name = strtolower($product->getName() ?? '');
            $code = strtolower($product->getCode() ?? '');
            $barcode = strtolower($product->getBarcode() ?? '');
            $description = strtolower($product->getDescription() ?? '');

            if (
                strpos($name, $keyword) !== false ||
                strpos($code, $keyword) !== false ||
                strpos($barcode, $keyword) !== false ||
                strpos($description, $keyword) !== false
            ) {
                $results[] = $product;
            }
        }

        return $results;
    }

    /**
     * Lọc sản phẩm theo danh mục
     */
    public function filterByCategory(array $products, int $categoryId): array
    {
        return array_filter($products, function ($product) use ($categoryId) {
            return $product->getCategoryId() === $categoryId;
        });
    }

    /**
     * Lọc sản phẩm theo thương hiệu
     */
    public function filterByBrand(array $products, int $brandId): array
    {
        return array_filter($products, function ($product) use ($brandId) {
            return $product->getBrandId() === $brandId;
        });
    }

    /**
     * Lọc sản phẩm theo trạng thái
     */
    public function filterByStatus(array $products, string $status): array
    {
        return array_filter($products, function ($product) use ($status) {
            return $product->getStatus() === $status;
        });
    }

    /**
     * Lọc sản phẩm hoạt động
     */
    public function filterActiveProducts(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isActive();
        });
    }

    /**
     * Lọc sản phẩm không hoạt động
     */
    public function filterInactiveProducts(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isInactive();
        });
    }

    /**
     * Lọc sản phẩm hết hàng
     */
    public function filterOutOfStock(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isOutOfStock();
        });
    }

    /**
     * Lọc sản phẩm còn hàng
     */
    public function filterInStock(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isInStock();
        });
    }

    /**
     * Lọc sản phẩm sắp hết hàng
     */
    public function filterLowStockProducts(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isLowStock();
        });
    }

    /**
     * Lọc sản phẩm nổi bật
     */
    public function filterHotProducts(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isHot();
        });
    }

    /**
     * Lọc sản phẩm mới
     */
    public function filterNewProducts(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isNew();
        });
    }

    /**
     * Lọc sản phẩm trang chủ
     */
    public function filterHomeProducts(array $products): array
    {
        return array_filter($products, function ($product) {
            return $product->isHome();
        });
    }

    /**
     * Lọc sản phẩm theo khoảng giá
     */
    public function filterByPriceRange(array $products, float $minPrice, float $maxPrice): array
    {
        return array_filter($products, function ($product) use ($minPrice, $maxPrice) {
            $price = $product->getPrice();
            return $price >= $minPrice && $price <= $maxPrice;
        });
    }

    /**
     * Sắp xếp sản phẩm
     */
    public function sortProducts(array $products, string $field, bool $ascending = true): array
    {
        usort($products, function ($a, $b) use ($field, $ascending) {
            $valueA = $a->getAttribute($field);
            $valueB = $b->getAttribute($field);

            if ($ascending) {
                return $valueA <=> $valueB;
            }

            return $valueB <=> $valueA;
        });

        return $products;
    }

    /**
     * Phân trang sản phẩm
     */
    public function paginateProducts(array $products, int $page = 1, int $perPage = 20): array
    {
        $total = count($products);
        $lastPage = ceil($total / $perPage);
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        $items = array_slice($products, $offset, $perPage);

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
            'has_more_pages' => $page < $lastPage
        ];
    }

    /**
     * Lấy tất cả sản phẩm
     */
    protected function getAllProducts(): array
    {
        // TODO: Implement actual data retrieval from API or database
        return [];
    }

    /**
     * Lấy tất cả danh mục
     */
    protected function getAllCategories(): array
    {
        // TODO: Implement actual data retrieval from API or database
        return [];
    }

    /**
     * Lấy tất cả thương hiệu
     */
    protected function getAllBrands(): array
    {
        // TODO: Implement actual data retrieval from API or database
        return [];
    }

    /**
     * Thêm sản phẩm mới hoặc cập nhật sản phẩm hiện có
     *
     * @param array|ProductAddRequest $productData Dữ liệu sản phẩm hoặc ProductAddRequest object
     * @return ProductAddResponse Response từ API
     * @throws \Exception Khi có lỗi xảy ra
     */
    public function addProduct($productData): ProductAddResponse
    {
        // Convert array to ProductAddRequest if needed
        if (is_array($productData)) {
            $productData = $this->productRepository->createProductAddRequest($productData);
        }

        // Validate request data
        if (!$productData->isValid()) {
            throw new \InvalidArgumentException('Dữ liệu sản phẩm không hợp lệ: ' . json_encode($productData->getErrors()));
        }

        // TODO: Implement actual API call to Nhanh.vn
        // This is a placeholder implementation
        $response = [
            'ids' => [
                $productData->getId() => rand(1000000, 9999999) // Mock Nhanh.vn ID
            ],
            'barcodes' => [
                $productData->getId() => 'BAR' . rand(100000, 999999) // Mock barcode
            ]
        ];

        return $this->productRepository->createProductAddResponse($response);
    }

    /**
     * Thêm nhiều sản phẩm cùng lúc (batch add)
     *
     * @param array $productsData Mảng dữ liệu sản phẩm
     * @return ProductAddResponse Response từ API
     * @throws \InvalidArgumentException Khi có lỗi xảy ra
     */
    public function addProducts(array $productsData): ProductAddResponse
    {
        if (empty($productsData)) {
            throw new \InvalidArgumentException('Danh sách sản phẩm không được để trống');
        }

        if (count($productsData) > 300) {
            throw new \InvalidArgumentException('Chỉ được thêm tối đa 300 sản phẩm mỗi lần');
        }

        // Validate all products
        $requests = [];
        foreach ($productsData as $index => $productData) {
            $request = $this->productRepository->createProductAddRequest($productData);

            if (!$request->isValid()) {
                throw new \InvalidArgumentException(
                    "Sản phẩm thứ {$index} không hợp lệ: " . json_encode($request->getErrors())
                );
            }

            $requests[] = $request;
            unset($request); // Memory management
        }

        // TODO: Implement actual batch API call to Nhanh.vn
        // This is a placeholder implementation
        $response = [
            'ids' => [],
            'barcodes' => []
        ];

        foreach ($requests as $request) {
            $response['ids'][$request->getId()] = rand(1000000, 9999999);
            $response['barcodes'][$request->getId()] = 'BAR' . rand(100000, 999999);
        }

        // Clean up memory
        unset($requests);

        return $this->productRepository->createProductAddResponse($response);
    }

    /**
     * Validate product add request data
     *
     * @param array $productData Dữ liệu sản phẩm
     * @return bool True nếu hợp lệ
     */
    public function validateProductAddRequest(array $productData): bool
    {
        try {
            $request = $this->productRepository->createProductAddRequest($productData);
            return $request->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate multiple product add requests
     *
     * @param array $productsData Mảng dữ liệu sản phẩm
     * @return array Mảng lỗi validation (empty nếu tất cả đều hợp lệ)
     */
    public function validateProductAddRequests(array $productsData): array
    {
        $errors = [];

        foreach ($productsData as $index => $productData) {
            if (!$this->validateProductAddRequest($productData)) {
                $errors[$index] = 'Dữ liệu sản phẩm không hợp lệ';
            }
        }

        return $errors;
    }

    /**
     * Thêm ảnh sản phẩm từ CDN bên ngoài
     *
     * @param array $productData Dữ liệu sản phẩm và ảnh
     * @return ProductExternalImageResponse Response từ API
     * @throws \InvalidArgumentException Khi có lỗi xảy ra
     */
    public function addProductExternalImage(array $productData): ProductExternalImageResponse
    {
        $request = $this->productRepository->createProductExternalImageRequest($productData);

        if (!$request->isValid()) {
            throw new \InvalidArgumentException(
                'Dữ liệu không hợp lệ: ' . json_encode($request->getErrors())
            );
        }

        // TODO: Implement actual API call to Nhanh.vn
        // This is a placeholder implementation
        $response = [
            'code' => 1,
            'data' => [$request->getProductId()]
        ];

        return $this->productRepository->createProductExternalImageResponse($response);
    }

    /**
     * Thêm ảnh cho nhiều sản phẩm cùng lúc (batch add)
     *
     * @param array $productsData Mảng dữ liệu sản phẩm và ảnh
     * @return ProductExternalImageResponse Response từ API
     * @throws \InvalidArgumentException Khi có lỗi xảy ra
     */
    public function addProductExternalImages(array $productsData): ProductExternalImageResponse
    {
        if (empty($productsData)) {
            throw new \InvalidArgumentException('Danh sách sản phẩm không được để trống');
        }

        if (count($productsData) > 10) {
            throw new \InvalidArgumentException('Chỉ được thêm ảnh cho tối đa 10 sản phẩm mỗi lần');
        }

        // Validate all products
        $requests = [];
        foreach ($productsData as $index => $productData) {
            $request = $this->productRepository->createProductExternalImageRequest($productData);

            if (!$request->isValid()) {
                $exception = new \InvalidArgumentException(
                    "Sản phẩm thứ {$index} không hợp lệ: " . json_encode($request->getErrors())
                );
                // Clean up memory before throwing exception
                unset($requests);
                throw $exception;
            }

            $requests[] = $request;
            unset($request); // Memory management
        }

        // TODO: Implement actual batch API call to Nhanh.vn
        // This is a placeholder implementation
        $response = [
            'code' => 1,
            'data' => array_map(fn($request) => $request->getProductId(), $requests)
        ];

        // Clean up memory
        unset($requests);

        return $this->productRepository->createProductExternalImageResponse($response);
    }

    /**
     * Validate product external image request data
     *
     * @param array $productData Dữ liệu sản phẩm và ảnh
     * @return bool True nếu hợp lệ
     */
    public function validateProductExternalImageRequest(array $productData): bool
    {
        try {
            $request = $this->productRepository->createProductExternalImageRequest($productData);
            return $request->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate multiple product external image requests
     *
     * @param array $productsData Mảng dữ liệu sản phẩm và ảnh
     * @return bool True nếu hợp lệ
     */
    public function validateProductExternalImageRequests(array $productsData): bool
    {
        foreach ($productsData as $productData) {
            if (!$this->validateProductExternalImageRequest($productData)) {
                return false;
            }
        }
        return true;
    }
}
