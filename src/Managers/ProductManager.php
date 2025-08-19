<?php

namespace Puleeno\NhanhVn\Managers;

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
use Puleeno\NhanhVn\Repositories\ProductRepository;
use Puleeno\NhanhVn\Services\ProductService;

/**
 * Product Manager
 */
class ProductManager
{
    protected ProductRepository $productRepository;
    protected ProductService $productService;

    public function __construct(ProductRepository $productRepository, ProductService $productService)
    {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }

    /**
     * Tạo product từ array data
     */
    public function createProduct(array $data): Product
    {
        return $this->productRepository->createProduct($data);
    }

    /**
     * Tạo nhiều products từ array data
     */
    public function createProducts(array $data): array
    {
        return $this->productRepository->createProducts($data);
    }

    /**
     * Tạo product collection từ array data
     */
    public function createProductCollection(array $data): ProductCollection
    {
        return $this->productRepository->createProductCollection($data);
    }

    /**
     * Tạo product collection từ array products
     */
    public function createProductCollectionFromProducts(array $products, array $pagination = []): ProductCollection
    {
        return $this->productRepository->createProductCollectionFromProducts($products, $pagination);
    }

    /**
     * Tạo product collection từ Collection object
     */
    public function createProductCollectionFromCollection(Collection $products, array $pagination = []): ProductCollection
    {
        return $this->productRepository->createProductCollectionFromCollection($products, $pagination);
    }

    /**
     * Tạo product category từ array data
     */
    public function createProductCategory(array $data): ProductCategory
    {
        return $this->productRepository->createProductCategory($data);
    }

    /**
     * Tạo nhiều product categories từ array data
     */
    public function createProductCategories(array $data): array
    {
        return $this->productRepository->createProductCategories($data);
    }

    /**
     * Tạo product brand từ array data
     */
    public function createProductBrand(array $data): ProductBrand
    {
        return $this->productRepository->createProductBrand($data);
    }

    /**
     * Tạo nhiều product brands từ array data
     */
    public function createProductBrands(array $data): array
    {
        return $this->productRepository->createProductBrands($data);
    }

    /**
     * Tạo product type từ array data
     */
    public function createProductType(array $data): ProductType
    {
        return $this->productRepository->createProductType($data);
    }

    /**
     * Tạo nhiều product types từ array data
     */
    public function createProductTypes(array $data): array
    {
        return $this->productRepository->createProductTypes($data);
    }

    /**
     * Tạo product supplier từ array data
     */
    public function createProductSupplier(array $data): ProductSupplier
    {
        return $this->productRepository->createProductSupplier($data);
    }

    /**
     * Tạo nhiều product suppliers từ array data
     */
    public function createProductSuppliers(array $data): array
    {
        return $this->productRepository->createProductSuppliers($data);
    }

    /**
     * Tạo product depot từ array data
     */
    public function createProductDepot(array $data): ProductDepot
    {
        return $this->productRepository->createProductDepot($data);
    }

    /**
     * Tạo nhiều product depots từ array data
     */
    public function createProductDepots(array $data): array
    {
        return $this->productRepository->createProductDepots($data);
    }

    /**
     * Tạo product import type từ array data
     */
    public function createProductImportType(array $data): ProductImportType
    {
        return $this->productRepository->createProductImportType($data);
    }

    /**
     * Tạo nhiều product import types từ array data
     */
    public function createProductImportTypes(array $data): array
    {
        return $this->productRepository->createProductImportTypes($data);
    }

    /**
     * Tạo product attribute từ array data
     */
    public function createProductAttribute(array $data): ProductAttribute
    {
        return $this->productRepository->createProductAttribute($data);
    }

    /**
     * Tạo nhiều product attributes từ array data
     */
    public function createProductAttributes(array $data): array
    {
        return $this->productRepository->createProductAttributes($data);
    }

    /**
     * Tạo product unit từ array data
     */
    public function createProductUnit(array $data): ProductUnit
    {
        return $this->productRepository->createProductUnit($data);
    }

    /**
     * Tạo nhiều product units từ array data
     */
    public function createProductUnits(array $data): array
    {
        return $this->productRepository->createProductUnits($data);
    }

    /**
     * Tạo product gift từ array data
     */
    public function createProductGift(array $data): ProductGift
    {
        return $this->productRepository->createProductGift($data);
    }

