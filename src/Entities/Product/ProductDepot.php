<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Depot entity
 */
class ProductDepot extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID kho hàng không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên kho hàng không được để trống');
        }
    }

    // Basic getters
    public function getId(): mixed
    {
        return $this->getAttribute('id');
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }

    public function getManager(): ?string
    {
        return $this->getAttribute('manager');
    }

    public function getStatus(): ?string
    {
        return $this->getAttribute('status');
    }

    public function getType(): ?string
    {
        return $this->getAttribute('type');
    }

    public function getCapacity(): ?int
    {
        return $this->getAttribute('capacity');
    }

    public function getUsedCapacity(): ?int
    {
        return $this->getAttribute('usedCapacity');
    }

    public function getAvailableCapacity(): ?int
    {
        return $this->getAttribute('availableCapacity');
    }

    public function getLocation(): ?string
    {
        return $this->getAttribute('location');
    }

    public function getCity(): ?string
    {
        return $this->getAttribute('city');
    }

    public function getDistrict(): ?string
    {
        return $this->getAttribute('district');
    }

    public function getWard(): ?string
    {
        return $this->getAttribute('ward');
    }

    public function getLatitude(): ?float
    {
        return $this->getAttribute('latitude');
    }

    public function getLongitude(): ?float
    {
        return $this->getAttribute('longitude');
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
    public function isActive(): bool
    {
        return $this->getStatus() === 'Active';
    }

    public function isInactive(): bool
    {
        return $this->getStatus() === 'Inactive';
    }

    public function hasCode(): bool
    {
        return !empty($this->getCode());
    }

    public function hasAddress(): bool
    {
        return !empty($this->getAddress());
    }

    public function hasPhone(): bool
    {
        return !empty($this->getPhone());
    }

    public function hasEmail(): bool
    {
        return !empty($this->getEmail());
    }

    public function hasManager(): bool
    {
        return !empty($this->getManager());
    }

    public function hasCapacity(): bool
    {
        return $this->getCapacity() !== null;
    }

    public function hasLocation(): bool
    {
        return !empty($this->getLocation());
    }

    public function hasCoordinates(): bool
    {
        return $this->getLatitude() !== null && $this->getLongitude() !== null;
    }

    public function getDisplayName(): string
    {
        if ($this->hasCode()) {
            return "{$this->getCode()} - {$this->getName()}";
        }

        return $this->getName() ?: '';
    }

    public function getFormattedAddress(): string
    {
        $address = $this->getAddress();
        if (!$address) {
            return 'N/A';
        }

        return $address;
    }

    public function getFormattedPhone(): string
    {
        $phone = $this->getPhone();
        if (!$phone) {
            return 'N/A';
        }

        // Format phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 10) {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 3);
        }

        if (strlen($phone) === 11) {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 4);
        }

        return $phone;
    }

    public function getFormattedCapacity(): string
    {
        if (!$this->hasCapacity()) {
            return 'N/A';
        }

        $capacity = $this->getCapacity();
        $used = $this->getUsedCapacity() ?? 0;
        $available = $this->getAvailableCapacity() ?? 0;

        return "{$used}/{$capacity} (Còn: {$available})";
    }

    public function getFormattedLocation(): string
    {
        $location = $this->getLocation();
        if (!$location) {
            return 'N/A';
        }

        return $location;
    }

    public function getFullAddress(): string
    {
        $parts = [];

        if ($this->getAddress()) {
            $parts[] = $this->getAddress();
        }

        if ($this->getWard()) {
            $parts[] = $this->getWard();
        }

        if ($this->getDistrict()) {
            $parts[] = $this->getDistrict();
        }

        if ($this->getCity()) {
            $parts[] = $this->getCity();
        }

        if (empty($parts)) {
            return 'N/A';
        }

        return implode(', ', $parts);
    }

    public function getFormattedCreatedAt(): string
    {
        $date = $this->getCreatedAt();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getFormattedUpdatedAt(): string
    {
        $date = $this->getUpdatedAt();
        if (!$date) {
            return 'N/A';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public function getStatusText(): string
    {
        return $this->getStatus() ?: 'Unknown';
    }

    public function getTypeText(): string
    {
        return $this->getType() ?: 'Unknown';
    }

    public function getDepotSummary(): string
    {
        $summary = [];

        $summary[] = $this->getName();

        if ($this->hasAddress()) {
            $summary[] = "ĐC: {$this->getFullAddress()}";
        }

        if ($this->hasPhone()) {
            $summary[] = "ĐT: {$this->getFormattedPhone()}";
        }

        if ($this->hasManager()) {
            $summary[] = "QL: {$this->getManager()}";
        }

        return implode(' | ', array_filter($summary));
    }

    /**
     * Kiểm tra xem depot có phải là kho chính không
     */
    public function isMainDepot(): bool
    {
        $type = strtolower($this->getType() ?? '');
        return strpos($type, 'chính') !== false || strpos($type, 'main') !== false;
    }

    /**
     * Kiểm tra xem depot có phải là kho phụ không
     */
    public function isSecondaryDepot(): bool
    {
        $type = strtolower($this->getType() ?? '');
        return strpos($type, 'phụ') !== false || strpos($type, 'secondary') !== false;
    }

    /**
     * Kiểm tra xem depot có phải là kho online không
     */
    public function isOnlineDepot(): bool
    {
        $type = strtolower($this->getType() ?? '');
        return strpos($type, 'online') !== false || strpos($type, 'web') !== false;
    }

    /**
     * Kiểm tra xem depot có phải là kho offline không
     */
    public function isOfflineDepot(): bool
    {
        $type = strtolower($this->getType() ?? '');
        return strpos($type, 'offline') !== false || strpos($type, 'cửa hàng') !== false;
    }

    /**
     * Kiểm tra xem depot có phải là kho bảo hành không
     */
    public function isWarrantyDepot(): bool
    {
        $type = strtolower($this->getType() ?? '');
        return strpos($type, 'bảo hành') !== false || strpos($type, 'warranty') !== false;
    }

    /**
     * Kiểm tra xem depot có đầy không
     */
    public function isFull(): bool
    {
        if (!$this->hasCapacity()) {
            return false;
        }

        $capacity = $this->getCapacity();
        $used = $this->getUsedCapacity() ?? 0;

        return $used >= $capacity;
    }

    /**
     * Kiểm tra xem depot có gần đầy không (>80%)
     */
    public function isNearFull(): bool
    {
        if (!$this->hasCapacity()) {
            return false;
        }

        $capacity = $this->getCapacity();
        $used = $this->getUsedCapacity() ?? 0;

        return ($used / $capacity) >= 0.8;
    }

    /**
     * Kiểm tra xem depot có trống không
     */
    public function isEmpty(): bool
    {
        if (!$this->hasCapacity()) {
            return false;
        }

        $used = $this->getUsedCapacity() ?? 0;
        return $used === 0;
    }

    /**
     * Lấy tỷ lệ sử dụng kho (0-100%)
     */
    public function getUsagePercentage(): ?float
    {
        if (!$this->hasCapacity()) {
            return null;
        }

        $capacity = $this->getCapacity();
        $used = $this->getUsedCapacity() ?? 0;

        return round(($used / $capacity) * 100, 2);
    }

    /**
     * Lấy tỷ lệ còn trống kho (0-100%)
     */
    public function getAvailablePercentage(): ?float
    {
        if (!$this->hasCapacity()) {
            return null;
        }

        $capacity = $this->getCapacity();
        $available = $this->getAvailableCapacity() ?? 0;

        return round(($available / $capacity) * 100, 2);
    }

    /**
     * Lấy trạng thái kho dựa trên dung lượng
     */
    public function getCapacityStatus(): string
    {
        if ($this->isEmpty()) {
            return 'Trống';
        }

        if ($this->isFull()) {
            return 'Đầy';
        }

        if ($this->isNearFull()) {
            return 'Gần đầy';
        }

        return 'Bình thường';
    }

    /**
     * Lấy màu sắc cho trạng thái dung lượng
     */
    public function getCapacityStatusColor(): string
    {
        $status = $this->getCapacityStatus();

        switch ($status) {
            case 'Trống':
                return '#28a745'; // Green
            case 'Bình thường':
                return '#007bff'; // Blue
            case 'Gần đầy':
                return '#ffc107'; // Yellow
            case 'Đầy':
                return '#dc3545'; // Red
            default:
                return '#6c757d'; // Gray
        }
    }

    /**
     * Lấy thông tin liên hệ đầy đủ
     */
    public function getFullContactInfo(): array
    {
        $contactInfo = [];

        if ($this->hasManager()) {
            $contactInfo['manager'] = $this->getManager();
        }

        if ($this->hasPhone()) {
            $contactInfo['phone'] = $this->getFormattedPhone();
        }

        if ($this->hasEmail()) {
            $contactInfo['email'] = $this->getEmail();
        }

        if ($this->hasAddress()) {
            $contactInfo['address'] = $this->getFullAddress();
        }

        return $contactInfo;
    }

    /**
     * Lấy thông tin vị trí đầy đủ
     */
    public function getFullLocationInfo(): array
    {
        $locationInfo = [];

        if ($this->hasLocation()) {
            $locationInfo['location'] = $this->getLocation();
        }

        if ($this->getWard()) {
            $locationInfo['ward'] = $this->getWard();
        }

        if ($this->getDistrict()) {
            $locationInfo['district'] = $this->getDistrict();
        }

        if ($this->getCity()) {
            $locationInfo['city'] = $this->getCity();
        }

        if ($this->hasCoordinates()) {
            $locationInfo['coordinates'] = [
                'latitude' => $this->getLatitude(),
                'longitude' => $this->getLongitude()
            ];
        }

        $locationInfo['fullAddress'] = $this->getFullAddress();

        return $locationInfo;
    }

    /**
     * Lấy thông tin dung lượng đầy đủ
     */
    public function getFullCapacityInfo(): array
    {
        if (!$this->hasCapacity()) {
            return [];
        }

        return [
            'capacity' => $this->getCapacity(),
            'usedCapacity' => $this->getUsedCapacity(),
            'availableCapacity' => $this->getAvailableCapacity(),
            'usagePercentage' => $this->getUsagePercentage(),
            'availablePercentage' => $this->getAvailablePercentage(),
            'capacityStatus' => $this->getCapacityStatus(),
            'capacityStatusColor' => $this->getCapacityStatusColor(),
            'formatted' => $this->getFormattedCapacity(),
            'isFull' => $this->isFull(),
            'isNearFull' => $this->isNearFull(),
            'isEmpty' => $this->isEmpty()
        ];
    }

    /**
     * Tạo depot từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo depot từ tên
     */
    public static function createFromName(string $name, string $code = ''): self
    {
        $data = ['name' => $name];

        if (!empty($code)) {
            $data['code'] = $code;
        }

        return new self($data);
    }

    /**
     * Tạo nhiều depots từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $depots = [];

        foreach ($data as $depotData) {
            $depots[] = self::createFromArray($depotData);
        }

        return $depots;
    }

    /**
     * Lọc depots theo trạng thái
     */
    public static function filterByStatus(array $depots, string $status): array
    {
        return array_filter($depots, function (ProductDepot $depot) use ($status) {
            return $depot->getStatus() === $status;
        });
    }

    /**
     * Lọc depots theo type
     */
    public static function filterByType(array $depots, string $type): array
    {
        return array_filter($depots, function (ProductDepot $depot) use ($type) {
            return $depot->getType() === $type;
        });
    }

    /**
     * Lọc depots chính
     */
    public static function filterMainDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isMainDepot();
        });
    }

    /**
     * Lọc depots phụ
     */
    public static function filterSecondaryDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isSecondaryDepot();
        });
    }

    /**
     * Lọc depots online
     */
    public static function filterOnlineDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isOnlineDepot();
        });
    }

    /**
     * Lọc depots offline
     */
    public static function filterOfflineDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isOfflineDepot();
        });
    }

    /**
     * Lọc depots bảo hành
     */
    public static function filterWarrantyDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isWarrantyDepot();
        });
    }

    /**
     * Lọc depots đầy
     */
    public static function filterFullDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isFull();
        });
    }

    /**
     * Lọc depots gần đầy
     */
    public static function filterNearFullDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isNearFull();
        });
    }

    /**
     * Lọc depots trống
     */
    public static function filterEmptyDepots(array $depots): array
    {
        return array_filter($depots, function (ProductDepot $depot) {
            return $depot->isEmpty();
        });
    }

    /**
     * Sắp xếp depots theo tên
     */
    public static function sortByName(array $depots, bool $ascending = true): array
    {
        usort($depots, function (ProductDepot $a, ProductDepot $b) use ($ascending) {
            $nameA = $a->getName() ?? '';
            $nameB = $b->getName() ?? '';

            if ($ascending) {
                return strcmp($nameA, $nameB);
            }

            return strcmp($nameB, $nameA);
        });

        return $depots;
    }

    /**
     * Sắp xếp depots theo dung lượng sử dụng
     */
    public static function sortByUsage(array $depots, bool $descending = true): array
    {
        usort($depots, function (ProductDepot $a, ProductDepot $b) use ($descending) {
            $usageA = $a->getUsagePercentage() ?? 0;
            $usageB = $b->getUsagePercentage() ?? 0;

            if ($descending) {
                return $usageB <=> $usageA;
            }

            return $usageA <=> $usageB;
        });

        return $depots;
    }

    /**
     * Sắp xếp depots theo dung lượng còn trống
     */
    public static function sortByAvailable(array $depots, bool $descending = true): array
    {
        usort($depots, function (ProductDepot $a, ProductDepot $b) use ($descending) {
            $availableA = $a->getAvailableCapacity() ?? 0;
            $availableB = $b->getAvailableCapacity() ?? 0;

            if ($descending) {
                return $availableB <=> $availableA;
            }

            return $availableA <=> $availableB;
        });

        return $depots;
    }

    /**
     * Tìm depots theo tên (partial match)
     */
    public static function searchByName(array $depots, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($depots, function (ProductDepot $depot) use ($searchTerm) {
            $name = strtolower($depot->getName() ?? '');
            return strpos($name, $searchTerm) !== false;
        });
    }

    /**
     * Tìm depots theo code (partial match)
     */
    public static function searchByCode(array $depots, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($depots, function (ProductDepot $depot) use ($searchTerm) {
            $code = strtolower($depot->getCode() ?? '');
            return strpos($code, $searchTerm) !== false;
        });
    }

    /**
     * Tìm depots theo địa chỉ (partial match)
     */
    public static function searchByAddress(array $depots, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($depots, function (ProductDepot $depot) use ($searchTerm) {
            $address = strtolower($depot->getFullAddress());
            return strpos($address, $searchTerm) !== false;
        });
    }

    /**
     * Tìm depots theo thành phố
     */
    public static function searchByCity(array $depots, string $city): array
    {
        $city = strtolower($city);

        return array_filter($depots, function (ProductDepot $depot) use ($city) {
            $depotCity = strtolower($depot->getCity() ?? '');
            return $depotCity === $city;
        });
    }

    /**
     * Lấy danh sách cities unique
     */
    public static function getUniqueCities(array $depots): array
    {
        $cities = [];

        foreach ($depots as $depot) {
            $city = $depot->getCity();
            if ($city && !in_array($city, $cities)) {
                $cities[] = $city;
            }
        }

        sort($cities);
        return $cities;
    }

    /**
     * Lấy danh sách districts unique
     */
    public static function getUniqueDistricts(array $depots): array
    {
        $districts = [];

        foreach ($depots as $depot) {
            $district = $depot->getDistrict();
            if ($district && !in_array($district, $districts)) {
                $districts[] = $district;
            }
        }

        sort($districts);
        return $districts;
    }

    /**
     * Đếm số depots theo type
     */
    public static function countByType(array $depots): array
    {
        $counts = [];

        foreach ($depots as $depot) {
            $type = $depot->getType() ?: 'Unknown';

            if (!isset($counts[$type])) {
                $counts[$type] = 0;
            }

            $counts[$type]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Đếm số depots theo status
     */
    public static function countByStatus(array $depots): array
    {
        $counts = [];

        foreach ($depots as $depot) {
            $status = $depot->getStatus() ?: 'Unknown';

            if (!isset($counts[$status])) {
                $counts[$status] = 0;
            }

            $counts[$status]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Đếm số depots theo trạng thái dung lượng
     */
    public static function countByCapacityStatus(array $depots): array
    {
        $counts = [
            'Trống' => 0,
            'Bình thường' => 0,
            'Gần đầy' => 0,
            'Đầy' => 0
        ];

        foreach ($depots as $depot) {
            $status = $depot->getCapacityStatus();
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }

        return $counts;
    }

    /**
     * Lấy thống kê depots
     */
    public static function getStatistics(array $depots): array
    {
        $stats = [
            'total' => count($depots),
            'active' => count(self::filterByStatus($depots, 'Active')),
            'inactive' => count(self::filterByStatus($depots, 'Inactive')),
            'main' => count(self::filterMainDepots($depots)),
            'secondary' => count(self::filterSecondaryDepots($depots)),
            'online' => count(self::filterOnlineDepots($depots)),
            'offline' => count(self::filterOfflineDepots($depots)),
            'warranty' => count(self::filterWarrantyDepots($depots)),
            'full' => count(self::filterFullDepots($depots)),
            'nearFull' => count(self::filterNearFullDepots($depots)),
            'empty' => count(self::filterEmptyDepots($depots)),
            'withCapacity' => count(array_filter($depots, function (ProductDepot $d) {
                return $d->hasCapacity();
            })),
            'withLocation' => count(array_filter($depots, function (ProductDepot $d) {
                return $d->hasLocation();
            })),
            'withCoordinates' => count(array_filter($depots, function (ProductDepot $d) {
                return $d->hasCoordinates();
            }))
        ];

        return $stats;
    }
}
