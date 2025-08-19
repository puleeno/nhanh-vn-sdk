<?php

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Managers\ProductManager;
use Puleeno\NhanhVn\Services\HttpService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;
use Puleeno\NhanhVn\Entities\Product\Product;
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
use Illuminate\Support\Collection;
use Exception;

/**
 * Product Module
 */
class ProductModule
{
    protected ProductManager $productManager;
    protected HttpService $httpService;
    protected LoggerInterface $logger;

    public function __construct(ProductManager $productManager, HttpService $httpService, LoggerInterface $logger)
    {
        $this->productManager = $productManager;
        $this->httpService = $httpService;
        $this->logger = $logger;
    }

        /**
     * Tìm kiếm sản phẩm
     */
    public function search(array $criteria = []): Collection
    {
        // DEBUG: Log search criteria
        $this->logger->debug("ProductModule::search() called with criteria", $criteria);

        try {
            // Chuẩn bị search criteria theo API Nhanh.vn
            $searchData = $this->prepareSearchCriteria($criteria);

            // Gọi API Nhanh.vn
            $this->logger->info("ProductModule::search() calling Nhanh.vn API", $searchData);

            $response = $this->httpService->callApi('/product/search', $searchData);

                        // Parse response
            if (!isset($response['data']) || !isset($response['data']['products'])) {
                $this->logger->warning("ProductModule::search() - Response không có products data");
                // Giải phóng memory
                unset($response, $searchData);
                return new Collection([]);
            }

            $this->logger->info("ProductModule::search() - Found " . count($response['data']['products']) . " products from API");

            // Sử dụng helper method để tạo entities với memory management
            $productEntities = $this->createEntitiesFromApiResponse($response, 'createProduct', 'products');

            $this->logger->info("ProductModule::search() - Created " . count($productEntities) . " product entities");

            // Giải phóng memory trước khi return
            unset($response, $searchData);

            return new Collection($productEntities);

        } catch (Exception $e) {
            $this->logger->error("ProductModule::search() error", ['error' => $e->getMessage()]);
            // Giải phóng memory trong trường hợp lỗi
            if (isset($response)) unset($response);
            if (isset($searchData)) unset($searchData);
            throw $e;
        }
    }

    /**
     * Chuẩn bị search criteria theo format API Nhanh.vn
     */
    private function prepareSearchCriteria(array $criteria): array
    {
        $searchData = [];

        // Các field cơ bản
        if (isset($criteria['page'])) {
            $searchData['page'] = (int) $criteria['page'];
        }

        if (isset($criteria['limit'])) {
            $searchData['icpp'] = min((int) $criteria['limit'], 100); // Tối đa 100
        }

        if (isset($criteria['sort'])) {
            $searchData['sort'] = $criteria['sort'];
        }

        if (isset($criteria['name'])) {
            $searchData['name'] = $criteria['name'];
        }

        if (isset($criteria['parentId'])) {
            $searchData['parentId'] = (int) $criteria['parentId'];
        }

        if (isset($criteria['categoryId'])) {
            $searchData['categoryId'] = (int) $criteria['categoryId'];
        }

        if (isset($criteria['status'])) {
            $searchData['status'] = $criteria['status'];
        }

        if (isset($criteria['priceFrom'])) {
            $searchData['priceFrom'] = (float) $criteria['priceFrom'];
        }

        if (isset($criteria['priceTo'])) {
            $searchData['priceTo'] = (float) $criteria['priceTo'];
        }

        if (isset($criteria['brandId'])) {
            $searchData['brandId'] = (int) $criteria['brandId'];
        }

        if (isset($criteria['imei'])) {
            $searchData['imei'] = $criteria['imei'];
        }

        if (isset($criteria['showHot'])) {
            $searchData['showHot'] = (int) $criteria['showHot'];
        }

        if (isset($criteria['showNew'])) {
            $searchData['showNew'] = (int) $criteria['showNew'];
        }

        if (isset($criteria['showHome'])) {
            $searchData['showHome'] = (int) $criteria['showHome'];
        }

        if (isset($criteria['updatedDateTimeFrom'])) {
            $searchData['updatedDateTimeFrom'] = $criteria['updatedDateTimeFrom'];
        }

        if (isset($criteria['updatedDateTimeTo'])) {
            $searchData['updatedDateTimeTo'] = $criteria['updatedDateTimeTo'];
        }

        return $searchData;
    }

