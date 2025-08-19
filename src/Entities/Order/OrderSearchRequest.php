<?php

namespace Puleeno\NhanhVn\Entities\Order;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * OrderSearchRequest Entity - Request tìm kiếm đơn hàng
 *
 * Entity này chứa các tham số tìm kiếm đơn hàng theo API Nhanh.vn
 * bao gồm: phân trang, lọc theo ngày, trạng thái, khách hàng...
 */
class OrderSearchRequest extends AbstractEntity
{
    /** @var int Trang hiện tại */
    protected int $page = 1;

    /** @var int Số lượng đơn hàng trên 1 trang */
    protected int $icpp = 100;

    /** @var string Ngày tạo đơn hàng từ */
    protected string $fromDate = '';

    /** @var string Ngày tạo đơn hàng đến */
    protected string $toDate = '';

    /** @var int ID đơn hàng */
    protected int $id = 0;

    /** @var string Số điện thoại khách hàng */
    protected string $customerMobile = '';

    /** @var int ID khách hàng */
    protected int $customerId = 0;

    /** @var array Trạng thái đơn hàng */
    protected array $statuses = [];

    /** @var string Ngày giao hàng từ */
    protected string $fromDeliveryDate = '';

    /** @var string Ngày giao hàng đến */
    protected string $toDeliveryDate = '';

    /** @var int ID hãng vận chuyển */
    protected int $carrierId = 0;

    /** @var string Mã vận đơn hãng vận chuyển */
    protected string $carrierCode = '';

    /** @var int Loại đơn hàng */
    protected int $type = 0;

    /** @var int Mã thành phố khách hàng */
    protected int $customerCityId = 0;

    /** @var int Mã quận huyện khách hàng */
    protected int $customerDistrictId = 0;

    /** @var int ID biên bản bàn giao */
    protected int $handoverId = 0;

    /** @var int ID kho hàng */
    protected int $depotId = 0;

    /** @var string Ngày cập nhật từ */
    protected string $updatedDateTimeFrom = '';

    /** @var string Ngày cập nhật đến */
    protected string $updatedDateTimeTo = '';

    /** @var array Lựa chọn dữ liệu cần lấy thêm */
    protected array $dataOptions = [];

    /**
     * Constructor
     *
     * @param array $data Dữ liệu request
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Validate entity data
     */
    protected function validate(): void
    {
        // Validate page
        if ($this->page < 1) {
            $this->addError('page', 'Trang phải lớn hơn 0');
        }

        // Validate icpp
        if ($this->icpp < 1 || $this->icpp > 100) {
            $this->addError('icpp', 'Số lượng đơn hàng trên trang phải từ 1 đến 100');
        }

        // Validate date ranges (10 days limit)
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $fromDate = \DateTime::createFromFormat('Y-m-d', $this->fromDate);
            $toDate = \DateTime::createFromFormat('Y-m-d', $this->toDate);
            
            if ($fromDate && $toDate) {
                $interval = $fromDate->diff($toDate);
                if ($interval->days > 10) {
                    $this->addError('dateRange', 'Khoảng thời gian tìm kiếm không được vượt quá 10 ngày');
                }
            }
        }

