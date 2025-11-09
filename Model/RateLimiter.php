<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Rate limiter to prevent brute force attacks on authentication endpoint
 */
class RateLimiter
{
    private const CACHE_PREFIX = 'google_one_tap_rate_limit_';
    private const DEFAULT_MAX_ATTEMPTS = 10;
    private const DEFAULT_TIME_WINDOW = 60; // seconds

    /**
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * Check if IP address has exceeded rate limit
     *
     * @param string $ipAddress
     * @param int $maxAttempts
     * @param int $timeWindow Time window in seconds
     * @return bool True if rate limit exceeded
     */
    public function isRateLimitExceeded(
        string $ipAddress,
        int $maxAttempts = self::DEFAULT_MAX_ATTEMPTS,
        int $timeWindow = self::DEFAULT_TIME_WINDOW
    ): bool {
        $cacheKey = $this->getCacheKey($ipAddress);
        $data = $this->getRateLimitData($cacheKey);

        if (!$data) {
            return false;
        }

        // Clean old attempts outside time window
        $currentTime = time();
        $data['attempts'] = array_filter(
            $data['attempts'],
            fn($timestamp) => ($currentTime - $timestamp) < $timeWindow
        );

        return count($data['attempts']) >= $maxAttempts;
    }

    /**
     * Record an authentication attempt
     *
     * @param string $ipAddress
     * @param int $timeWindow
     * @return void
     */
    public function recordAttempt(string $ipAddress, int $timeWindow = self::DEFAULT_TIME_WINDOW): void
    {
        $cacheKey = $this->getCacheKey($ipAddress);
        $data = $this->getRateLimitData($cacheKey);

        if (!$data) {
            $data = ['attempts' => []];
        }

        // Add current attempt
        $data['attempts'][] = time();

        // Save to cache with TTL
        $this->cache->save(
            $this->serializer->serialize($data),
            $cacheKey,
            [],
            $timeWindow
        );
    }

    /**
     * Reset rate limit for an IP address
     *
     * @param string $ipAddress
     * @return void
     */
    public function resetRateLimit(string $ipAddress): void
    {
        $cacheKey = $this->getCacheKey($ipAddress);
        $this->cache->remove($cacheKey);
    }

    /**
     * Get cache key for IP address
     *
     * @param string $ipAddress
     * @return string
     */
    private function getCacheKey(string $ipAddress): string
    {
        return self::CACHE_PREFIX . hash('sha256', $ipAddress);
    }

    /**
     * Get rate limit data from cache
     *
     * @param string $cacheKey
     * @return array|null
     */
    private function getRateLimitData(string $cacheKey): ?array
    {
        $cached = $this->cache->load($cacheKey);

        if (!$cached) {
            return null;
        }

        try {
            return $this->serializer->unserialize($cached);
        } catch (\Exception $e) {
            return null;
        }
    }
}
