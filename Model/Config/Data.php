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

    // ── Enhanced One Tap config ────────────────────────────────────────

    /**
     * Get One Tap prompt context (signin, signup, use)
     *
     * @return string
     */
    public function getContext(): string
    {
        return (string)($this->scopeConfig->getValue(API::XML_CONTEXT, ScopeInterface::SCOPE_WEBSITE) ?: 'signin');
    }

    /**
     * Get prompt parent element CSS selector
     *
     * @return string
     */
    public function getPromptParentId(): string
    {
        return (string)$this->scopeConfig->getValue(API::XML_PROMPT_PARENT_ID, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Check if ITP (Intelligent Tracking Prevention) support is enabled
     * Defaults to true for backward compatibility
     *
     * @return bool
     */
    public function isItpSupport(): bool
    {
        $value = $this->scopeConfig->getValue(API::XML_ITP_SUPPORT, ScopeInterface::SCOPE_WEBSITE);
        return $value === null ? true : (bool)$value;
    }

    /**
     * Check if close callback message is enabled
     *
     * @return bool
     */
    public function isCloseCallbackEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(API::XML_CLOSE_CALLBACK, ScopeInterface::SCOPE_WEBSITE);
    }

    // ── Google Sign-In Button config ────────────────────────────────────

    /**
     * Check if Google Sign-In button is enabled
     *
     * @param int $websiteId
     * @return bool
     */
    public function isButtonEnabled(int $websiteId): bool
    {
        return $this->scopeConfig->isSetFlag(API::XML_BUTTON_ENABLED, ScopeInterface::SCOPE_WEBSITE, $websiteId);
    }

    /**
     * Get pages where button should be displayed
     *
     * @return array
     */
    public function getButtonPages(): array
    {
        $value = (string)$this->scopeConfig->getValue(API::XML_BUTTON_PAGES, ScopeInterface::SCOPE_WEBSITE);
        return $value ? explode(',', $value) : [];
    }

    /**
     * Get custom CSS selector for button placement
     *
     * @return string
     */
    public function getButtonCustomSelector(): string
    {
        return (string)$this->scopeConfig->getValue(API::XML_BUTTON_CUSTOM_SELECTOR, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Get button theme
     *
     * @return string
     */
    public function getButtonTheme(): string
    {
        return (string)($this->scopeConfig->getValue(API::XML_BUTTON_THEME, ScopeInterface::SCOPE_WEBSITE) ?: 'outline');
    }

    /**
     * Get button size
     *
     * @return string
     */
    public function getButtonSize(): string
    {
        return (string)($this->scopeConfig->getValue(API::XML_BUTTON_SIZE, ScopeInterface::SCOPE_WEBSITE) ?: 'large');
    }

    /**
     * Get button shape
     *
     * @return string
     */
    public function getButtonShape(): string
    {
        return (string)($this->scopeConfig->getValue(API::XML_BUTTON_SHAPE, ScopeInterface::SCOPE_WEBSITE) ?: 'rectangular');
    }

    /**
     * Get button text
     *
     * @return string
     */
    public function getButtonText(): string
    {
        return (string)($this->scopeConfig->getValue(API::XML_BUTTON_TEXT, ScopeInterface::SCOPE_WEBSITE) ?: 'signin_with');
    }

    /**
     * Get button logo alignment
     *
     * @return string
     */
    public function getButtonLogoAlignment(): string
    {
        return (string)($this->scopeConfig->getValue(API::XML_BUTTON_LOGO_ALIGNMENT, ScopeInterface::SCOPE_WEBSITE) ?: 'left');
    }

    // ── Amasty compatibility ────────────────────────────────────────────

    /**
     * Check if Google button should be disabled when Amasty Social Login is present
     *
     * @return bool
     */
    public function isDisableButtonWhenAmasty(): bool
    {
        return $this->scopeConfig->isSetFlag(API::XML_COMPAT_DISABLE_BUTTON_AMASTY, ScopeInterface::SCOPE_WEBSITE);
    }

    // ── Security & Logging ──────────────────────────────────────────────

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