        // Validate updatedDateTime range (10 days limit)
        if (!empty($this->updatedDateTimeFrom) && !empty($this->updatedDateTimeTo)) {
            $fromDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $this->updatedDateTimeFrom);
            $toDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $this->updatedDateTimeTo);
            
            if ($fromDateTime && $toDateTime) {
                $interval = $fromDateTime->diff($toDateTime);
                if ($interval->days > 10) {
                    $this->addError('updatedDateTimeRange', 'Khoảng thời gian cập nhật không được vượt quá 10 ngày');
                }
            }
        }

        // Validate delivery date range (10 days limit)
        if (!empty($this->fromDeliveryDate) && !empty($this->toDeliveryDate)) {
            $fromDelivery = \DateTime::createFromFormat('Y-m-d', $this->fromDeliveryDate);
            $toDelivery = \DateTime::createFromFormat('Y-m-d', $this->toDeliveryDate);
            
            if ($fromDelivery && $toDelivery) {
                $interval = $fromDelivery->diff($toDelivery);
                if ($interval->days > 10) {
                    $this->addError('deliveryDateRange', 'Khoảng thời gian giao hàng không được vượt quá 10 ngày');
                }
            }
        }
    }

    // Getters
    public function getPage(): int { return $this->page; }
    public function getIcpp(): int { return $this->icpp; }
    public function getFromDate(): string { return $this->fromDate; }
    public function getToDate(): string { return $this->toDate; }
    public function getId(): int { return $this->id; }
    public function getCustomerMobile(): string { return $this->customerMobile; }
    public function getCustomerId(): int { return $this->customerId; }
    public function getStatuses(): array { return $this->statuses; }
    public function getFromDeliveryDate(): string { return $this->fromDeliveryDate; }
    public function getToDeliveryDate(): string { return $this->toDeliveryDate; }
    public function getCarrierId(): int { return $this->carrierId; }
    public function getCarrierCode(): string { return $this->carrierCode; }
    public function getType(): int { return $this->type; }
    public function getCustomerCityId(): int { return $this->customerCityId; }
    public function getCustomerDistrictId(): int { return $this->customerDistrictId; }
    public function getHandoverId(): int { return $this->handoverId; }
    public function getDepotId(): int { return $this->depotId; }
    public function getUpdatedDateTimeFrom(): string { return $this->updatedDateTimeFrom; }
    public function getUpdatedDateTimeTo(): string { return $this->updatedDateTimeTo; }
    public function getDataOptions(): array { return $this->dataOptions; }

    /**
     * Chuyển đổi thành format API Nhanh.vn
     *
     * @return array Dữ liệu đã format
     */
    public function toApiFormat(): array
    {
        $apiData = [];

        // Các field cơ bản
        if ($this->page > 1) {
            $apiData['page'] = $this->page;
        }

        if ($this->icpp !== 100) {
            $apiData['icpp'] = $this->icpp;
        }

        // Các field tìm kiếm
        if (!empty($this->fromDate)) {
            $apiData['fromDate'] = $this->fromDate;
        }

        if (!empty($this->toDate)) {
            $apiData['toDate'] = $this->toDate;
        }

        if ($this->id > 0) {
            $apiData['id'] = $this->id;
        }

        if (!empty($this->customerMobile)) {
            $apiData['customerMobile'] = $this->customerMobile;
        }

        if ($this->customerId > 0) {
            $apiData['customerId'] = $this->customerId;
        }

        if (!empty($this->statuses)) {
            $apiData['statuses'] = $this->statuses;
        }

        if (!empty($this->fromDeliveryDate)) {
            $apiData['fromDeliveryDate'] = $this->fromDeliveryDate;
        }

        if (!empty($this->toDeliveryDate)) {
            $apiData['toDeliveryDate'] = $this->toDeliveryDate;
        }

        if ($this->carrierId > 0) {
            $apiData['carrierId'] = $this->carrierId;
        }

        if (!empty($this->carrierCode)) {
            $apiData['carrierCode'] = $this->carrierCode;
        }

        if ($this->type > 0) {
            $apiData['type'] = $this->type;
        }

        if ($this->customerCityId > 0) {
            $apiData['customerCityId'] = $this->customerCityId;
        }

        if ($this->customerDistrictId > 0) {
            $apiData['customerDistrictId'] = $this->customerDistrictId;
        }

        if ($this->handoverId > 0) {
            $apiData['handoverId'] = $this->handoverId;
        }

        if ($this->depotId > 0) {
            $apiData['depotId'] = $this->depotId;
        }

        if (!empty($this->updatedDateTimeFrom)) {
            $apiData['updatedDateTimeFrom'] = $this->updatedDateTimeFrom;
        }

        if (!empty($this->updatedDateTimeTo)) {
            $apiData['updatedDateTimeTo'] = $this->updatedDateTimeTo;
        }

        if (!empty($this->dataOptions)) {
            $apiData['dataOptions'] = $this->dataOptions;
        }

        return $apiData;
    }

    /**
     * Kiểm tra có cần bắt buộc fromDate và toDate không
     *
     * @return bool True nếu cần bắt buộc
     */
    public function requiresDateRange(): bool
    {
        // Nếu có lọc theo ID, customerId hoặc customerMobile thì không cần bắt buộc
        if ($this->id > 0 || $this->customerId > 0 || !empty($this->customerMobile)) {
            return false;
        }

        // Nếu có lọc theo updatedDateTime thì không cần bắt buộc
        if (!empty($this->updatedDateTimeFrom) || !empty($this->updatedDateTimeTo)) {
            return false;
        }

        // Các trường hợp khác cần bắt buộc
        return true;
    }

    /**
     * Tự động set fromDate nếu không có (10 ngày gần nhất)
     */
    public function setDefaultDateRange(): void
    {
        if (empty($this->fromDate) && $this->requiresDateRange()) {
            $this->fromDate = date('Y-m-d', strtotime('-10 days'));
        }

        if (empty($this->toDate) && $this->requiresDateRange()) {
            $this->toDate = date('Y-m-d');
        }
    }
}
