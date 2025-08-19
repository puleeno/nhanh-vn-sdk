<?php

namespace Puleeno\NhanhVn\Repositories;

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
use Puleeno\NhanhVn\Services\CacheService;

/**
 * Product Repository
 */
class ProductRepository
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    // Factory methods for creating entities
    public function createProduct(array $data): Product { return new Product($data); }
    public function createProducts(array $data): array { return array_map([$this, 'createProduct'], $data); }
    public function createProductCollection(array $data): ProductCollection { return new ProductCollection($data); }
    public function createProductCollectionFromProducts(array $products, array $pagination = []): ProductCollection {
        return new ProductCollection(['products' => $products, 'id' => 'collection_' . uniqid()] + $pagination);
    }
    public function createProductCollectionFromCollection(Collection $products, array $pagination = []): ProductCollection {
        return new ProductCollection(['products' => $products, 'id' => 'collection_' . uniqid()] + $pagination);
    }
    public function createProductCategory(array $data): ProductCategory { return new ProductCategory($data); }
    public function createProductCategories(array $data): array { return array_map([$this, 'createProductCategory'], $data); }
    public function createProductBrand(array $data): ProductBrand { return new ProductBrand($data); }
    public function createProductBrands(array $data): array { return array_map([$this, 'createProductBrand'], $data); }
    public function createProductType(array $data): ProductType { return new ProductType($data); }
    public function createProductTypes(array $data): array { return array_map([$this, 'createProductType'], $data); }
    public function createProductSupplier(array $data): ProductSupplier { return new ProductSupplier($data); }
    public function createProductSuppliers(array $data): array { return array_map([$this, 'createProductSupplier'], $data); }
    public function createProductDepot(array $data): ProductDepot { return new ProductDepot($data); }
    public function createProductDepots(array $data): array { return array_map([$this, 'createProductDepot'], $data); }
    public function createProductImportType(array $data): ProductImportType { return new ProductImportType($data); }
    public function createProductImportTypes(array $data): array { return array_map([$this, 'createProductImportType'], $data); }
    public function createProductAttribute(array $data): ProductAttribute { return new ProductAttribute($data); }
    public function createProductAttributes(array $data): array { return array_map([$this, 'createProductAttribute'], $data); }
    public function createProductUnit(array $data): ProductUnit { return new ProductUnit($data); }
    public function createProductUnits(array $data): array { return array_map([$this, 'createProductUnit'], $data); }
    public function createProductGift(array $data): ProductGift { return new ProductGift($data); }
    public function createProductGifts(array $data): array { return array_map([$this, 'createProductGift'], $data); }
    public function createProductImei(array $data): ProductImei { return new ProductImei($data); }
    public function createProductImeis(array $data): array { return array_map([$this, 'createProductImei'], $data); }
    public function createProductExpiry(array $data): ProductExpiry { return new ProductExpiry($data); }
    public function createProductExpiries(array $data): array { return array_map([$this, 'createProductExpiry'], $data); }
    public function createProductImeiHistory(array $data): ProductImeiHistory { return new ProductImeiHistory($data); }
    public function createProductImeiHistories(array $data): array { return array_map([$this, 'createProductImeiHistory'], $data); }
    public function createProductImeiSold(array $data): ProductImeiSold { return new ProductImeiSold($data); }
    public function createProductImeiSolds(array $data): array { return array_map([$this, 'createProductImeiSold'], $data); }
    public function createProductInternalCategory(array $data): ProductInternalCategory { return new ProductInternalCategory($data); }
    public function createProductInternalCategories(array $data): array { return array_map([$this, 'createProductInternalCategory'], $data); }
    public function createProductWebsiteInfo(array $data): ProductWebsiteInfo { return new ProductWebsiteInfo($data); }
    public function createProductWebsiteInfos(array $data): array { return array_map([$this, 'createProductWebsiteInfo'], $data); }
    public function createProductWarranty(array $data): ProductWarranty { return new ProductWarranty($data); }
    public function createProductWarranties(array $data): array { return array_map([$this, 'createProductWarranty'], $data); }

    // Product Add Request/Response methods
    public function createProductAddRequest(array $data): ProductAddRequest { return new ProductAddRequest($data); }
    public function createProductAddRequests(array $data): array { return array_map([$this, 'createProductAddRequest'], $data); }
    public function createProductAddResponse(array $data): ProductAddResponse { return new ProductAddResponse($data); }

    // Validation methods
    public function validateProductData(array $data): bool { try { return $this->createProduct($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductCategoryData(array $data): bool { try { return $this->createProductCategory($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductBrandData(array $data): bool { try { return $this->createProductBrand($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductSupplierData(array $data): bool { try { return $this->createProductSupplier($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductDepotData(array $data): bool { try { return $this->createProductDepot($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductImportTypeData(array $data): bool { try { return $this->createProductImportType($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductAttributeData(array $data): bool { try { return $this->createProductAttribute($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductUnitData(array $data): bool { try { return $this->createProductUnit($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductGiftData(array $data): bool { try { return $this->createProductGift($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductImeiData(array $data): bool { try { return $this->createProductImei($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductExpiryData(array $data): bool { try { return $this->createProductExpiry($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductImeiHistoryData(array $data): bool { try { return $this->createProductImeiHistory($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductImeiSoldData(array $data): bool { try { return $this->createProductImeiSold($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductInternalCategoryData(array $data): bool { try { return $this->createProductInternalCategory($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductWebsiteInfoData(array $data): bool { try { return $this->createProductWebsiteInfo($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductWarrantyData(array $data): bool { try { return $this->createProductWarranty($data)->isValid(); } catch (\Exception $e) { return false; } }
    public function validateProductAddRequestData(array $data): bool { try { return $this->createProductAddRequest($data)->isValid(); } catch (\Exception $e) { return false; } }

    public function validateBatchProductData(array $products): bool {
        foreach ($products as $productData) { if (!$this->validateProductData($productData)) return false; } return true;
    }

    public function validateBatchProductExternalImageData(array $products): bool {
        foreach ($products as $productData) { if (!isset($productData['id']) || !isset($productData['externalImage'])) return false; } return true;
    }

    // Collection analysis methods
    public function getProductStatistics(ProductCollection $collection): array {
        $products = $collection->getProducts();
        return [
            'total' => $products->count(),
            'active' => $products->filter(fn($p) => $p->isActive())->count(),
            'inactive' => $products->filter(fn($p) => $p->isInactive())->count(),
            'outOfStock' => $products->filter(fn($p) => $p->isOutOfStock())->count(),
            'inStock' => $products->filter(fn($p) => $p->isInStock())->count(),
            'lowStock' => $products->filter(fn($p) => $p->isLowStock())->count(),
            'hot' => $products->filter(fn($p) => $p->isHot())->count(),
            'new' => $products->filter(fn($p) => $p->isNew())->count(),
            'home' => $products->filter(fn($p) => $p->isHome())->count()
        ];
    }

    public function getProductPaginationInfo(ProductCollection $collection): array {
        return [
            'totalPages' => $collection->getTotalPages(),
            'currentPage' => $collection->getCurrentPage(),
            'perPage' => $collection->getPerPage(),
            'total' => $collection->getTotal(),
            'from' => $collection->getFrom(),
            'to' => $collection->getTo(),
            'lastPage' => $collection->getLastPage(),
            'hasMorePages' => $collection->getHasMorePages()
        ];
    }

    public function getProductFiltersInfo(ProductCollection $collection): array {
        return [
            'filters' => $collection->getFilters(),
            'sort' => $collection->getSort(),
            'searchTerm' => $collection->getSearchTerm()
        ];
    }

    // Filter methods
    public function filterProductsByCategory(ProductCollection $collection, int $categoryId): Collection { return $collection->getByCategory($categoryId); }
    public function filterProductsByBrand(ProductCollection $collection, int $brandId): Collection { return $collection->getByBrand($brandId); }
    public function filterProductsByStatus(ProductCollection $collection, string $status): Collection { return $collection->getByStatus($status); }
    public function filterActiveProducts(ProductCollection $collection): Collection { return $collection->getActive(); }
    public function filterInactiveProducts(ProductCollection $collection): Collection { return $collection->getInactive(); }
    public function filterOutOfStockProducts(ProductCollection $collection): Collection { return $collection->getOutOfStock(); }
    public function filterInStockProducts(ProductCollection $collection): Collection { return $collection->getInStock(); }
    public function filterLowStockProducts(ProductCollection $collection): Collection { return $collection->getLowStock(); }
    public function filterHotProducts(ProductCollection $collection): Collection { return $collection->getHot(); }
    public function filterNewProducts(ProductCollection $collection): Collection { return $collection->getNew(); }
    public function filterHomeProducts(ProductCollection $collection): Collection { return $collection->getHome(); }
    public function filterProductsByPriceRange(ProductCollection $collection, float $minPrice, float $maxPrice): Collection { return $collection->getByPriceRange($minPrice, $maxPrice); }
    public function filterProductsByInventoryRange(ProductCollection $collection, int $minQuantity, int $maxQuantity): Collection { return $collection->getByInventoryRange($minQuantity, $maxQuantity); }

    // Search and sort methods
    public function searchProducts(ProductCollection $collection, string $term): Collection { return $collection->search($term); }
    public function sortProducts(ProductCollection $collection, string $field, bool $ascending = true): Collection { return $collection->sortBy($field, $ascending); }
    public function paginateProducts(ProductCollection $collection, int $page = 1, int $perPage = 20): array { return $collection->paginate($page, $perPage); }

    // Getter methods
    public function getProductById(ProductCollection $collection, int $id): ?Product { return $collection->getById($id); }
    public function getProductByCode(ProductCollection $collection, string $code): ?Product { return $collection->getByCode($code); }
    public function getProductByName(ProductCollection $collection, string $name): ?Product { return $collection->getByName($name); }
    public function getProductByBarcode(ProductCollection $collection, string $barcode): ?Product { return $collection->getByBarcode($barcode); }
    public function getFirstProduct(ProductCollection $collection): ?Product { return $collection->first(); }
    public function getLastProduct(ProductCollection $collection): ?Product { return $collection->last(); }
    public function getProductAt(ProductCollection $collection, int $index): ?Product { return $collection->get($index); }

    // Statistical methods
    public function getTotalProductValue(ProductCollection $collection): float { return $collection->getProducts()->sum('price'); }
    public function getAverageProductPrice(ProductCollection $collection): float { $products = $collection->getProducts(); return $products->isEmpty() ? 0.0 : ($products->avg('price') ?? 0.0); }
    public function getMinProductPrice(ProductCollection $collection): float { $products = $collection->getProducts(); return $products->isEmpty() ? 0.0 : ($products->min('price') ?? 0.0); }
    public function getMaxProductPrice(ProductCollection $collection): float { $products = $collection->getProducts(); return $products->isEmpty() ? 0.0 : ($products->max('price') ?? 0.0); }
    public function getTotalProductStock(ProductCollection $collection): int { return $collection->getProducts()->sum('availableStock'); }
    public function getAverageProductStock(ProductCollection $collection): float { $products = $collection->getProducts(); return $products->isEmpty() ? 0.0 : ($products->avg('availableStock') ?? 0.0); }
    public function getMinProductStock(ProductCollection $collection): int { $products = $collection->getProducts(); return $products->isEmpty() ? 0 : ($products->min('availableStock') ?? 0); }
    public function getMaxProductStock(ProductCollection $collection): int { $products = $collection->getProducts(); return $products->isEmpty() ? 0 : ($products->max('availableStock') ?? 0); }

    // Cache methods for individual products
    public function cacheProduct(Product $product, int $ttl = 3600): bool { return $this->cacheService->set("product_{$product->getId()}", $product, $ttl); }
    public function getCachedProduct(int $id): ?Product { return $this->cacheService->get("product_{$id}"); }
    public function clearCachedProduct(int $id): bool { return $this->cacheService->delete("product_{$id}"); }

    // Cache methods for collections
    public function cacheProductCollection(ProductCollection $collection, int $ttl = 3600): bool { return $this->cacheService->set("product_collection_{$collection->getId()}", $collection, $ttl); }
    public function getCachedProductCollection(string $key): ?ProductCollection { return $this->cacheService->get("product_collection_{$key}"); }
    public function clearCachedProductCollection(string $key): bool { return $this->cacheService->delete("product_collection_{$key}"); }

    // Cache methods for static data (24h TTL)
    public function cacheProductCategories(array $categories, int $ttl = 86400): bool { return $this->cacheService->set('product_categories', $categories, $ttl); }
    public function getCachedProductCategories(): ?array { return $this->cacheService->get('product_categories'); }
    public function clearCachedProductCategories(): bool { return $this->cacheService->delete('product_categories'); }

    public function cacheProductBrands(array $brands, int $ttl = 86400): bool { return $this->cacheService->set('product_brands', $brands, $ttl); }
    public function getCachedProductBrands(): ?array { return $this->cacheService->get('product_brands'); }
    public function clearCachedProductBrands(): bool { return $this->cacheService->delete('product_brands'); }

    public function cacheProductTypes(array $types, int $ttl = 86400): bool { return $this->cacheService->set('product_types', $types, $ttl); }
    public function getCachedProductTypes(): ?array { return $this->cacheService->get('product_types'); }
    public function clearCachedProductTypes(): bool { return $this->cacheService->delete('product_types'); }

    public function cacheProductSuppliers(array $suppliers, int $ttl = 86400): bool { return $this->cacheService->set('product_suppliers', $suppliers, $ttl); }
    public function getCachedProductSuppliers(): ?array { return $this->cacheService->get('product_suppliers'); }
    public function clearCachedProductSuppliers(): bool { return $this->cacheService->delete('product_suppliers'); }

    public function cacheProductDepots(array $depots, int $ttl = 86400): bool { return $this->cacheService->set('product_depots', $depots, $ttl); }
    public function getCachedProductDepots(): ?array { return $this->cacheService->get('product_depots'); }
    public function clearCachedProductDepots(): bool { return $this->cacheService->delete('product_depots'); }

    public function cacheProductImportTypes(array $importTypes, int $ttl = 86400): bool { return $this->cacheService->set('product_import_types', $importTypes, $ttl); }
    public function getCachedProductImportTypes(): ?array { return $this->cacheService->get('product_import_types'); }
    public function clearCachedProductImportTypes(): bool { return $this->cacheService->delete('product_import_types'); }

    public function cacheProductAttributes(array $attributes, int $ttl = 86400): bool { return $this->cacheService->set('product_attributes', $attributes, $ttl); }
    public function getCachedProductAttributes(): ?array { return $this->cacheService->get('product_attributes'); }
    public function clearCachedProductAttributes(): bool { return $this->cacheService->delete('product_attributes'); }

    public function cacheProductUnits(array $units, int $ttl = 86400): bool { return $this->cacheService->set('product_units', $units, $ttl); }
    public function getCachedProductUnits(): ?array { return $this->cacheService->get('product_units'); }
    public function clearCachedProductUnits(): bool { return $this->cacheService->delete('product_units'); }

    public function cacheProductGifts(array $gifts, int $ttl = 86400): bool { return $this->cacheService->set('product_gifts', $gifts, $ttl); }
    public function getCachedProductGifts(): ?array { return $this->cacheService->get('product_gifts'); }
    public function clearCachedProductGifts(): bool { return $this->cacheService->delete('product_gifts'); }

    public function cacheProductImeis(array $imeis, int $ttl = 86400): bool { return $this->cacheService->set('product_imeis', $imeis, $ttl); }
    public function getCachedProductImeis(): ?array { return $this->cacheService->get('product_imeis'); }
    public function clearCachedProductImeis(): bool { return $this->cacheService->delete('product_imeis'); }

    public function cacheProductExpiries(array $expiries, int $ttl = 86400): bool { return $this->cacheService->set('product_expiries', $expiries, $ttl); }
    public function getCachedProductExpiries(): ?array { return $this->cacheService->get('product_expiries'); }
    public function clearCachedProductExpiries(): bool { return $this->cacheService->delete('product_expiries'); }

    public function cacheProductImeiHistories(array $histories, int $ttl = 86400): bool { return $this->cacheService->set('product_imei_histories', $histories, $ttl); }
    public function getCachedProductImeiHistories(): ?array { return $this->cacheService->get('product_imei_histories'); }
    public function clearCachedProductImeiHistories(): bool { return $this->cacheService->delete('product_imei_histories'); }

    public function cacheProductImeiSolds(array $solds, int $ttl = 86400): bool { return $this->cacheService->set('product_imei_solds', $solds, $ttl); }
    public function getCachedProductImeiSolds(): ?array { return $this->cacheService->get('product_imei_solds'); }
    public function clearCachedProductImeiSolds(): bool { return $this->cacheService->delete('product_imei_solds'); }

    public function cacheProductInternalCategories(array $categories, int $ttl = 86400): bool { return $this->cacheService->set('product_internal_categories', $categories, $ttl); }
    public function getCachedProductInternalCategories(): ?array { return $this->cacheService->get('product_internal_categories'); }
    public function clearCachedProductInternalCategories(): bool { return $this->cacheService->delete('product_internal_categories'); }

    public function cacheProductWebsiteInfos(array $infos, int $ttl = 86400): bool { return $this->cacheService->set('product_website_infos', $infos, $ttl); }
    public function getCachedProductWebsiteInfos(): ?array { return $this->cacheService->get('product_website_infos'); }
    public function clearCachedProductWebsiteInfos(): bool { return $this->cacheService->delete('product_website_infos'); }

    public function cacheProductWarranties(array $warranties, int $ttl = 86400): bool { return $this->cacheService->set('product_warranties', $warranties, $ttl); }
    public function getCachedProductWarranties(): ?array { return $this->cacheService->get('product_warranties'); }
    public function clearCachedProductWarranties(): bool { return $this->cacheService->delete('product_warranties'); }
}
