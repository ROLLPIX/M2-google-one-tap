<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Rollpix\GoogleOneTap\Model\API\DataInterface as API;

class Data implements API
{

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {}

    /**
     * Get Google client ID from Config
     *
     * @param int $websiteId
     * @return string
     * @throws LocalizedException
     */
    public function getClientId(int $websiteId): string
    {
        $encryptedValue = $this->scopeConfig->getValue(
            API::XML_CLIENT_ID,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        if (empty($encryptedValue)) {
            throw new LocalizedException(__('Google Client ID is not configured.'));
        }

        return $this->encryptor->decrypt($encryptedValue);
    }

    /**
     * Get background click disable config
     *
     * @return bool
     */
    public function getClickDisable(): bool
    {
        return (bool)$this->scopeConfig->getValue(API::XML_BCG_CLICK, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get auto sign-in config
     *
     * @return bool
     */
    public function getAutoSign(): bool
    {
        return (bool)$this->scopeConfig->getValue(API::XML_AUTO_SIGN_IN, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get position config
     *
     * @return string
     */
    public function getPosition(): string
    {
        return (string)$this->scopeConfig->getValue(API::XML_POSITION, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get config
     *
     * @param int $websiteId
     * @return bool
     */
    public function isEnable(int $websiteId): bool
    {
        return $this->scopeConfig->isSetFlag(API::XML_STATUS, ScopeInterface::SCOPE_WEBSITE, $websiteId);
    }

    /**
     * Check if debug logging is enabled
     *
     * @return bool
     */
    public function isDebugLoggingEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(API::XML_DEBUG_LOGGING, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Check if rate limiting is enabled
     *
     * @return bool
     */
    public function isRateLimitEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(API::XML_RATE_LIMIT_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get maximum allowed attempts for rate limiting
     *
     * @return int
     */
    public function getRateLimitMaxAttempts(): int
    {
        return (int)($this->scopeConfig->getValue(
            API::XML_RATE_LIMIT_MAX_ATTEMPTS,
            ScopeInterface::SCOPE_WEBSITE
        ) ?: 10);
    }

    /**
     * Get time window for rate limiting (in seconds)
     *
     * @return int
     */
    public function getRateLimitTimeWindow(): int
    {
        return (int)($this->scopeConfig->getValue(
            API::XML_RATE_LIMIT_TIME_WINDOW,
            ScopeInterface::SCOPE_WEBSITE
        ) ?: 60);
    }
}
