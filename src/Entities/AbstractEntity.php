<?php

namespace Puleeno\NhanhVn\Entities;

use Puleeno\NhanhVn\Contracts\EntityInterface;

/**
 * Abstract class cơ bản cho tất cả entities
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * Entity attributes
     */
    protected array $attributes = [];

    /**
     * Validation rules
     */
    protected array $rules = [];

    /**
     * Validation errors
     */
    protected array $errors = [];

    /**
     * Constructor
     */
    public function __construct(array $data = [])
    {
        $this->attributes = $data;
        $this->validate();
    }

    /**
     * Get attribute value
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Set attribute value
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Check if attribute exists
     */
    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get all attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Convert entity to array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Convert entity to JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->attributes, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get entity identifier
     */
    public function getId(): mixed
    {
        return $this->getAttribute('id');
    }

    /**
     * Check if entity is valid
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add validation error
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Validate entity
     */
    abstract protected function validate(): void;

    /**
     * Magic method for getting attributes
     */
    public function __get(string $name): mixed
    {
        return $this->getAttribute($name);
    }

    /**
     * Magic method for setting attributes
     */
    public function __set(string $name, mixed $value): void
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Magic method for checking attribute existence
     */
    public function __isset(string $name): bool
    {
        return $this->hasAttribute($name);
    }
}
