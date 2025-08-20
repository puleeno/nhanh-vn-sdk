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
use Puleeno\NhanhVn\Entities\Product\ProductAddRequest;
use Puleeno\NhanhVn\Entities\Product\ProductAddResponse;
use Puleeno\NhanhVn\Entities\Product\ProductExternalImageRequest;
use Puleeno\NhanhVn\Entities\Product\ProductExternalImageResponse;
use Illuminate\Support\Collection;
use Exception;

/**
 * Product Module - Quản lý các thao tác liên quan đến sản phẩm
 *
 * Module này cung cấp các method để tương tác với API sản phẩm của Nhanh.vn
 * bao gồm: tìm kiếm, lấy chi tiết, quản lý cache và các thao tác khác
 */
class ProductModule
{
    /** @var ProductManager Quản lý business logic sản phẩm */
    protected ProductManager $productManager;

    /** @var HttpService Service gọi HTTP API */
    protected HttpService $httpService;

    /** @var LoggerInterface Logger để ghi log */
    protected LoggerInterface $logger;

    /**
     * Constructor của ProductModule
     *
     * @param ProductManager $productManager Quản lý business logic sản phẩm
     * @param HttpService $httpService Service gọi HTTP API
     * @param LoggerInterface $logger Logger để ghi log
     */
    public function __construct(ProductManager $productManager, HttpService $httpService, LoggerInterface $logger)
    {
        $this->productManager = $productManager;
        $this->httpService = $httpService;
        $this->logger = $logger;
    }

        /**
     * Tìm kiếm sản phẩm theo các tiêu chí
     *
     * @param array $criteria Các tiêu chí tìm kiếm
     * @return Collection Collection các sản phẩm tìm được
     * @throws Exception Khi có lỗi xảy ra trong quá trình tìm kiếm
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
            $productEntities = $this->createEntitiesFromApiResponse($response['data'], 'createProduct', 'products');

            $this->logger->info("ProductModule::search() - Created " . count($productEntities) . " product entities");

            // Giải phóng memory trước khi return
            unset($response, $searchData);

            return new Collection($productEntities);
        } catch (Exception $e) {
            $this->logger->error("ProductModule::search() error", ['error' => $e->getMessage()]);
            // Giải phóng memory trong trường hợp lỗi
            if (isset($response)) {
                unset($response);
            }
            if (isset($searchData)) {
                unset($searchData);
            }
            throw $e;
        }
    }

    /**
     * Chuẩn bị search criteria theo format API Nhanh.vn
     *
     * @param array $criteria Các tiêu chí tìm kiếm từ người dùng
     * @return array Các tiêu chí đã được format theo chuẩn API Nhanh.vn
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
     * Lấy chi tiết sản phẩm theo ID
     *
     * @param int $productId ID sản phẩm trên Nhanh.vn
     * @return Product|null Sản phẩm chi tiết hoặc null nếu không tìm thấ
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *


















































































































































































    /**
     * Lấy chi tiết sản phẩm theo ID
     *
     * @param int $productId ID sản phẩm trên Nhanh.vn
     * @return Product|null Sản phẩm chi tiết hoặc null nếu không tìm thấy
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy chi tiết
     */
    public function detail(int $productId): ?Product
    {
        $this->logger->debug("ProductModule::detail() called with productId", ['productId' => $productId]);

        try {
            // Gọi API Nhanh.vn để lấy chi tiết sản phẩm
            $this->logger->info("ProductModule::detail() calling Nhanh.vn API", ['productId' => $productId]);

            $response = $this->httpService->callApi('/product/detail', $productId);

            // Parse response
            if (!isset($response['data'])) {
                $this->logger->warning("ProductModule::detail() - Response không có 'data' field", [
                    'response_keys' => array_keys($response ?? []),
                    'response' => $response
                ]);
                unset($response);
                return null;
            }

            // Kiểm tra cấu trúc response
            $this->logger->debug("ProductModule::detail() - Response structure", [
                'data_type' => gettype($response['data']),
                'data_content' => $response['data']
            ]);

            // API detail có thể trả về data trực tiếp hoặc trong array
            $productData = null;
            if (is_array($response['data'])) {
                if (empty($response['data'])) {
                    $this->logger->warning("ProductModule::detail() - Response data array rỗng");
                    unset($response);
                    return null;
                }
                // Nếu data là array, lấy phần tử đầu tiên một cách an toàn
                $firstKey = array_key_first($response['data']);
                if ($firstKey !== null) {
                    $productData = $response['data'][$firstKey];
                } else {
                    $this->logger->warning("ProductModule::detail() - Không thể lấy key đầu tiên từ data array");
                    unset($response);
                    return null;
                }
            } else {
                // Nếu data không phải array, có thể là object hoặc data trực tiếp
                $productData = $response['data'];
            }

            // Kiểm tra productData có hợp lệ không
            if ($productData === null) {
                $this->logger->warning("ProductModule::detail() - Product data là null");
                unset($response);
                return null;
            }

            // Nếu productData không phải array, cố gắng convert
            if (!is_array($productData)) {
                if (is_object($productData)) {
                    $productData = (array) $productData;
                } else {
                    $this->logger->warning("ProductModule::detail() - Product data không phải array hoặc object", [
                        'productData' => $productData,
                        'productData_type' => gettype($productData)
                    ]);
                    unset($response);
                    return null;
                }
            }

            $this->logger->info("ProductModule::detail() - Found product data", [
                'productId' => $productId,
                'data_keys' => array_keys($productData)
            ]);

            // Tạo Product entity
            $product = $this->productManager->createProduct($productData);

            $this->logger->info("ProductModule::detail() - Created product entity", ['productId' => $productId]);

            // Giải phóng memory
            unset($response, $productData);

            return $product;
        } catch (Exception $e) {
            $this->logger->error("ProductModule::detail() error", ['error' => $e->getMessage(), 'productId' => $productId]);
            // Giải phóng memory trong trường hợp lỗi
            if (isset($response)) {
                unset($response);
            }
            if (isset($productData)) {
                unset($productData);
            }
            throw $e;
        }
    }