    /**
     * Lấy chi tiết sản phẩm
     */
    public function detail(int $productId): ?Product
    {
        // TODO: Implement API call to get product detail
        return null;
    }

    /**
     * Thêm sản phẩm mới
     */
    public function add(array $productData): Product
    {
        return $this->productManager->createProduct($productData);
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(int $productId, array $productData): ?Product
    {
        // TODO: Implement API call to update product
        return null;
    }

    /**
     * Xóa sản phẩm
     */
    public function delete(int $productId): bool
    {
        // TODO: Implement API call to delete product
        return false;
    }

    /**
     * Lấy sản phẩm theo danh mục
     */
    public function getByCategory(int $categoryId, array $options = []): Collection
    {
        return $this->productManager->getProductsByCategory($categoryId, $options);
    }

    /**
     * Lấy sản phẩm theo thương hiệu
     */
    public function getByBrand(int $brandId, array $options = []): Collection
    {
        return $this->productManager->getProductsByBrand($brandId, $options);
    }

    /**
     * Lấy sản phẩm nổi bật
     */
    public function getHot(int $limit = 10): Collection
    {
        return $this->productManager->getHotProducts($limit);
    }

    /**
     * Lấy sản phẩm mới
     */
    public function getNew(int $limit = 10): Collection
    {
        return $this->productManager->getNewProducts($limit);
    }

    /**
     * Lấy sản phẩm trang chủ
     */
    public function getHome(int $limit = 20): Collection
    {
        return $this->productManager->getHomeProducts($limit);
    }

    /**
     * Lấy sản phẩm sắp hết hàng
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->productManager->getLowStockProducts($threshold);
    }

    /**
     * Lấy sản phẩm hết hàng
     */
    public function getOutOfStock(): Collection
    {
        return $this->productManager->getOutOfStockProducts();
    }

    /**
     * Lấy sản phẩm liên quan
     */
    public function getRelated(Product $product, int $limit = 5): Collection
    {
        return $this->productManager->getRelatedProducts($product, $limit);
    }

    /**
     * Lấy sản phẩm bán chạy
     */
    public function getBestSelling(int $limit = 10): Collection
    {
        return $this->productManager->getBestSellingProducts($limit);
    }

    /**
     * Lấy sản phẩm giảm giá
     */
    public function getDiscounted(int $limit = 10): Collection
    {
        return $this->productManager->getDiscountedProducts($limit);
    }

    /**
     * Lấy sản phẩm theo khoảng giá
     */
    public function getByPriceRange(float $minPrice, float $maxPrice, array $options = []): Collection
    {
        return $this->productManager->getProductsByPriceRange($minPrice, $maxPrice, $options);
    }

    /**
     * Lấy thống kê sản phẩm
     */
    public function getStatistics(): array
    {
        return $this->productManager->getAllProductStatistics();
    }

        /**
     * Lấy danh mục sản phẩm
     */
    public function getCategories(): array
    {
        try {
            $this->logger->info("ProductModule::getCategories() - Calling Nhanh.vn API");

            // Gọi API Nhanh.vn để lấy categories
            $response = $this->httpService->callApi('/product/category', []);

            if (!isset($response['data']) || !is_array($response['data'])) {
                $this->logger->warning("ProductModule::getCategories() - Response không có categories data");
                // Giải phóng memory
                unset($response);
                return [];
            }

            $this->logger->info("ProductModule::getCategories() - Found " . count($response['data']) . " categories from API");

            // Sử dụng helper method để tạo entities với memory management
            $categoryEntities = $this->createEntitiesFromApiResponse($response, 'createProductCategory');

            $this->logger->info("ProductModule::getCategories() - Created " . count($categoryEntities) . " category entities");

            return $categoryEntities;

        } catch (Exception $e) {
            $this->logger->error("ProductModule::getCategories() error", ['error' => $e->getMessage()]);
            // Fallback to cached data if available
            $cachedCategories = $this->productManager->getCachedProductCategories();
            if ($cachedCategories !== null) {
                $this->logger->info("ProductModule::getCategories() - Using cached categories");
                return $this->createEntitiesWithMemoryManagement($cachedCategories, 'createProductCategories');
            }
            return [];
        }
    }

    /**
     * Lấy thương hiệu sản phẩm
     */
    public function getBrands(): array
    {
        $cachedBrands = $this->productManager->getCachedProductBrands();
        return $this->createEntitiesWithMemoryManagement($cachedBrands, 'createProductBrands');
    }

    /**
     * Lấy loại sản phẩm
     */
    public function getTypes(): array
    {
        $cachedTypes = $this->productManager->getCachedProductTypes();
        return $this->createEntitiesWithMemoryManagement($cachedTypes, 'createProductTypes');
    }

    /**
     * Lấy nhà cung cấp
     */
    public function getSuppliers(): array
    {
        $cachedSuppliers = $this->productManager->getCachedProductSuppliers();
        return $this->createEntitiesWithMemoryManagement($cachedSuppliers, 'createProductSuppliers');
    }

    /**
     * Lấy kho hàng
     */
    public function getDepots(): array
    {
        $cachedDepots = $this->productManager->getCachedProductDepots();

        if ($cachedDepots !== null) {
            return $this->productManager->createProductDepots($cachedDepots);
        }

        // TODO: Implement API call to get depots
        return [];
    }

    /**
     * Lấy kiểu nhập kho
     */
    public function getImportTypes(): array
    {
        $cachedImportTypes = $this->productManager->getCachedProductImportTypes();

        if ($cachedImportTypes !== null) {
            return $this->productManager->createProductImportTypes($cachedImportTypes);
        }

        // TODO: Implement API call to get import types
        return [];
    }

    /**
     * Lấy thuộc tính sản phẩm
     */
    public function getAttributes(): array
    {
        $cachedAttributes = $this->productManager->getCachedProductAttributes();

        if ($cachedAttributes !== null) {
            return $this->productManager->createProductAttributes($cachedAttributes);
        }

        // TODO: Implement API call to get attributes
        return [];
    }

    /**
     * Lấy đơn vị sản phẩm
     */
    public function getUnits(): array
    {
        $cachedUnits = $this->productManager->getCachedProductUnits();

        if ($cachedUnits !== null) {
            return $this->productManager->createProductUnits($cachedUnits);
        }

        // TODO: Implement API call to get units
        return [];
    }

    /**
     * Lấy quà tặng sản phẩm
     */
    public function getGifts(): array
    {
        $cachedGifts = $this->productManager->getCachedProductGifts();

        if ($cachedGifts !== null) {
            return $this->productManager->createProductGifts($cachedGifts);
        }

        // TODO: Implement API call to get gifts
        return [];
    }

    /**
     * Lấy IMEI sản phẩm
     */
    public function getImeis(): array
    {
        $cachedImeis = $this->productManager->getCachedProductImeis();

        if ($cachedImeis !== null) {
            return $this->productManager->createProductImeis($cachedImeis);
        }

        // TODO: Implement API call to get IMEIs
        return [];
    }

    /**
     * Lấy hạn sử dụng sản phẩm
     */
    public function getExpiries(): array
    {
        $cachedExpiries = $this->productManager->getCachedProductExpiries();

        if ($cachedExpiries !== null) {
            return $this->productManager->createProductExpiries($cachedExpiries);
        }

        // TODO: Implement API call to get expiries
        return [];
    }

    /**
     * Lấy lịch sử IMEI
     */
    public function getImeiHistories(): array
    {
        $cachedHistories = $this->productManager->getCachedProductImeiHistories();

        if ($cachedHistories !== null) {
            return $this->productManager->createProductImeiHistories($cachedHistories);
        }

        // TODO: Implement API call to get IMEI histories
        return [];
    }

    /**
     * Lấy IMEI đã bán
     */
    public function getImeiSolds(): array
    {
        $cachedSolds = $this->productManager->getCachedProductImeiSolds();

        if ($cachedSolds !== null) {
            return $this->productManager->createProductImeiSolds($cachedSolds);
        }

        // TODO: Implement API call to get IMEI solds
        return [];
    }

    /**
     * Lấy danh mục nội bộ
     */
    public function getInternalCategories(): array
    {
        $cachedCategories = $this->productManager->getCachedProductInternalCategories();

        if ($cachedCategories !== null) {
            return $this->productManager->createProductInternalCategories($cachedCategories);
        }

        // TODO: Implement API call to get internal categories
        return [];
    }

    /**
     * Lấy thông tin website
     */
    public function getWebsiteInfos(): array
    {
        $cachedInfos = $this->productManager->getCachedProductWebsiteInfos();

        if ($cachedInfos !== null) {
            return $this->productManager->createProductWebsiteInfos($cachedInfos);
        }

        // TODO: Implement API call to get website infos
        return [];
    }

    /**
     * Lấy bảo hành sản phẩm
     */
    public function getWarranties(): array
    {
        $cachedWarranties = $this->productManager->getCachedProductWarranties();

        if ($cachedWarranties !== null) {
            return $this->productManager->createProductWarranties($cachedWarranties);
        }

        // TODO: Implement API call to get warranties
        return [];
    }

    /**
     * Lấy trạng thái cache
     */
    public function getCacheStatus(): array
    {
        return $this->productManager->getProductCacheStatus();
    }

    /**
     * Xóa tất cả cache
     */
    public function clearCache(): bool
    {
        return $this->productManager->clearAllProductCache();
    }

    /**
     * Kiểm tra cache có sẵn không
     */
    public function isCacheAvailable(): bool
    {
        return $this->productManager->isProductCacheAvailable();
    }

    /**
     * Lấy ProductManager instance
     */
    public function getManager(): ProductManager
    {
        return $this->productManager;
    }

    /**
     * Helper method để giải phóng memory cho cached data
     */
    private function createEntitiesWithMemoryManagement(array $cachedData, string $methodName): array
    {
        if ($cachedData === null) {
            return [];
        }

        try {
            $result = $this->productManager->$methodName($cachedData);
            // Giải phóng memory ngay lập tức
            unset($cachedData);
            return $result;
        } catch (Exception $e) {
            $this->logger->error("ProductModule::$methodName() - Error creating entities", ['error' => $e->getMessage()]);
            unset($cachedData);
            return [];
        }
    }

    /**
     * Helper method để giải phóng memory cho API response data
     */
    private function createEntitiesFromApiResponse(array $responseData, string $methodName, string $dataKey = 'data'): array
    {
        if (!isset($responseData[$dataKey]) || !is_array($responseData[$dataKey])) {
            unset($responseData);
            return [];
        }

        $data = $responseData[$dataKey];
        $entities = [];

        foreach ($data as $itemData) {
            try {
                $entity = $this->productManager->$methodName($itemData);
                $entities[] = $entity;
                // Giải phóng memory ngay sau khi tạo entity
                unset($itemData);
            } catch (Exception $e) {
                $this->logger->error("ProductModule::$methodName() - Error creating entity", ['error' => $e->getMessage()]);
                // Skip invalid data
            }
        }

        // Giải phóng memory
        unset($responseData, $data);

        return $entities;
    }
}
