<?php

namespace Puleeno\NhanhVn\Entities\Customer;

use Puleeno\NhanhVn\Entities\AbstractEntity;
use Puleeno\NhanhVn\Exceptions\InvalidDataException;

/**
 * Customer Search Response Entity
 *
 * Represents the response from customer search API
 *
 * @package Puleeno\NhanhVn\Entities\Customer
 */
class CustomerSearchResponse extends AbstractEntity
{
    protected const REQUIRED_FIELDS = ['code'];
    protected const OPTIONAL_FIELDS = ['messages', 'data'];

    /**
     * Response codes
     */
    public const SUCCESS_CODE = 1;
    public const ERROR_CODE = 0;

    /**
     * Validate response data
     *
     * @throws \Puleeno\NhanhVn\Exceptions\InvalidDataException
     */
    protected function validate(): void
    {
        $code = $this->getAttribute('code');

        if (!in_array($code, [self::SUCCESS_CODE, self::ERROR_CODE])) {
            throw new InvalidDataException('Mã phản hồi không hợp lệ');
        }

        // If error, messages should be present
        if ($code === self::ERROR_CODE && !$this->hasAttribute('messages')) {
            throw new InvalidDataException('Thông báo lỗi không được cung cấp');
        }

        // If success, data should be present
        if ($code === self::SUCCESS_CODE && !$this->hasAttribute('data')) {
            throw new InvalidDataException('Dữ liệu không được cung cấp');
        }
    }

    /**
     * Get response code
     *
     * @return int
     */
    public function getCode(): int
    {
        return (int) $this->getAttribute('code');
    }

    /**
     * Get error messages
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->hasAttribute('messages') ? $this->getAttribute('messages') : [];
    }

    /**
     * Get response data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->hasAttribute('data') ? $this->getAttribute('data') : [];
    }

    /**
     * Check if response is successful
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->getCode() === self::SUCCESS_CODE;
    }

    /**
     * Check if response has error
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->getCode() === self::ERROR_CODE;
    }

    /**
     * Get first error message
     *
     * @return string|null
     */
    public function getFirstMessage(): ?string
    {
        $messages = $this->getMessages();
        return !empty($messages) ? $messages[0] : null;
    }

    /**
     * Get all messages as string
     *
     * @param string $separator
     * @return string
     */
    public function getAllMessagesAsString(string $separator = '; '): string
    {
        return implode($separator, $this->getMessages());
    }

    /**
     * Get total pages
     *
     * @return int
     */
    public function getTotalPages(): int
    {
        $data = $this->getData();
        return isset($data['totalPages']) ? (int) $data['totalPages'] : 0;
    }

    /**
     * Get customers array
     *
     * @return array
     */
    public function getCustomers(): array
    {
        $data = $this->getData();
        return isset($data['customers']) ? $data['customers'] : [];
    }

    /**
     * Get total customers count
     *
     * @return int
     */
    public function getTotalCustomers(): int
    {
        return count($this->getCustomers());
    }

    /**
     * Check if has customers
     *
     * @return bool
     */
    public function hasCustomers(): bool
    {
        return $this->getTotalCustomers() > 0;
    }

    /**
     * Get first customer
     *
     * @return array|null
     */
    public function getFirstCustomer(): ?array
    {
        $customers = $this->getCustomers();
        return !empty($customers) ? $customers[0] : null;
    }

    /**
     * Get customer by index
     *
     * @param int $index
     * @return array|null
     */
    public function getCustomerByIndex(int $index): ?array
    {
        $customers = $this->getCustomers();
        return isset($customers[$index]) ? $customers[$index] : null;
    }

    /**
     * Get customers by type
     *
     * @param int $type
     * @return array
     */
    public function getCustomersByType(int $type): array
    {
        $filteredCustomers = [];
        $customers = $this->getCustomers();

        foreach ($customers as $customer) {
            if (isset($customer['type']) && (int) $customer['type'] === $type) {
                $filteredCustomers[] = $customer;
            }
        }

        return $filteredCustomers;
    }

    /**
     * Get customers by gender
     *
     * @param int $gender
     * @return array
     */
    public function getCustomersByGender(int $gender): array
    {
        $filteredCustomers = [];
        $customers = $this->getCustomers();

        foreach ($customers as $customer) {
            if (isset($customer['gender']) && (int) $customer['gender'] === $gender) {
                $filteredCustomers[] = $customer;
            }
        }

        return $filteredCustomers;
    }