    /**
     * Tạo nhiều product gifts từ array data
     */
    public function createProductGifts(array $data): array
    {
        return $this->productRepository->createProductGifts($data);
    }

    /**
     * Tạo product IMEI từ array data
     */
    public function createProductImei(array $data): ProductImei
    {
        return $this->productRepository->createProductImei($data);
    }

    /**
     * Tạo nhiều product IMEIs từ array data
     */
    public function createProductImeis(array $data): array
    {
        return $this->productRepository->createProductImeis($data);
    }

    /**
     * Tạo product expiry từ array data
     */
    public function createProductExpiry(array $data): ProductExpiry
    {
        return $this->productRepository->createProductExpiry($data);
    }

    /**
     * Tạo nhiều product expiries từ array data
     */
    public function createProductExpiries(array $data): array
    {
        return $this->productRepository->createProductExpiries($data);
    }

    /**
     * Tạo product IMEI history từ array data
     */
    public function createProductImeiHistory(array $data): ProductImeiHistory
    {
        return $this->productRepository->createProductImeiHistory($data);
    }

    /**
     * Tạo nhiều product IMEI histories từ array data
     */
    public function createProductImeiHistories(array $data): array
    {
        return $this->productRepository->createProductImeiHistories($data);
    }

    /**
     * Tạo product IMEI sold từ array data
     */
    public function createProductImeiSold(array $data): ProductImeiSold
    {
        return $this->productRepository->createProductImeiSold($data);
    }

    /**
     * Tạo nhiều product IMEI solds từ array data
     */
    public function createProductImeiSolds(array $data): array
    {
        return $this->productRepository->createProductImeiSolds($data);
    }

    /**
     * Tạo product internal category từ array data
     */
    public function createProductInternalCategory(array $data): ProductInternalCategory
    {
        return $this->productRepository->createProductInternalCategory($data);
    }

    /**
     * Tạo nhiều product internal categories từ array data
     */
    public function createProductInternalCategories(array $data): array
    {
        return $this->productRepository->createProductInternalCategories($data);
    }

    /**
     * Tạo product website info từ array data
     */
    public function createProductWebsiteInfo(array $data): ProductWebsiteInfo
    {
        return $this->productRepository->createProductWebsiteInfo($data);
    }

    /**
     * Tạo nhiều product website infos từ array data
     */
    public function createProductWebsiteInfos(array $data): array
    {
        return $this->productRepository->createProductWebsiteInfos($data);
    }

    /**
     * Tạo product warranty từ array data
     */
    public function createProductWarranty(array $data): ProductWarranty
    {
        return $this->productRepository->createProductWarranty($data);
    }

    /**
     * Tạo nhiều product warranties từ array data
     */
    public function createProductWarranties(array $data): array
    {
        return $this->productRepository->createProductWarranties($data);
    }

    /**
     * Validate product data
     */
    public function validateProductData(array $data): bool
    {
        return $this->productRepository->validateProductData($data);
    }

    /**
     * Validate product category data
     */
    public function validateProductCategoryData(array $data): bool
    {
        return $this->productRepository->validateProductCategoryData($data);
    }

    /**
     * Validate product brand data
     */
    public function validateProductBrandData(array $data): bool
    {
        return $this->productRepository->validateProductBrandData($data);
    }

    /**
     * Validate product supplier data
     */
    public function validateProductSupplierData(array $data): bool
    {
        return $this->productRepository->validateProductSupplierData($data);
    }

    /**
     * Validate product depot data
     */
    public function validateProductDepotData(array $data): bool
    {
        return $this->productRepository->validateProductDepotData($data);
    }

    /**
     * Validate product import type data
     */
    public function validateProductImportTypeData(array $data): bool
    {
        return $this->productRepository->validateProductImportTypeData($data);
    }

    /**
     * Validate product attribute data
     */
    public function validateProductAttributeData(array $data): bool
    {
        return $this->productRepository->validateProductAttributeData($data);
    }

    /**
     * Validate product unit data
     */
    public function validateProductUnitData(array $data): bool
    {
        return $this->productRepository->validateProductUnitData($data);
    }

    /**
     * Validate product gift data
     */
    public function validateProductGiftData(array $data): bool
    {
        return $this->productRepository->validateProductGiftData($data);
    }