    /**
     * Thêm sản phẩm mới hoặc cập nhật sản phẩm hiện có
     *
     * @param array|ProductAddRequest $productData Dữ liệu sản phẩm hoặc ProductAddRequest object
     * @return ProductAddResponse Response từ API
     * @throws Exception Khi có lỗi xảy ra
     */
    public function add($productData): ProductAddResponse
    {
        $this->logger->info("ProductModule::add() called", ['data' => $productData]);

        try {
            // Convert array to ProductAddRequest if needed
            if (is_array($productData)) {
                $productData = $this->productManager->createProductAddRequest($productData);
            }

            // Validate request data
            if (!$productData->isValid()) {
                $errors = $productData->getErrors();
                $this->logger->error("ProductModule::add() - Validation failed", ['errors' => $errors]);
                throw new Exception('Dữ liệu sản phẩm không hợp lệ: ' . json_encode($errors));
            }

            // Prepare API request data
            $apiData = $productData->toApiFormat();
            $this->logger->debug("ProductModule::add() - API request data", $apiData);

            // Call Nhanh.vn API
            $response = $this->httpService->callApi('/product/add', $apiData);

            // Parse response
            if (!isset($response['data'])) {
                $this->logger->warning("ProductModule::add() - Response không có data");
                throw new Exception('API response không hợp lệ');
            }

            // Create response entity
            $addResponse = $this->productManager->createProductAddResponse($response['data']);

            $this->logger->info("ProductModule::add() - Success", [
                'system_id' => $productData->getId(),
                'nhanh_id' => $addResponse->getNhanhId($productData->getId())
            ]);

            // Clear cache for this product
            $this->clearProductCache($productData->getId());

            return $addResponse;
        } catch (Exception $e) {
            $this->logger->error("ProductModule::add() error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Thêm nhiều sản phẩm cùng lúc (batch add)
     *
     * @param array $productsData Mảng dữ liệu sản phẩm
     * @return ProductAddResponse Response từ API
     * @throws Exception Khi có lỗi xảy ra
     */
    public function addBatch(array $productsData): ProductAddResponse
    {
        $this->logger->info("ProductModule::addBatch() called", ['count' => count($productsData)]);

        if (empty($productsData)) {
            throw new Exception('Danh sách sản phẩm không được để trống');
        }

        if (count($productsData) > 300) {
            throw new Exception('Chỉ được thêm tối đa 300 sản phẩm mỗi lần');
        }

        try {
            // Validate all products
            $requests = [];
            foreach ($productsData as $index => $productData) {
                $request = $this->productManager->createProductAddRequest($productData);

                if (!$request->isValid()) {
                    $errors = $request->getErrors();
                    $this->logger->error("ProductModule::addBatch() - Product {$index} validation failed", ['errors' => $errors]);
                    throw new Exception("Sản phẩm thứ {$index} không hợp lệ: " . json_encode($errors));
                }

                $requests[] = $request;
            }

            // Prepare batch API request data
            $batchData = [];
            foreach ($requests as $request) {
                $batchData[] = $request->toApiFormat();
            }

            $this->logger->debug("ProductModule::addBatch() - Batch API request data", ['count' => count($batchData)]);

            // Call Nhanh.vn batch API
            $response = $this->httpService->callApi('/product/add', $batchData);

            // Parse response
            if (!isset($response['data'])) {
                $this->logger->warning("ProductModule::addBatch() - Response không có data");
                throw new Exception('API response không hợp lệ');
            }

            // Create response entity
            $addResponse = $this->productManager->createProductAddResponse($response['data']);

            $this->logger->info("ProductModule::addBatch() - Success", [
                'total_products' => $addResponse->getTotalProducts(),
                'success_count' => $addResponse->getSuccessCount(),
                'success_rate' => $addResponse->getSuccessRate()
            ]);

            // Clear cache for all products
            foreach ($requests as $request) {
                $this->clearProductCache($request->getId());
            }

            // Clean up memory
            unset($requests, $batchData);

            return $addResponse;
        } catch (Exception $e) {
            $this->logger->error("ProductModule::addBatch() error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Cập nhật sản phẩm theo ID
     *
     * @param int $productId ID sản phẩm cần cập nhật
     * @param array $productData Dữ liệu mới của sản phẩm
     * @return Product|null Sản phẩm đã được cập nhật hoặc null nếu không tìm thấy
     */
    public function update(int $productId, array $productData): ?Product
    {
        // TODO: Implement API call to update product
        return null;
    }

    /**
     * Xóa sản phẩm theo ID
     *
     * @param int $productId ID sản phẩm cần xóa
     * @return bool True nếu xóa thành công, false nếu thất bại
     */
    public function delete(int $productId): bool
    {
        // TODO: Implement API call to delete product
        return false;
    }

    /**
     * Lấy sản phẩm theo danh mục
     *
     * @param int $categoryId ID danh mục sản phẩm
     * @param array $options Các tùy chọn bổ sung (limit, offset, sort...)
     * @return Collection Collection các sản phẩm trong danh mục
     */
    public function getByCategory(int $categoryId, array $options = []): Collection
    {
        return $this->productManager->getProductsByCategory($categoryId, $options);
    }

    /**
     * Lấy sản phẩm theo thương hiệu
     *
     * @param int $brandId ID thương hiệu sản phẩm
     * @param array $options Các tùy chọn bổ sung (limit, offset, sort...)
     * @return Collection Collection các sản phẩm của thương hiệu
     */
    public function getByBrand(int $brandId, array $options = []): Collection
    {
        return $this->productManager->getProductsByBrand($brandId, $options);
    }

    /**
     * Lấy sản phẩm nổi bật (hot)
     *
     * @param int $limit Số lượng sản phẩm tối đa (mặc định: 10)
     * @return Collection Collection các sản phẩm nổi bật
     */
    public function getHot(int $limit = 10): Collection
    {
        return $this->productManager->getHotProducts($limit);
    }

    /**
     * Lấy sản phẩm mới
     *
     * @param int $limit Số lượng sản phẩm tối đa (mặc định: 10)
     * @return Collection Collection các sản phẩm mới
     */
    public function getNew(int $limit = 10): Collection
    {
        return $this->productManager->getNewProducts($limit);
    }

    /**
     * Lấy sản phẩm hiển thị trên trang chủ
     *
     * @param int $limit Số lượng sản phẩm tối đa (mặc định: 20)
     * @return Collection Collection các sản phẩm trang chủ
     */
    public function getHome(int $limit = 20): Collection
    {
        return $this->productManager->getHomeProducts($limit);
    }

    /**
     * Lấy sản phẩm sắp hết hàng (tồn kho thấp)
     *
     * @param int $threshold Ngưỡng tồn kho tối thiểu (mặc định: 10)
     * @return Collection Collection các sản phẩm sắp hết hàng
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->productManager->getLowStockProducts($threshold);
    }

    /**
     * Lấy sản phẩm hết hàng (tồn kho = 0)
     *
     * @return Collection Collection các sản phẩm hết hàng
     */
    public function getOutOfStock(): Collection
    {
        return $this->productManager->getOutOfStockProducts();
    }

    /**
     * Lấy sản phẩm liên quan đến sản phẩm hiện tại
     *
     * @param Product $product Sản phẩm gốc để tìm sản phẩm liên quan
     * @param int $limit Số lượng sản phẩm liên quan tối đa (mặc định: 5)
     * @return Collection Collection các sản phẩm liên quan
     */
    public function getRelated(Product $product, int $limit = 5): Collection
    {
        return $this->productManager->getRelatedProducts($product, $limit);
    }

    /**
     * Lấy sản phẩm bán chạy nhất
     *
     * @param int $limit Số lượng sản phẩm tối đa (mặc định: 10)
     * @return Collection Collection các sản phẩm bán chạy
     */
    public function getBestSelling(int $limit = 10): Collection
    {
        return $this->productManager->getBestSellingProducts($limit);
    }

    /**
     * Lấy sản phẩm đang giảm giá
     *
     * @param int $limit Số lượng sản phẩm tối đa (mặc định: 10)
     * @return Collection Collection các sản phẩm giảm giá
     */
    public function getDiscounted(int $limit = 10): Collection
    {
        return $this->productManager->getDiscountedProducts($limit);
    }

    /**
     * Lấy sản phẩm theo khoảng giá
     *
     * @param float $minPrice Giá tối thiểu
     * @param float $maxPrice Giá tối đa
     * @param array $options Các tùy chọn bổ sung (limit, offset, sort...)
     * @return Collection Collection các sản phẩm trong khoảng giá
     */
    public function getByPriceRange(float $minPrice, float $maxPrice, array $options = []): Collection
    {
        return $this->productManager->getProductsByPriceRange($minPrice, $maxPrice, $options);
    }

    /**
     * Lấy thống kê tổng quan về sản phẩm
     *
     * @return array Mảng chứa các thống kê sản phẩm
     */
    public function getStatistics(): array
    {
        return $this->productManager->getAllProductStatistics();
    }

    /**
     * Lấy danh mục sản phẩm từ API Nhanh.vn
     *
     * @return array Mảng các danh mục sản phẩm
     * @throws Exception Khi có lỗi xảy ra trong quá trình lấy danh mục
     */
    public function getCategories(): array
    {
        try {
            $this->logger->info("ProductModule::getCategories() - Calling Nhanh.vn API");

            // Gọi API Nhanh.vn để lấy categories
            $response = $this->httpService->callApi('/product/category', []);

            // DEBUG: Log full response để kiểm tra structure
            $this->logger->info("ProductModule::getCategories() - Full API Response", ['response' => $response]);

            if (!isset($response['data']) || !is_array($response['data'])) {
                $this->logger->warning("ProductModule::getCategories() - Response không có categories data", [
                    'response_keys' => array_keys($response ?? []),
                    'has_data' => isset($response['data']),
                    'data_type' => gettype($response['data'] ?? null)
                ]);
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
     * Lấy danh sách thương hiệu sản phẩm
     *
     * @return array Mảng các thương hiệu sản phẩm
     */
    public function getBrands(): array
    {
        $cachedBrands = $this->productManager->getCachedProductBrands();
        return $this->createEntitiesWithMemoryManagement($cachedBrands, 'createProductBrands');
    }

    /**
     * Lấy danh sách loại sản phẩm
     *
     * @return array Mảng các loại sản phẩm
     */
    public function getTypes(): array
    {
        $cachedTypes = $this->productManager->getCachedProductTypes();
        return $this->createEntitiesWithMemoryManagement($cachedTypes, 'createProductTypes');
    }

    /**
     * Lấy danh sách nhà cung cấp
     *
     * @return array Mảng các nhà cung cấp
     */
    public function getSuppliers(): array
    {
        $cachedSuppliers = $this->productManager->getCachedProductSuppliers();
        return $this->createEntitiesWithMemoryManagement($cachedSuppliers, 'createProductSuppliers');
    }

    /**
     * Lấy danh sách kho hàng
     *
     * @return array Mảng các kho hàng
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
     * Lấy danh sách kiểu nhập kho
     *
     * @return array Mảng các kiểu nhập kho
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
     * Lấy danh sách thuộc tính sản phẩm
     *
     * @return array Mảng các thuộc tính sản phẩm
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
     * Lấy danh sách đơn vị sản phẩm
     *
     * @return array Mảng các đơn vị sản phẩm
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
     * Lấy danh sách quà tặng sản phẩm
     *
     * @return array Mảng các quà tặng sản phẩm
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
     * Lấy danh sách IMEI sản phẩm
     *
     * @return array Mảng các IMEI sản phẩm
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
     * Lấy danh sách hạn sử dụng sản phẩm
     *
     * @return array Mảng các hạn sử dụng sản phẩm
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
     * Lấy danh sách lịch sử IMEI
     *
     * @return array Mảng các lịch sử IMEI
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
     * Lấy danh sách IMEI đã bán
     *
     * @return array Mảng các IMEI đã bán
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
     * Lấy danh sách danh mục nội bộ
     *
     * @return array Mảng các danh mục nội bộ
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
     * Lấy danh sách thông tin website
     *
     * @return array Mảng các thông tin website
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
     * Lấy danh sách bảo hành sản phẩm
     *
     * @return array Mảng các bảo hành sản phẩm
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
     * Lấy trạng thái cache của sản phẩm
     *
     * @return array Mảng chứa thông tin trạng thái cache
     */
    public function getCacheStatus(): array
    {
        return $this->productManager->getProductCacheStatus();
    }

    /**
     * Xóa tất cả cache sản phẩm
     *
     * @return bool True nếu xóa thành công, false nếu thất bại
     */
    public function clearCache(): bool
    {
        return $this->productManager->clearAllProductCache();
    }

    /**
     * Kiểm tra cache sản phẩm có sẵn không
     *
     * @return bool True nếu cache có sẵn, false nếu không
     */
    public function isCacheAvailable(): bool
    {
        return $this->productManager->isProductCacheAvailable();
    }

    /**
     * Lấy instance của ProductManager
     *
     * @return ProductManager Instance của ProductManager
     */
    public function getManager(): ProductManager
    {
        return $this->productManager;
    }

    /**
     * Helper method để giải phóng memory cho cached data
     *
     * @param array $cachedData Dữ liệu cache cần xử lý
     * @param string $methodName Tên method để tạo entities
     * @return array Mảng các entities đã được tạo
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
     *
     * @param array $responseData Dữ liệu response từ API
     * @param string $methodName Tên method để tạo entities
     * @param string $dataKey Key chứa data trong response (mặc định: 'data')
     * @return array Mảng các entities đã được tạo
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

    /**
     * Thêm ảnh sản phẩm từ CDN bên ngoài
     *
     * @param array $productData Dữ liệu sản phẩm và ảnh
     * @return ProductExternalImageResponse Response từ API
     * @throws Exception Khi có lỗi xảy ra
     */
    public function addExternalImage(array $productData): ProductExternalImageResponse
    {
        $this->logger->info("ProductModule::addExternalImage() called", ['productData' => $productData]);

        try {
            $response = $this->productManager->addProductExternalImage($productData);

            $this->logger->info("ProductModule::addExternalImage() - Success", [
                'productId' => $response->getFirstProcessedProductId(),
                'totalProcessed' => $response->getTotalProcessedProducts()
            ]);

            return $response;
        } catch (Exception $e) {
            $this->logger->error("ProductModule::addExternalImage() - Error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Thêm ảnh cho nhiều sản phẩm cùng lúc (batch add)
     *
     * @param array $productsData Mảng dữ liệu sản phẩm và ảnh
     * @return ProductExternalImageResponse Response từ API
     * @throws Exception Khi có lỗi xảy ra
     */
    public function addExternalImages(array $productsData): ProductExternalImageResponse
    {
        $this->logger->info("ProductModule::addExternalImages() called", [
            'totalProducts' => count($productsData)
        ]);

        try {
            // Validate batch size
            if (count($productsData) > 10) {
                throw new \InvalidArgumentException('Chỉ được thêm ảnh cho tối đa 10 sản phẩm mỗi lần');
            }

            $response = $this->productManager->addProductExternalImages($productsData);

            $this->logger->info("ProductModule::addExternalImages() - Success", [
                'totalProcessed' => $response->getTotalProcessedProducts(),
                'processedIds' => $response->getAllProcessedProductIds()
            ]);

            return $response;
        } catch (Exception $e) {
            $this->logger->error("ProductModule::addExternalImages() - Error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validate product external image request data
     *
     * @param array $productData Dữ liệu sản phẩm và ảnh
     * @return bool True nếu hợp lệ
     */
    public function validateExternalImageRequest(array $productData): bool
    {
        try {
            return $this->productManager->validateProductExternalImageRequest($productData);
        } catch (Exception $e) {
            $this->logger->error("ProductModule::validateExternalImageRequest() - Error", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Validate multiple product external image requests
     *
     * @param array $productsData Mảng dữ liệu sản phẩm và ảnh
     * @return array Mảng lỗi validation (empty nếu tất cả đều hợp lệ)
     */
    public function validateExternalImageRequests(array $productsData): array
    {
        $errors = [];

        foreach ($productsData as $index => $productData) {
            if (!$this->validateExternalImageRequest($productData)) {
                $errors[] = "Dữ liệu sản phẩm thứ {$index} không hợp lệ";
            }
        }

        return $errors;
    }

    /**
     * Xóa cache của sản phẩm theo ID
     *
     * @param string $productId ID sản phẩm cần xóa cache
     * @return bool True nếu xóa cache thành công
     */
    public function clearProductCache(string $productId): bool
    {
        try {
            $this->logger->debug("ProductModule::clearProductCache() called", ['productId' => $productId]);

            // Xóa cache từ ProductManager nếu có
            if (method_exists($this->productManager, 'clearProductCache')) {
                return $this->productManager->clearProductCache($productId);
            }

            // Nếu ProductManager không có method này, chỉ log và return true
            $this->logger->info("ProductModule::clearProductCache() - ProductManager không có clearProductCache method, skipping");

            return true;
        } catch (Exception $e) {
            $this->logger->error("ProductModule::clearProductCache() - Error", [
                'productId' => $productId,
                'error' => $e->getMessage()
            ]);

            // Return true để không làm gián đoạn flow chính
            return true;
        }
    }
}