    /**
     * Get customers with mobile
     *
     * @return array
     */
    public function getCustomersWithMobile(): array
    {
        $filteredCustomers = [];
        $customers = $this->getCustomers();

        foreach ($customers as $customer) {
            if (!empty($customer['mobile'])) {
                $filteredCustomers[] = $customer;
            }
        }

        return $filteredCustomers;
    }

    /**
     * Get customers with email
     *
     * @return array
     */
    public function getCustomersWithEmail(): array
    {
        $filteredCustomers = [];
        $customers = $this->getCustomers();

        foreach ($customers as $customer) {
            if (!empty($customer['email'])) {
                $filteredCustomers[] = $customer;
            }
        }

        return $filteredCustomers;
    }

    /**
     * Get customers with address
     *
     * @return array
     */
    public function getCustomersWithAddress(): array
    {
        $filteredCustomers = [];
        $customers = $this->getCustomers();

        foreach ($customers as $customer) {
            if (!empty($customer['address'])) {
                $filteredCustomers[] = $customer;
            }
        }

        return $filteredCustomers;
    }

    /**
     * Get summary statistics
     *
     * @return array
     */
    public function getSummary(): array
    {
        $customers = $this->getCustomers();
        $totalCustomers = count($customers);

        if ($totalCustomers === 0) {
            return [
                'totalCustomers' => 0,
                'totalPages' => $this->getTotalPages(),
                'hasData' => false
            ];
        }

        $typeCounts = [
            'retail' => 0,
            'wholesale' => 0,
            'agent' => 0,
            'unknown' => 0
        ];

        $genderCounts = [
            'male' => 0,
            'female' => 0,
            'unknown' => 0
        ];

        $hasMobile = 0;
        $hasEmail = 0;
        $hasAddress = 0;

        foreach ($customers as $customer) {
            // Count by type
            if (isset($customer['type'])) {
                switch ((int) $customer['type']) {
                    case Customer::TYPE_RETAIL:
                        $typeCounts['retail']++;
                        break;
                    case Customer::TYPE_WHOLESALE:
                        $typeCounts['wholesale']++;
                        break;
                    case Customer::TYPE_AGENT:
                        $typeCounts['agent']++;
                        break;
                    default:
                        $typeCounts['unknown']++;
                        break;
                }
            } else {
                $typeCounts['unknown']++;
            }

            // Count by gender
            if (isset($customer['gender'])) {
                switch ((int) $customer['gender']) {
                    case Customer::GENDER_MALE:
                        $genderCounts['male']++;
                        break;
                    case Customer::GENDER_FEMALE:
                        $genderCounts['female']++;
                        break;
                    default:
                        $genderCounts['unknown']++;
                        break;
                }
            } else {
                $genderCounts['unknown']++;
            }

            // Count additional fields
            if (!empty($customer['mobile'])) {
                $hasMobile++;
            }
            if (!empty($customer['email'])) {
                $hasEmail++;
            }
            if (!empty($customer['address'])) {
                $hasAddress++;
            }
        }

        return [
            'totalCustomers' => $totalCustomers,
            'totalPages' => $this->getTotalPages(),
            'hasData' => true,
            'typeDistribution' => $typeCounts,
            'genderDistribution' => $genderCounts,
            'fieldCompleteness' => [
                'mobile' => $hasMobile,
                'email' => $hasEmail,
                'address' => $hasAddress
            ]
        ];
    }

    /**
     * Create from API response
     *
     * @param array $response
     * @return self
     */
    public static function createFromApiResponse(array $response): self
    {
        return new self($response);
    }

    /**
     * Create success response
     *
     * @param array $data
     * @return self
     */
    public static function createSuccessResponse(array $data): self
    {
        return new self([
            'code' => self::SUCCESS_CODE,
            'data' => $data
        ]);
    }

    /**
     * Create error response
     *
     * @param array $messages
     * @return self
     */
    public static function createErrorResponse(array $messages): self
    {
        return new self([
            'code' => self::ERROR_CODE,
            'messages' => $messages
        ]);
    }
}
