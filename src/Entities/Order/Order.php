<?php

namespace Puleeno\NhanhVn\Entities\Order;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Order Entity - Đại diện cho đơn hàng từ Nhanh.vn
 *
 * Entity này chứa toàn bộ thông tin đơn hàng bao gồm:
 * - Thông tin cơ bản: ID, mã đơn hàng, loại đơn hàng
 * - Thông tin khách hàng: tên, số điện thoại, địa chỉ
 * - Thông tin vận chuyển: hãng vận chuyển, phí vận chuyển
 * - Thông tin sản phẩm: danh sách sản phẩm, giá cả
 * - Thông tin trạng thái: trạng thái đơn hàng, thời gian tạo/cập nhật
 */
class Order extends AbstractEntity
{
    /** @var int|null ID đơn hàng trên Nhanh.vn */
    protected ?int $id = null;

    /** @var string|null ID website tích hợp */
    protected ?string $shopOrderId = null;

    /** @var string|null Mã vận đơn web tích hợp */
    protected ?string $merchantTrackingNumber = null;

    /** @var int|null ID biên bản bàn giao */
    protected ?int $handoverId = null;

    /** @var int|null ID kho hàng */
    protected ?int $depotId = null;

    /** @var string|null Tên kho hàng */
    protected ?string $depotName = null;

    /** @var int|null Mã loại đơn hàng */
    protected ?int $typeId = null;

    /** @var string|null Loại đơn hàng */
    protected ?string $type = null;

    /** @var float|null Tiền chiết khấu */
    protected ?float $moneyDiscount = null;

    /** @var float|null Tiền đặt cọc */
    protected ?float $moneyDeposit = null;

    /** @var float|null Tiền chuyển khoản */
    protected ?float $moneyTransfer = null;

    /** @var int|null Số điểm đã tiêu */
    protected ?int $usedPoints = null;

    /** @var float|null Số tiền tiêu điểm đã tiêu */
    protected ?float $moneyUsedPoints = null;

    /** @var int|null Số tiền tiêu điểm đã tiêu */
    protected ?int $usedPointAmount = null;

    /** @var int|null Mã dịch vụ vận chuyển */
    protected ?int $serviceId = null;

    /** @var int|null ID hãng vận chuyển */
    protected ?int $carrierId = null;

    /** @var int|null Loại dịch vụ vận chuyển */
    protected ?int $carrierServiceType = null;

    /** @var string|null Tên loại dịch vụ */
    protected ?string $carrierServiceTypeName = null;

    /** @var string|null Mã vận đơn */
    protected ?string $carrierCode = null;

    /** @var string|null Tên hãng vận chuyển */
    protected ?string $carrierName = null;

    /** @var string|null Dịch vụ vận chuyển */
    protected ?string $carrierServiceName = null;

    /** @var float|null Phí vận chuyển */
    protected ?float $shipFee = null;

    /** @var float|null Phí thu tiền hộ */
    protected ?float $codFee = null;

    /** @var float|null Phí bảo hiểm */
    protected ?float $declaredFee = null;

    /** @var float|null Phí thu của khách */
    protected ?float $customerShipFee = null;

    /** @var float|null Phí chuyển hoàn */
    protected ?float $returnFee = null;

    /** @var float|null Phí vượt cân */
    protected ?float $overWeightShipFee = null;

    /** @var string|null Ghi chú của khách hàng */
    protected ?string $description = null;

    /** @var string|null Ghi chú nội bộ */
    protected ?string $privateDescription = null;

    /** @var int|null Mã khách hàng */
    protected ?int $customerId = null;

    /** @var string|null Tên khách hàng */
    protected ?string $customerName = null;

    /** @var string|null Số điện thoại khách hàng */
    protected ?string $customerMobile = null;

    /** @var string|null Email khách hàng */
    protected ?string $customerEmail = null;

    /** @var string|null Địa chỉ khách hàng */
    protected ?string $customerAddress = null;

    /** @var int|null Mã tỉnh */
    protected ?int $customerCityId = null;

    /** @var string|null Thành phố */
    protected ?string $customerCity = null;

    /** @var int|null Mã quận/huyện */
    protected ?int $customerDistrictId = null;

    /** @var string|null Quận huyện */
    protected ?string $customerDistrict = null;

    /** @var int|null ID người tạo đơn */
    protected ?int $createdById = null;

    /** @var string|null Người tạo đơn */
    protected ?string $createdByName = null;

    /** @var string|null Thời gian tạo đơn hàng */
    protected ?string $createdDateTime = null;

    /** @var string|null Ngày giao hàng */
    protected ?string $deliveryDate = null;

    /** @var string|null Mã trạng thái */
    protected ?string $statusCode = null;

    /** @var string|null Trạng thái đơn hàng */
    protected ?string $statusName = null;

