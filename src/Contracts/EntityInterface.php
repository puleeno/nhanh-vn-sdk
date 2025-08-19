<?php

namespace Puleeno\NhanhVn\Contracts;

/**
 * Interface cơ bản cho tất cả entities
 */
interface EntityInterface
{
    /**
     * Convert entity to array
     */
    public function toArray(): array;

    /**
     * Convert entity to JSON string
     */
    public function toJson(): string;

    /**
     * Get entity identifier
     */
    public function getId(): mixed;

    /**
     * Check if entity is valid
     */
    public function isValid(): bool;
}
