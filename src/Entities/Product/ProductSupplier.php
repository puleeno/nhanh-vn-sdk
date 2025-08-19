<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Supplier entity
 */
class ProductSupplier extends AbstractEntity
{
    protected function validate(): void
    {
        if (!$this->getId()) {
            $this->addError('id', 'ID nhà cung cấp không được để trống');
        }

        if (!$this->getName()) {
            $this->addError('name', 'Tên nhà cung cấp không được để trống');
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

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }

    public function getAddress(): ?string
    {
        return $this->getAttribute('address');
    }

    public function getTaxCode(): ?string
    {
        return $this->getAttribute('taxCode');
    }

    public function getBankAccount(): ?string
    {
        return $this->getAttribute('bankAccount');
    }

    public function getBankName(): ?string
    {
        return $this->getAttribute('bankName');
    }

    public function getContactPerson(): ?string
    {
        return $this->getAttribute('contactPerson');
    }

    public function getStatus(): ?string
    {
        return $this->getAttribute('status');
    }

    public function getType(): ?string
    {
        return $this->getAttribute('type');
    }

    public function getRating(): ?float
    {
        return $this->getAttribute('rating');
    }

    public function getPaymentTerm(): ?int
    {
        return $this->getAttribute('paymentTerm');
    }

    public function getCreditLimit(): ?float
    {
        return $this->getAttribute('creditLimit');
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

    public function hasPhone(): bool
    {
        return !empty($this->getPhone());
    }

    public function hasEmail(): bool
    {
        return !empty($this->getEmail());
    }

    public function hasAddress(): bool
    {
        return !empty($this->getAddress());
    }

    public function hasTaxCode(): bool
    {
        return !empty($this->getTaxCode());
    }

    public function hasBankInfo(): bool
    {
        return !empty($this->getBankAccount()) && !empty($this->getBankName());
    }

    public function hasContactPerson(): bool
    {
        return !empty($this->getContactPerson());
    }

    public function hasRating(): bool
    {
        return $this->getRating() !== null;
    }

    public function hasPaymentTerm(): bool
    {
        return $this->getPaymentTerm() !== null;
    }

    public function hasCreditLimit(): bool
    {
        return $this->getCreditLimit() !== null;
    }

    public function getDisplayName(): string
    {
        if ($this->hasCode()) {
            return "{$this->getCode()} - {$this->getName()}";
        }

        return $this->getName() ?: '';
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

    public function getFormattedAddress(): string
    {
        $address = $this->getAddress();
        if (!$address) {
            return 'N/A';
        }

        return $address;
    }

    public function getFormattedTaxCode(): string
    {
        $taxCode = $this->getTaxCode();
        if (!$taxCode) {
            return 'N/A';
        }

        return $taxCode;
    }

    public function getFormattedBankInfo(): string
    {
        if (!$this->hasBankInfo()) {
            return 'N/A';
        }

        return "{$this->getBankName()} - {$this->getBankAccount()}";
    }

    public function getFormattedPaymentTerm(): string
    {
        $term = $this->getPaymentTerm();
        if (!$term) {
            return 'N/A';
        }

        if ($term === 0) {
            return 'Thanh toán ngay';
        }

        return "{$term} ngày";
    }

    public function getFormattedCreditLimit(): string
    {
        $limit = $this->getCreditLimit();
        if (!$limit) {
            return 'N/A';
        }

        return number_format($limit, 0, ',', '.') . ' VNĐ';
    }

    public function getFormattedRating(): string
    {
        $rating = $this->getRating();
        if (!$rating) {
            return 'Chưa đánh giá';
        }

        return number_format($rating, 1) . '/5.0';
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

    public function getSupplierSummary(): string
    {
        $summary = [];

        $summary[] = $this->getName();

        if ($this->hasPhone()) {
            $summary[] = "ĐT: {$this->getFormattedPhone()}";
        }

        if ($this->hasEmail()) {
            $summary[] = "Email: {$this->getEmail()}";
        }

        if ($this->hasAddress()) {
            $summary[] = "ĐC: {$this->getFormattedAddress()}";
        }

        return implode(' | ', array_filter($summary));
    }

    /**
     * Kiểm tra xem supplier có phải là supplier chính không
     */
    public function isMainSupplier(): bool
    {
        $type = strtolower($this->getType() ?? '');
        return strpos($type, 'chính') !== false || strpos($type, 'main') !== false;
    }

    /**
     * Kiểm tra xem supplier có phải là supplier phụ không
     */
    public function isSecondarySupplier(): bool
    {
        $type = strtolower($this->getType() ?? '');
        return strpos($type, 'phụ') !== false || strpos($type, 'secondary') !== false;
    }

    /**
     * Kiểm tra xem supplier có phải là supplier ưu tiên không
     */
    public function isPreferredSupplier(): bool
    {
        $rating = $this->getRating();
        return $rating !== null && $rating >= 4.0;
    }

    /**
     * Kiểm tra xem supplier có phải là supplier mới không
     */
    public function isNewSupplier(): bool
    {
        $createdAt = $this->getCreatedAt();
        if (!$createdAt) {
            return false;
        }

        $createdTime = strtotime($createdAt);
        $thirtyDaysAgo = strtotime('-30 days');

        return $createdTime >= $thirtyDaysAgo;
    }

    /**
     * Kiểm tra xem supplier có thể thanh toán chậm không
     */
    public function canPayLater(): bool
    {
        return $this->hasPaymentTerm() && $this->getPaymentTerm() > 0;
    }

    /**
     * Kiểm tra xem supplier có thể mua chịu không
     */
    public function canBuyOnCredit(): bool
    {
        return $this->hasCreditLimit() && $this->getCreditLimit() > 0;
    }

    /**
     * Lấy thông tin liên hệ đầy đủ
     */
    public function getFullContactInfo(): array
    {
        $contactInfo = [];

        if ($this->hasContactPerson()) {
            $contactInfo['contactPerson'] = $this->getContactPerson();
        }

        if ($this->hasPhone()) {
            $contactInfo['phone'] = $this->getFormattedPhone();
        }

        if ($this->hasEmail()) {
            $contactInfo['email'] = $this->getEmail();
        }

        if ($this->hasAddress()) {
            $contactInfo['address'] = $this->getFormattedAddress();
        }

        return $contactInfo;
    }

    /**
     * Lấy thông tin ngân hàng đầy đủ
     */
    public function getFullBankInfo(): array
    {
        if (!$this->hasBankInfo()) {
            return [];
        }

        return [
            'bankName' => $this->getBankName(),
            'bankAccount' => $this->getBankAccount(),
            'formatted' => $this->getFormattedBankInfo()
        ];
    }

    /**
     * Lấy thông tin thanh toán đầy đủ
     */
    public function getFullPaymentInfo(): array
    {
        $paymentInfo = [];

        if ($this->hasPaymentTerm()) {
            $paymentInfo['paymentTerm'] = $this->getPaymentTerm();
            $paymentInfo['paymentTermFormatted'] = $this->getFormattedPaymentTerm();
            $paymentInfo['canPayLater'] = $this->canPayLater();
        }

        if ($this->hasCreditLimit()) {
            $paymentInfo['creditLimit'] = $this->getCreditLimit();
            $paymentInfo['creditLimitFormatted'] = $this->getFormattedCreditLimit();
            $paymentInfo['canBuyOnCredit'] = $this->canBuyOnCredit();
        }

        return $paymentInfo;
    }

    /**
     * Tạo supplier từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo supplier từ tên
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
     * Tạo nhiều suppliers từ array data
     */
    public static function createFromArrayMultiple(array $data): array
    {
        $suppliers = [];

        foreach ($data as $supplierData) {
            $suppliers[] = self::createFromArray($supplierData);
        }

        return $suppliers;
    }

    /**
     * Lọc suppliers theo trạng thái
     */
    public static function filterByStatus(array $suppliers, string $status): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) use ($status) {
            return $supplier->getStatus() === $status;
        });
    }

    /**
     * Lọc suppliers theo type
     */
    public static function filterByType(array $suppliers, string $type): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) use ($type) {
            return $supplier->getType() === $type;
        });
    }

    /**
     * Lọc suppliers chính
     */
    public static function filterMainSuppliers(array $suppliers): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) {
            return $supplier->isMainSupplier();
        });
    }

    /**
     * Lọc suppliers phụ
     */
    public static function filterSecondarySuppliers(array $suppliers): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) {
            return $supplier->isSecondarySupplier();
        });
    }

    /**
     * Lọc suppliers ưu tiên
     */
    public static function filterPreferredSuppliers(array $suppliers): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) {
            return $supplier->isPreferredSupplier();
        });
    }

    /**
     * Lọc suppliers mới
     */
    public static function filterNewSuppliers(array $suppliers): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) {
            return $supplier->isNewSupplier();
        });
    }

    /**
     * Lọc suppliers có thể thanh toán chậm
     */
    public static function filterPayLaterSuppliers(array $suppliers): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) {
            return $supplier->canPayLater();
        });
    }

    /**
     * Lọc suppliers có thể mua chịu
     */
    public static function filterCreditSuppliers(array $suppliers): array
    {
        return array_filter($suppliers, function (ProductSupplier $supplier) {
            return $supplier->canBuyOnCredit();
        });
    }

    /**
     * Sắp xếp suppliers theo tên
     */
    public static function sortByName(array $suppliers, bool $ascending = true): array
    {
        usort($suppliers, function (ProductSupplier $a, ProductSupplier $b) use ($ascending) {
            $nameA = $a->getName() ?? '';
            $nameB = $b->getName() ?? '';

            if ($ascending) {
                return strcmp($nameA, $nameB);
            }

            return strcmp($nameB, $nameA);
        });

        return $suppliers;
    }

    /**
     * Sắp xếp suppliers theo rating
     */
    public static function sortByRating(array $suppliers, bool $descending = true): array
    {
        usort($suppliers, function (ProductSupplier $a, ProductSupplier $b) use ($descending) {
            $ratingA = $a->getRating() ?? 0;
            $ratingB = $b->getRating() ?? 0;

            if ($descending) {
                return $ratingB <=> $ratingA;
            }

            return $ratingA <=> $ratingB;
        });

        return $suppliers;
    }

    /**
     * Sắp xếp suppliers theo ngày tạo
     */
    public static function sortByCreatedAt(array $suppliers, bool $descending = true): array
    {
        usort($suppliers, function (ProductSupplier $a, ProductSupplier $b) use ($descending) {
            $dateA = strtotime($a->getCreatedAt() ?? '1970-01-01');
            $dateB = strtotime($b->getCreatedAt() ?? '1970-01-01');

            if ($descending) {
                return $dateB <=> $dateA;
            }

            return $dateA <=> $dateB;
        });

        return $suppliers;
    }

    /**
     * Tìm suppliers theo tên (partial match)
     */
    public static function searchByName(array $suppliers, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($suppliers, function (ProductSupplier $supplier) use ($searchTerm) {
            $name = strtolower($supplier->getName() ?? '');
            return strpos($name, $searchTerm) !== false;
        });
    }

    /**
     * Tìm suppliers theo code (partial match)
     */
    public static function searchByCode(array $suppliers, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($suppliers, function (ProductSupplier $supplier) use ($searchTerm) {
            $code = strtolower($supplier->getCode() ?? '');
            return strpos($code, $searchTerm) !== false;
        });
    }

    /**
     * Tìm suppliers theo phone (partial match)
     */
    public static function searchByPhone(array $suppliers, string $searchTerm): array
    {
        $searchTerm = preg_replace('/[^0-9]/', '', $searchTerm);

        return array_filter($suppliers, function (ProductSupplier $supplier) use ($searchTerm) {
            $phone = preg_replace('/[^0-9]/', '', $supplier->getPhone() ?? '');
            return strpos($phone, $searchTerm) !== false;
        });
    }

    /**
     * Tìm suppliers theo email (partial match)
     */
    public static function searchByEmail(array $suppliers, string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);

        return array_filter($suppliers, function (ProductSupplier $supplier) use ($searchTerm) {
            $email = strtolower($supplier->getEmail() ?? '');
            return strpos($email, $searchTerm) !== false;
        });
    }

    /**
     * Lấy danh sách types unique
     */
    public static function getUniqueTypes(array $suppliers): array
    {
        $types = [];

        foreach ($suppliers as $supplier) {
            $type = $supplier->getType();
            if ($type && !in_array($type, $types)) {
                $types[] = $type;
            }
        }

        sort($types);
        return $types;
    }

    /**
     * Đếm số suppliers theo type
     */
    public static function countByType(array $suppliers): array
    {
        $counts = [];

        foreach ($suppliers as $supplier) {
            $type = $supplier->getType() ?: 'Unknown';

            if (!isset($counts[$type])) {
                $counts[$type] = 0;
            }

            $counts[$type]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Đếm số suppliers theo status
     */
    public static function countByStatus(array $suppliers): array
    {
        $counts = [];

        foreach ($suppliers as $supplier) {
            $status = $supplier->getStatus() ?: 'Unknown';

            if (!isset($counts[$status])) {
                $counts[$status] = 0;
            }

            $counts[$status]++;
        }

        arsort($counts);
        return $counts;
    }

    /**
     * Lấy thống kê suppliers
     */
    public static function getStatistics(array $suppliers): array
    {
        $stats = [
            'total' => count($suppliers),
            'active' => count(self::filterByStatus($suppliers, 'Active')),
            'inactive' => count(self::filterByStatus($suppliers, 'Inactive')),
            'main' => count(self::filterMainSuppliers($suppliers)),
            'secondary' => count(self::filterSecondarySuppliers($suppliers)),
            'preferred' => count(self::filterPreferredSuppliers($suppliers)),
            'new' => count(self::filterNewSuppliers($suppliers)),
            'payLater' => count(self::filterPayLaterSuppliers($suppliers)),
            'credit' => count(self::filterCreditSuppliers($suppliers)),
            'withPhone' => count(array_filter($suppliers, function (ProductSupplier $s) {
                return $s->hasPhone();
            })),
            'withEmail' => count(array_filter($suppliers, function (ProductSupplier $s) {
                return $s->hasEmail();
            })),
            'withAddress' => count(array_filter($suppliers, function (ProductSupplier $s) {
                return $s->hasAddress();
            })),
            'withBankInfo' => count(array_filter($suppliers, function (ProductSupplier $s) {
                return $s->hasBankInfo();
            }))
        ];

        return $stats;
    }
}