    /** @var float|null Tổng thu của khách */
    protected ?float $calcTotalMoney = null;

    /** @var int|null ID nguồn đơn hàng */
    protected ?int $trafficSourceId = null;

    /** @var string|null Tên nguồn đơn hàng */
    protected ?string $trafficSourceName = null;

    /** @var int|null ID nhân viên bán hàng */
    protected ?int $saleId = null;

    /** @var string|null Tên nhân viên bán hàng */
    protected ?string $saleName = null;

    /** @var string|null ID đơn hàng gốc (cho đơn trả hàng) */
    protected ?string $returnFromOrderId = null;

    /** @var string|null Mã giới thiệu */
    protected ?string $affiliateCode = null;

    /** @var string|null Tiền hoa hồng được hưởng */
    protected ?string $affiliateBonusCash = null;

    /** @var int|null Phần trăm hoa hồng được hưởng */
    protected ?int $affiliateBonusPercent = null;

    /** @var array|null Mảng các nhãn của đơn hàng */
    protected ?array $tags = null;

    /** @var int|null Kênh bán phát sinh đơn hàng */
    protected ?int $saleChannel = null;

    /** @var string|null ID shop ecommerce */
    protected ?string $ecomShopId = null;

    /** @var string|null Mã coupon */
    protected ?string $couponCode = null;

    /** @var array|null Danh sách sản phẩm */
    protected ?array $products = null;

    /** @var string|null UTM Source */
    protected ?string $utmSource = null;

    /** @var string|null UTM Medium */
    protected ?string $utmMedium = null;

    /** @var string|null UTM Campaign */
    protected ?string $utmCampaign = null;

    /** @var array|null Thông tin Facebook */
    protected ?array $facebook = null;

    /** @var int|null Ngày cập nhật đơn hàng (timestamp) */
    protected ?int $updatedAt = null;

    /** @var array|null Thông tin đóng gói */
    protected ?array $packed = null;

    /** @var array|null Thông tin VAT */
    protected ?array $vat = null;