    /**
     * Validate product IMEI data
     */
    public function validateProductImeiData(array $data): bool
    {
        return $this->productRepository->validateProductImeiData($data);
    }

    /**
     * Validate product expiry data
     */
    public function validateProductExpiryData(array $data): bool
    {
        return $this->productRepository->validateProductExpiryData($data);
    }

    /**
     * Validate product IMEI history data
     */
    public function validateProductImeiHistoryData(array $data): bool
    {
        return $this->productRepository->validateProductImeiHistoryData($data);
    }

    /**
     * Validate product IMEI sold data
     */
    public function validateProductImeiSoldData(array $data): bool
    {
        return $this->productRepository->validateProductImeiSoldData($data);
    }

    /**
     * Validate product internal category data
     */
    public function validateProductInternalCategoryData(array $data): bool
    {
        return $this->productRepository->validateProductInternalCategoryData($data);
    }

    /**
     * Validate product website info data
     */
    public function validateProductWebsiteInfoData(array $data): bool
    {
        return $this->productRepository->validateProductWebsiteInfoData($data);
    }

    /**
     * Validate product warranty data
     */
    public function validateProductWarrantyData(array $data): bool
    {
        return $this->productRepository->validateProductWarrantyData($data);
    }

    /**
     * Validate batch product data
     */
    public function validateBatchProductData(array $products): bool
    {
        return $this->productRepository->validateBatchProductData($products);
    }

    /**
     * Validate batch product external image data
     */
    public function validateBatchProductExternalImageData(array $products): bool
    {
        return $this->productRepository->validateBatchProductExternalImageData($products);
    }

    /**
     * Get product statistics from collection
     */
    public function getProductStatistics(ProductCollection $collection): array
    {
        return $this->productRepository->getProductStatistics($collection);
    }

    /**
     * Get product pagination info from collection
     */
    public function getProductPaginationInfo(ProductCollection $collection): array
    {
        return $this->productRepository->getProductPaginationInfo($collection);
    }

    /**
     * Get product filters info from collection
     */
    public function getProductFiltersInfo(ProductCollection $collection): array
    {
        return $this->productRepository->getProductFiltersInfo($collection);
    }

    /**
     * Filter products by category
     */
    public function filterProductsByCategory(ProductCollection $collection, int $categoryId): Collection
    {
        return $this->productRepository->filterProductsByCategory($collection, $categoryId);
    }

    /**
     * Filter products by brand
     */
    public function filterProductsByBrand(ProductCollection $collection, int $brandId): Collection
    {
        return $this->productRepository->filterProductsByBrand($collection, $brandId);
    }

    /**
     * Filter products by status
     */
    public function filterProductsByStatus(ProductCollection $collection, string $status): Collection
    {
        return $this->productRepository->filterProductsByStatus($collection, $status);
    }

