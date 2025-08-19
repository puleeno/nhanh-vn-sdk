<?php

namespace Puleeno\NhanhVn\Services;

/**
 * Cache Service
 */
class CacheService
{
    protected array $cache = [];
    protected array $expiry = [];

    /**
     * Lưu dữ liệu vào cache
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $this->cache[$key] = $value;
        $this->expiry[$key] = time() + $ttl;

        return true;
    }

    /**
     * Lấy dữ liệu từ cache
     */
    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            return null;
        }

        if ($this->isExpired($key)) {
            $this->delete($key);
            return null;
        }

        return $this->cache[$key];
    }

    /**
     * Kiểm tra xem key có tồn tại trong cache không
     */
    public function has(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    /**
     * Xóa dữ liệu khỏi cache
     */
    public function delete(string $key): bool
    {
        unset($this->cache[$key]);
        unset($this->expiry[$key]);

        return true;
    }

    /**
     * Xóa tất cả cache
     */
    public function clear(): bool
    {
        $this->cache = [];
        $this->expiry = [];

        return true;
    }

    /**
     * Kiểm tra xem key có hết hạn không
     */
    public function isExpired(string $key): bool
    {
        if (!isset($this->expiry[$key])) {
            return true;
        }

        return time() > $this->expiry[$key];
    }

    /**
     * Lấy thời gian còn lại của key
     */
    public function getTtl(string $key): int
    {
        if (!isset($this->expiry[$key])) {
            return 0;
        }

        $remaining = $this->expiry[$key] - time();
        return max(0, $remaining);
    }

    /**
     * Lấy danh sách tất cả keys trong cache
     */
    public function getKeys(): array
    {
        return array_keys($this->cache);
    }

    /**
     * Lấy thống kê cache
     */
    public function getStats(): array
    {
        $totalKeys = count($this->cache);
        $expiredKeys = 0;
        $activeKeys = 0;

        foreach ($this->cache as $key => $value) {
            if ($this->isExpired($key)) {
                $expiredKeys++;
            } else {
                $activeKeys++;
            }
        }

        return [
            'total_keys' => $totalKeys,
            'active_keys' => $activeKeys,
            'expired_keys' => $expiredKeys,
            'memory_usage' => $this->getMemoryUsage()
        ];
    }

    /**
     * Lấy thông tin memory usage
     */
    protected function getMemoryUsage(): string
    {
        $memory = memory_get_usage(true);

        if ($memory < 1024) {
            return $memory . ' B';
        } elseif ($memory < 1024 * 1024) {
            return round($memory / 1024, 2) . ' KB';
        } else {
            return round($memory / (1024 * 1024), 2) . ' MB';
        }
    }

    /**
     * Dọn dẹp cache hết hạn
     */
    public function cleanup(): int
    {
        $cleaned = 0;

        foreach ($this->cache as $key => $value) {
            if ($this->isExpired($key)) {
                $this->delete($key);
                $cleaned++;
            }
        }

        return $cleaned;
    }

    /**
     * Lấy nhiều keys cùng lúc
     */
    public function getMultiple(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    /**
     * Lưu nhiều keys cùng lúc
     */
    public function setMultiple(array $values, int $ttl = 3600): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * Xóa nhiều keys cùng lúc
     */
    public function deleteMultiple(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * Tăng giá trị của key
     */
    public function increment(string $key, int $value = 1): int
    {
        $current = $this->get($key) ?? 0;
        $newValue = $current + $value;

        $this->set($key, $newValue);

        return $newValue;
    }

    /**
     * Giảm giá trị của key
     */
    public function decrement(string $key, int $value = 1): int
    {
        $current = $this->get($key) ?? 0;
        $newValue = $current - $value;

        $this->set($key, $newValue);

        return $newValue;
    }

    /**
     * Lấy và xóa key
     */
    public function pull(string $key): mixed
    {
        $value = $this->get($key);
        $this->delete($key);

        return $value;
    }

    /**
     * Lấy hoặc lưu giá trị mặc định
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    /**
     * Lấy hoặc lưu giá trị mặc định (vĩnh viễn)
     */
    public function rememberForever(string $key, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, 0); // 0 = không bao giờ hết hạn

        return $value;
    }

    /**
     * Kiểm tra xem key có tồn tại và chưa hết hạn không
     */
    public function exists(string $key): bool
    {
        return $this->has($key) && !$this->isExpired($key);
    }

    /**
     * Lấy thời gian hết hạn của key
     */
    public function getExpiry(string $key): ?int
    {
        return $this->expiry[$key] ?? null;
    }

    /**
     * Gia hạn key
     */
    public function touch(string $key, int $ttl = 3600): bool
    {
        if (!$this->has($key)) {
            return false;
        }

        $this->expiry[$key] = time() + $ttl;

        return true;
    }

    /**
     * Lấy tất cả cache data (debug purpose)
     */
    public function getAll(): array
    {
        return [
            'cache' => $this->cache,
            'expiry' => $this->expiry
        ];
    }
}