    /**
     * Constructor
     *
     * @param array $data Dữ liệu đơn hàng từ API
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        // Map data từ attributes sang properties để tương thích với getter methods
        $this->id = $this->getAttribute('id');
        $this->shopOrderId = $this->getAttribute('shopOrderId');
        $this->merchantTrackingNumber = $this->getAttribute('merchantTrackingNumber');
        $this->handoverId = $this->getAttribute('handoverId');
        $this->depotId = $this->getAttribute('depotId');
        $this->depotName = $this->getAttribute('depotName');
        $this->typeId = $this->getAttribute('typeId');
        $this->type = $this->getAttribute('type');
        $this->moneyDiscount = $this->getAttribute('moneyDiscount');
        $this->moneyDeposit = $this->getAttribute('moneyDeposit');
        $this->moneyTransfer = $this->getAttribute('moneyTransfer');
        $this->usedPoints = $this->getAttribute('usedPoints');
        $this->moneyUsedPoints = $this->getAttribute('moneyUsedPoints');
        $this->usedPointAmount = $this->getAttribute('usedPointAmount');
        $this->serviceId = $this->getAttribute('serviceId');
        $this->carrierId = $this->getAttribute('carrierId');
        $this->carrierServiceType = $this->getAttribute('carrierServiceType');
        $this->carrierServiceTypeName = $this->getAttribute('carrierServiceTypeName');
        $this->carrierCode = $this->getAttribute('carrierCode');
        $this->carrierName = $this->getAttribute('carrierName');
        $this->carrierServiceName = $this->getAttribute('carrierServiceName');
        $this->shipFee = $this->getAttribute('shipFee');
        $this->codFee = $this->getAttribute('codFee');
        $this->declaredFee = $this->getAttribute('declaredFee');
        $this->customerShipFee = $this->getAttribute('customerShipFee');
        $this->returnFee = $this->getAttribute('returnFee');
        $this->overWeightShipFee = $this->getAttribute('overWeightShipFee');
        $this->description = $this->getAttribute('description');
        $this->privateDescription = $this->getAttribute('privateDescription');
        $this->customerId = $this->getAttribute('customerId');
        $this->customerName = $this->getAttribute('customerName');
        $this->customerMobile = $this->getAttribute('customerMobile');
        $this->customerEmail = $this->getAttribute('customerEmail');
        $this->customerAddress = $this->getAttribute('customerAddress');
        $this->customerCityId = $this->getAttribute('customerCityId');
        $this->customerCity = $this->getAttribute('customerCity');
        $this->customerDistrictId = $this->getAttribute('customerDistrictId');
        $this->customerDistrict = $this->getAttribute('customerDistrict');
        $this->createdById = $this->getAttribute('createdById');
        $this->createdByName = $this->getAttribute('createdByName');
        $this->createdDateTime = $this->getAttribute('createdDateTime');
        $this->deliveryDate = $this->getAttribute('deliveryDate');
        $this->statusCode = $this->getAttribute('statusCode');
        $this->statusName = $this->getAttribute('statusName');
        $this->calcTotalMoney = $this->getAttribute('calcTotalMoney');
        $this->trafficSourceId = $this->getAttribute('trafficSourceId');
        $this->trafficSourceName = $this->getAttribute('trafficSourceName');
        $this->saleId = $this->getAttribute('saleId');
        $this->saleName = $this->getAttribute('saleName');
        $this->returnFromOrderId = $this->getAttribute('returnFromOrderId');
        $this->affiliateCode = $this->getAttribute('affiliateCode');
        $this->affiliateBonusCash = $this->getAttribute('affiliateBonusCash');
        $this->affiliateBonusPercent = $this->getAttribute('affiliateBonusPercent');
        $this->tags = $this->getAttribute('tags');
        // Map cả channel và saleChannel
        $this->saleChannel = $this->getAttribute('saleChannel') ?: $this->getAttribute('channel');
        $this->ecomShopId = $this->getAttribute('ecomShopId');
        $this->couponCode = $this->getAttribute('couponCode');
        $this->products = $this->getAttribute('products');
        $this->utmSource = $this->getAttribute('utmSource');
        $this->utmMedium = $this->getAttribute('utmMedium');
        $this->utmCampaign = $this->getAttribute('utmCampaign');
        $this->facebook = $this->getAttribute('facebook');
        $this->updatedAt = $this->getAttribute('updatedAt');
        $this->packed = $this->getAttribute('packed');
        $this->vat = $this->getAttribute('vat');
    }

    /**
     * Validate entity data
     */
    protected function validate(): void
    {
        // Validate required fields
        if (empty($this->attributes['id'])) {
            $this->addError('id', 'ID đơn hàng không được để trống');
        }

        if (empty($this->attributes['customerName'])) {
            $this->addError('customerName', 'Tên khách hàng không được để trống');
        }

        if (empty($this->attributes['customerMobile'])) {
            $this->addError('customerMobile', 'Số điện thoại khách hàng không được để trống');
        }

        // Validate numeric fields
        if (isset($this->attributes['calcTotalMoney']) && !is_numeric($this->attributes['calcTotalMoney'])) {
            $this->addError('calcTotalMoney', 'Tổng tiền phải là số');
        }

        if (isset($this->attributes['shipFee']) && !is_numeric($this->attributes['shipFee'])) {
            $this->addError('shipFee', 'Phí vận chuyển phải là số');
        }
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getShopOrderId(): ?string
    {
        return $this->shopOrderId;
    }
    public function getMerchantTrackingNumber(): ?string
    {
        return $this->merchantTrackingNumber;
    }
    public function getHandoverId(): ?int
    {
        return $this->handoverId;
    }
    public function getDepotId(): ?int
    {
        return $this->depotId;
    }
    public function getDepotName(): ?string
    {
        return $this->depotName;
    }
    public function getTypeId(): ?int
    {
        return $this->typeId;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function getMoneyDiscount(): ?float
    {
        return $this->moneyDiscount;
    }
    public function getMoneyDeposit(): ?float
    {
        return $this->moneyDeposit;
    }
    public function getMoneyTransfer(): ?float
    {
        return $this->moneyTransfer;
    }
    public function getUsedPoints(): ?int
    {
        return $this->usedPoints;
    }
    public function getMoneyUsedPoints(): ?float
    {
        return $this->moneyUsedPoints;
    }
    public function getUsedPointAmount(): ?int
    {
        return $this->usedPointAmount;
    }
    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }
    public function getCarrierId(): ?int
    {
        return $this->carrierId;
    }
    public function getCarrierServiceType(): ?int
    {
        return $this->carrierServiceType;
    }
    public function getCarrierServiceTypeName(): ?string
    {
        return $this->carrierServiceTypeName;
    }
    public function getCarrierCode(): ?string
    {
        return $this->carrierCode;
    }
    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }
    public function getCarrierServiceName(): ?string
    {
        return $this->carrierServiceName;
    }
    public function getShipFee(): ?float
    {
        return $this->shipFee;
    }
    public function getCodFee(): ?float
    {
        return $this->codFee;
    }
    public function getDeclaredFee(): ?float
    {
        return $this->declaredFee;
    }
    public function getCustomerShipFee(): ?float
    {
        return $this->customerShipFee;
    }
    public function getReturnFee(): ?float
    {
        return $this->returnFee;
    }
    public function getOverWeightShipFee(): ?float
    {
        return $this->overWeightShipFee;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getPrivateDescription(): ?string
    {
        return $this->privateDescription;
    }
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }
    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }
    public function getCustomerMobile(): ?string
    {
        return $this->customerMobile;
    }
    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }
    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }
    public function getCustomerCityId(): ?int
    {
        return $this->customerCityId;
    }
    public function getCustomerCity(): ?string
    {
        return $this->customerCity;
    }
    public function getCustomerDistrictId(): ?int
    {
        return $this->customerDistrictId;
    }
    public function getCustomerDistrict(): ?string
    {
        return $this->customerDistrict;
    }
    public function getCreatedById(): ?int
    {
        return $this->createdById;
    }
    public function getCreatedByName(): ?string
    {
        return $this->createdByName;
    }
    public function getCreatedDateTime(): ?string
    {
        return $this->createdDateTime;
    }
    public function getDeliveryDate(): ?string
    {
        return $this->deliveryDate;
    }
    public function getStatusCode(): ?string
    {
        return $this->statusCode;
    }
    public function getStatusName(): ?string
    {
        return $this->statusName;
    }
    public function getCalcTotalMoney(): ?float
    {
        return $this->calcTotalMoney;
    }
    public function getTrafficSourceId(): ?int
    {
        return $this->trafficSourceId;
    }
    public function getTrafficSourceName(): ?string
    {
        return $this->trafficSourceName;
    }
    public function getSaleId(): ?int
    {
        return $this->saleId;
    }
    public function getSaleName(): ?string
    {
        return $this->saleName;
    }
    public function getReturnFromOrderId(): ?string
    {
        return $this->returnFromOrderId;
    }
    public function getAffiliateCode(): ?string
    {
        return $this->affiliateCode;
    }
    public function getAffiliateBonusCash(): ?string
    {
        return $this->affiliateBonusCash;
    }
    public function getAffiliateBonusPercent(): ?int
    {
        return $this->affiliateBonusPercent;
    }
    public function getTags(): ?array
    {
        return $this->tags;
    }
    public function getSaleChannel(): ?int
    {
        return $this->saleChannel;
    }
    public function getEcomShopId(): ?string
    {
        return $this->ecomShopId;
    }
    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }
    public function getProducts(): ?array
    {
        return $this->products;
    }
    public function getUtmSource(): ?string
    {
        return $this->utmSource;
    }
    public function getUtmMedium(): ?string
    {
        return $this->utmMedium;
    }
    public function getUtmCampaign(): ?string
    {
        return $this->utmCampaign;
    }
    public function getFacebook(): ?array
    {
        return $this->facebook;
    }
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }
    public function getPacked(): ?array
    {
        return $this->packed;
    }
    public function getVat(): ?array
    {
        return $this->vat;
    }

    /**
     * Lấy tổng phí vận chuyển
     *
     * @return float Tổng phí vận chuyển
     */
    public function getTotalCarrierFee(): float
    {
        return ($this->shipFee ?? 0) + ($this->codFee ?? 0) + ($this->declaredFee ?? 0) + ($this->returnFee ?? 0) + ($this->overWeightShipFee ?? 0);
    }

    /**
     * Kiểm tra đơn hàng có phải đơn giao hàng không
     *
     * @return bool True nếu là đơn giao hàng
     */
    public function isShippingOrder(): bool
    {
        return $this->typeId === 1;
    }

    /**
     * Kiểm tra đơn hàng có phải đơn mua tại quầy không
     *
     * @return bool True nếu là đơn mua tại quầy
     */
    public function isCounterOrder(): bool
    {
        return $this->typeId === 2;
    }

    /**
     * Kiểm tra đơn hàng có phải đơn đặt trước không
     *
     * @return bool True nếu là đơn đặt trước
     */
    public function isPreOrder(): bool
    {
        return $this->typeId === 3;
    }

    /**
     * Kiểm tra đơn hàng có phải đơn trả hàng không
     *
     * @return bool True nếu là đơn trả hàng
     */
    public function isReturnOrder(): bool
    {
        return $this->typeId === 14;
    }

    /**
     * Lấy tên kênh bán hàng
     *
     * @return string Tên kênh bán hàng
     */
    public function getSaleChannelName(): string
    {
        $channels = [
            1 => 'Admin',
            2 => 'Website',
            10 => 'API',
            20 => 'Facebook',
            21 => 'Instagram',
            41 => 'Lazada.vn',
            42 => 'Shopee.vn',
            43 => 'Sendo.vn',
            45 => 'Tiki.vn',
            46 => 'Zalo Shop',
            47 => '1Landing.vn',
            48 => 'Tiktok Shop',
            49 => 'Zalo OA',
            50 => 'Shopee Chat',
            51 => 'Lazada Chat',
            52 => 'Zalo cá nhân'
        ];

        return $channels[$this->saleChannel] ?? 'Unknown';
    }
}