    /**
     * Filter active products
     */
    public function filterActiveProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterActiveProducts($collection);
    }

    /**
     * Filter inactive products
     */
    public function filterInactiveProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterInactiveProducts($collection);
    }

    /**
     * Filter out of stock products
     */
    public function filterOutOfStockProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterOutOfStockProducts($collection);
    }

    /**
     * Filter in stock products
     */
    public function filterInStockProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterInStockProducts($collection);
    }

    /**
     * Filter low stock products
     */
    public function filterLowStockProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterLowStockProducts($collection);
    }

    /**
     * Filter hot products
     */
    public function filterHotProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterHotProducts($collection);
    }

    /**
     * Filter new products
     */
    public function filterNewProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterNewProducts($collection);
    }

    /**
     * Filter home products
     */
    public function filterHomeProducts(ProductCollection $collection): Collection
    {
        return $this->productRepository->filterHomeProducts($collection);
    }

    /**
     * Filter products by price range
     */
    public function filterProductsByPriceRange(ProductCollection $collection, float $minPrice, float $maxPrice): Collection
    {
        return $this->productRepository->filterProductsByPriceRange($collection, $minPrice, $maxPrice);
    }

    /**
     * Filter products by inventory range
     */
    public function filterProductsByInventoryRange(ProductCollection $collection, int $minQuantity, int $maxQuantity): Collection
    {
        return $this->productRepository->filterProductsByInventoryRange($collection, $minQuantity, $maxQuantity);
    }

    /**
     * Search products
     */
    public function searchProducts(ProductCollection $collection, string $term): Collection
    {
        return $this->productRepository->searchProducts($collection, $term);
    }

    /**
     * Sort products
     */
    public function sortProducts(ProductCollection $collection, string $field, bool $ascending = true): Collection
    {
        return $this->productRepository->sortProducts($collection, $field, $ascending);
    }

    /**
     * Paginate products
     */
    public function paginateProducts(ProductCollection $collection, int $page = 1, int $perPage = 20): array
    {
        return $this->productRepository->paginateProducts($collection, $page, $perPage);
    }

    /**
     * Get product by ID
     */
    public function getProductById(ProductCollection $collection, int $id): ?Product
    {
        return $this->productRepository->getProductById($collection, $id);
    }

    /**
     * Get product by code
     */
    public function getProductByCode(ProductCollection $collection, string $code): ?Product
    {
        return $this->productRepository->getProductByCode($collection, $code);
    }

    /**
     * Get product by name
     */
    public function getProductByName(ProductCollection $collection, string $name): ?Product
    {
        return $this->productRepository->getProductByName($collection, $name);
    }

    /**
     * Get product by barcode
     */
    public function getProductByBarcode(ProductCollection $collection, string $barcode): ?Product
    {
        return $this->productRepository->getProductByBarcode($collection, $barcode);
    }

    /**
     * Get first product
     */
    public function getFirstProduct(ProductCollection $collection): ?Product
    {
        return $this->productRepository->getFirstProduct($collection);
    }

    /**
     * Get last product
     */
    public function getLastProduct(ProductCollection $collection): ?Product
    {
        return $this->productRepository->getLastProduct($collection);
    }

    /**
     * Get product at index
     */
    public function getProductAt(ProductCollection $collection, int $index): ?Product
    {
        return $this->productRepository->getProductAt($collection, $index);
    }

    /**
     * Get total value of products
     */
    public function getTotalProductValue(ProductCollection $collection): float
    {
        return $this->productRepository->getTotalProductValue($collection);
    }

    /**
     * Get average product price
     */
    public function getAverageProductPrice(ProductCollection $collection): float
    {
        return $this->productRepository->getAverageProductPrice($collection);
    }

    /**
     * Get min product price
     */
    public function getMinProductPrice(ProductCollection $collection): float
    {
        return $this->productRepository->getMinProductPrice($collection);
    }

    /**
     * Get max product price
     */
    public function getMaxProductPrice(ProductCollection $collection): float
    {
        return $this->productRepository->getMaxProductPrice($collection);
    }

    /**
     * Get total product stock
     */
    public function getTotalProductStock(ProductCollection $collection): int
    {
        return $this->productRepository->getTotalProductStock($collection);
    }

    /**
     * Get average product stock
     */
    public function getAverageProductStock(ProductCollection $collection): float
    {
        return $this->productRepository->getAverageProductStock($collection);
    }

    /**
     * Get min product stock
     */
    public function getMinProductStock(ProductCollection $collection): int
    {
        return $this->productRepository->getMinProductStock($collection);
    }

    /**
     * Get max product stock
     */
    public function getMaxProductStock(ProductCollection $collection): int
    {
        return $this->productRepository->getMaxProductStock($collection);
    }

    /**
     * Lưu product vào cache
     */
    public function cacheProduct(Product $product, int $ttl = 3600): bool
    {
        return $this->productRepository->cacheProduct($product, $ttl);
    }

    /**
     * Lấy product từ cache
     */
    public function getCachedProduct(int $id): ?Product
    {
        return $this->productRepository->getCachedProduct($id);
    }

    /**
     * Xóa product khỏi cache
     */
    public function clearCachedProduct(int $id): bool
    {
        return $this->productRepository->clearCachedProduct($id);
    }

    /**
     * Lưu product collection vào cache
     */
    public function cacheProductCollection(ProductCollection $collection, int $ttl = 3600): bool
    {
        return $this->productRepository->cacheProductCollection($collection, $ttl);
    }

    /**
     * Lấy product collection từ cache
     */
    public function getCachedProductCollection(string $key): ?ProductCollection
    {
        return $this->productRepository->getCachedProductCollection($key);
    }

    /**
     * Xóa product collection khỏi cache
     */
    public function clearCachedProductCollection(string $key): bool
    {
        return $this->productRepository->clearCachedProductCollection($key);
    }

    /**
     * Lưu product categories vào cache
     */
    public function cacheProductCategories(array $categories, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductCategories($categories, $ttl);
    }

    /**
     * Lấy product categories từ cache
     */
    public function getCachedProductCategories(): ?array
    {
        return $this->productRepository->getCachedProductCategories();
    }

    /**
     * Xóa product categories khỏi cache
     */
    public function clearCachedProductCategories(): bool
    {
        return $this->productRepository->clearCachedProductCategories();
    }

    /**
     * Lưu product brands vào cache
     */
    public function cacheProductBrands(array $brands, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductBrands($brands, $ttl);
    }

    /**
     * Lấy product brands từ cache
     */
    public function getCachedProductBrands(): ?array
    {
        return $this->productRepository->getCachedProductBrands();
    }

    /**
     * Xóa product brands khỏi cache
     */
    public function clearCachedProductBrands(): bool
    {
        return $this->productRepository->clearCachedProductBrands();
    }

    /**
     * Lưu product types vào cache
     */
    public function cacheProductTypes(array $types, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductTypes($types, $ttl);
    }

    /**
     * Lấy product types từ cache
     */
    public function getCachedProductTypes(): ?array
    {
        return $this->productRepository->getCachedProductTypes();
    }

    /**
     * Xóa product types khỏi cache
     */
    public function clearCachedProductTypes(): bool
    {
        return $this->productRepository->clearCachedProductTypes();
    }

    /**
     * Lưu product suppliers vào cache
     */
    public function cacheProductSuppliers(array $suppliers, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductSuppliers($suppliers, $ttl);
    }

    /**
     * Lấy product suppliers từ cache
     */
    public function getCachedProductSuppliers(): ?array
    {
        return $this->productRepository->getCachedProductSuppliers();
    }

    /**
     * Xóa product suppliers khỏi cache
     */
    public function clearCachedProductSuppliers(): bool
    {
        return $this->productRepository->clearCachedProductSuppliers();
    }

    /**
     * Lưu product depots vào cache
     */
    public function cacheProductDepots(array $depots, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductDepots($depots, $ttl);
    }

    /**
     * Lấy product depots từ cache
     */
    public function getCachedProductDepots(): ?array
    {
        return $this->productRepository->getCachedProductDepots();
    }

    /**
     * Xóa product depots khỏi cache
     */
    public function clearCachedProductDepots(): bool
    {
        return $this->productRepository->clearCachedProductDepots();
    }

    /**
     * Lưu product import types vào cache
     */
    public function cacheProductImportTypes(array $importTypes, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductImportTypes($importTypes, $ttl);
    }

    /**
     * Lấy product import types từ cache
     */
    public function getCachedProductImportTypes(): ?array
    {
        return $this->productRepository->getCachedProductImportTypes();
    }

    /**
     * Xóa product import types khỏi cache
     */
    public function clearCachedProductImportTypes(): bool
    {
        return $this->productRepository->clearCachedProductImportTypes();
    }

    /**
     * Lưu product attributes vào cache
     */
    public function cacheProductAttributes(array $attributes, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductAttributes($attributes, $ttl);
    }

    /**
     * Lấy product attributes từ cache
     */
    public function getCachedProductAttributes(): ?array
    {
        return $this->productRepository->getCachedProductAttributes();
    }

    /**
     * Xóa product attributes khỏi cache
     */
    public function clearCachedProductAttributes(): bool
    {
        return $this->productRepository->clearCachedProductAttributes();
    }

    /**
     * Lưu product units vào cache
     */
    public function cacheProductUnits(array $units, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductUnits($units, $ttl);
    }

    /**
     * Lấy product units từ cache
     */
    public function getCachedProductUnits(): ?array
    {
        return $this->productRepository->getCachedProductUnits();
    }

    /**
     * Xóa product units khỏi cache
     */
    public function clearCachedProductUnits(): bool
    {
        return $this->productRepository->clearCachedProductUnits();
    }

    /**
     * Lưu product gifts vào cache
     */
    public function cacheProductGifts(array $gifts, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductGifts($gifts, $ttl);
    }

    /**
     * Lấy product gifts từ cache
     */
    public function getCachedProductGifts(): ?array
    {
        return $this->productRepository->getCachedProductGifts();
    }

    /**
     * Xóa product gifts khỏi cache
     */
    public function clearCachedProductGifts(): bool
    {
        return $this->productRepository->clearCachedProductGifts();
    }

    /**
     * Lưu product IMEIs vào cache
     */
    public function cacheProductImeis(array $imeis, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductImeis($imeis, $ttl);
    }

    /**
     * Lấy product IMEIs từ cache
     */
    public function getCachedProductImeis(): ?array
    {
        return $this->productRepository->getCachedProductImeis();
    }

    /**
     * Xóa product IMEIs khỏi cache
     */
    public function clearCachedProductImeis(): bool
    {
        return $this->productRepository->clearCachedProductImeis();
    }

    /**
     * Lưu product expiries vào cache
     */
    public function cacheProductExpiries(array $expiries, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductExpiries($expiries, $ttl);
    }

    /**
     * Lấy product expiries từ cache
     */
    public function getCachedProductExpiries(): ?array
    {
        return $this->productRepository->getCachedProductExpiries();
    }

    /**
     * Xóa product expiries khỏi cache
     */
    public function clearCachedProductExpiries(): bool
    {
        return $this->productRepository->clearCachedProductExpiries();
    }

    /**
     * Lưu product IMEI histories vào cache
     */
    public function cacheProductImeiHistories(array $histories, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductImeiHistories($histories, $ttl);
    }

    /**
     * Lấy product IMEI histories từ cache
     */
    public function getCachedProductImeiHistories(): ?array
    {
        return $this->productRepository->getCachedProductImeiHistories();
    }

    /**
     * Xóa product IMEI histories khỏi cache
     */
    public function clearCachedProductImeiHistories(): bool
    {
        return $this->productRepository->clearCachedProductImeiHistories();
    }

    /**
     * Lưu product IMEI solds vào cache
     */
    public function cacheProductImeiSolds(array $solds, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductImeiSolds($solds, $ttl);
    }

    /**
     * Lấy product IMEI solds từ cache
     */
    public function getCachedProductImeiSolds(): ?array
    {
        return $this->productRepository->getCachedProductImeiSolds();
    }

    /**
     * Xóa product IMEI solds khỏi cache
     */
    public function clearCachedProductImeiSolds(): bool
    {
        return $this->productRepository->clearCachedProductImeiSolds();
    }

    /**
     * Lưu product internal categories vào cache
     */
    public function cacheProductInternalCategories(array $categories, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductInternalCategories($categories, $ttl);
    }

    /**
     * Lấy product internal categories từ cache
     */
    public function getCachedProductInternalCategories(): ?array
    {
        return $this->productRepository->getCachedProductInternalCategories();
    }

    /**
     * Xóa product internal categories khỏi cache
     */
    public function clearCachedProductInternalCategories(): bool
    {
        return $this->productRepository->clearCachedProductInternalCategories();
    }

    /**
     * Lưu product website infos vào cache
     */
    public function cacheProductWebsiteInfos(array $infos, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductWebsiteInfos($infos, $ttl);
    }

    /**
     * Lấy product website infos từ cache
     */
    public function getCachedProductWebsiteInfos(): ?array
    {
        return $this->productRepository->getCachedProductWebsiteInfos();
    }

    /**
     * Xóa product website infos khỏi cache
     */
    public function clearCachedProductWebsiteInfos(): bool
    {
        return $this->productRepository->clearCachedProductWebsiteInfos();
    }

    /**
     * Lưu product warranties vào cache
     */
    public function cacheProductWarranties(array $warranties, int $ttl = 86400): bool
    {
        return $this->productRepository->cacheProductWarranties($warranties, $ttl);
    }

    /**
     * Lấy product warranties từ cache
     */
    public function getCachedProductWarranties(): ?array
    {
        return $this->productRepository->getCachedProductWarranties();
    }

    /**
     * Xóa product warranties khỏi cache
     */
    public function clearCachedProductWarranties(): bool
    {
        return $this->productRepository->clearCachedProductWarranties();
    }

    /**
     * Clear all product cache
     */
    public function clearAllProductCache(): bool
    {
        $this->clearCachedProductCategories();
        $this->clearCachedProductBrands();
        $this->clearCachedProductTypes();
        $this->clearCachedProductSuppliers();
        $this->clearCachedProductDepots();
        $this->clearCachedProductImportTypes();
        $this->clearCachedProductAttributes();
        $this->clearCachedProductUnits();
        $this->clearCachedProductGifts();
        $this->clearCachedProductImeis();
        $this->clearCachedProductExpiries();
        $this->clearCachedProductImeiHistories();
        $this->clearCachedProductImeiSolds();
        $this->clearCachedProductInternalCategories();
        $this->clearCachedProductWebsiteInfos();
        $this->clearCachedProductWarranties();

        return true;
    }

    /**
     * Get all product statistics
     */
    public function getAllProductStatistics(): array
    {
        $stats = [];

        // Get cached data if available
        $categories = $this->getCachedProductCategories();
        if ($categories) {
            $stats['categories'] = count($categories);
        }

        $brands = $this->getCachedProductBrands();
        if ($brands) {
            $stats['brands'] = count($brands);
        }

        $types = $this->getCachedProductTypes();
        if ($types) {
            $stats['types'] = count($types);
        }

        $suppliers = $this->getCachedProductSuppliers();
        if ($suppliers) {
            $stats['suppliers'] = count($suppliers);
        }

        $depots = $this->getCachedProductDepots();
        if ($depots) {
            $stats['depots'] = count($depots);
        }

        $importTypes = $this->getCachedProductImportTypes();
        if ($importTypes) {
            $stats['importTypes'] = count($importTypes);
        }

        $attributes = $this->getCachedProductAttributes();
        if ($attributes) {
            $stats['attributes'] = count($attributes);
        }

        $units = $this->getCachedProductUnits();
        if ($units) {
            $stats['units'] = count($units);
        }

        $gifts = $this->getCachedProductGifts();
        if ($gifts) {
            $stats['gifts'] = count($gifts);
        }

        $imeis = $this->getCachedProductImeis();
        if ($imeis) {
            $stats['imeis'] = count($imeis);
        }

        $expiries = $this->getCachedProductExpiries();
        if ($expiries) {
            $stats['expiries'] = count($expiries);
        }

        $imeiHistories = $this->getCachedProductImeiHistories();
        if ($imeiHistories) {
            $stats['imeiHistories'] = count($imeiHistories);
        }

        $imeiSolds = $this->getCachedProductImeiSolds();
        if ($imeiSolds) {
            $stats['imeiSolds'] = count($imeiSolds);
        }

        $internalCategories = $this->getCachedProductInternalCategories();
        if ($internalCategories) {
            $stats['internalCategories'] = count($internalCategories);
        }

        $websiteInfos = $this->getCachedProductWebsiteInfos();
        if ($websiteInfos) {
            $stats['websiteInfos'] = count($websiteInfos);
        }

        $warranties = $this->getCachedProductWarranties();
        if ($warranties) {
            $stats['warranties'] = count($warranties);
        }

        return $stats;
    }

    /**
     * Check if product cache is available
     */
    public function isProductCacheAvailable(): bool
    {
        $categories = $this->getCachedProductCategories();
        $brands = $this->getCachedProductBrands();
        $types = $this->getCachedProductTypes();

        return $categories !== null && $brands !== null && $types !== null;
    }

    /**
     * Get product cache status
     */
    public function getProductCacheStatus(): array
    {
        return [
            'categories' => $this->getCachedProductCategories() !== null,
            'brands' => $this->getCachedProductBrands() !== null,
            'types' => $this->getCachedProductTypes() !== null,
            'suppliers' => $this->getCachedProductSuppliers() !== null,
            'depots' => $this->getCachedProductDepots() !== null,
            'importTypes' => $this->getCachedProductImportTypes() !== null,
            'attributes' => $this->getCachedProductAttributes() !== null,
            'units' => $this->getCachedProductUnits() !== null,
            'gifts' => $this->getCachedProductGifts() !== null,
            'imeis' => $this->getCachedProductImeis() !== null,
            'expiries' => $this->getCachedProductExpiries() !== null,
            'imeiHistories' => $this->getCachedProductImeiHistories() !== null,
            'imeiSolds' => $this->getCachedProductImeiSolds() !== null,
            'internalCategories' => $this->getCachedProductInternalCategories() !== null,
            'websiteInfos' => $this->getCachedProductWebsiteInfos() !== null,
            'warranties' => $this->getCachedProductWarranties() !== null
        ];
    }
}
