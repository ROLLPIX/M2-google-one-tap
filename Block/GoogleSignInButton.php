<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Block;

use Magento\Customer\Model\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Http\Context as AuthContext;
use Rollpix\GoogleOneTap\Model\Config\Data;

class GoogleSignInButton extends Template
{

    /**
     * @param Template\Context $context
     * @param Data $config
     * @param AuthContext $authContext
     * @param ModuleManager $moduleManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private readonly Data $config,
        private readonly AuthContext $authContext,
        private readonly ModuleManager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Check if the button should render on this page
     *
     * @return bool
     */
    public function shouldRender(): bool
    {
        try {
            $websiteId = (int)$this->_storeManager->getWebsite()->getId();

            if (!$this->config->isEnable($websiteId)) {
                return false;
            }
            if (!$this->config->isButtonEnabled($websiteId)) {
                return false;
            }
            if ($this->isCustomerLoggedIn()) {
                return false;
            }

            // Amasty compatibility check
            if ($this->config->isDisableButtonWhenAmasty()
                && $this->moduleManager->isEnabled('Amasty_SocialLogin')) {
                return false;
            }

            // Check if this page type is configured to show the button
            $pages = $this->config->getButtonPages();
            $currentPage = $this->getData('page_type') ?: '';

            if (!empty($currentPage) && !in_array($currentPage, $pages)) {
                return false;
            }

            return true;
        } catch (LocalizedException $e) {
            return false;
        }
    }

    /**
     * Check is Customer Logged In
     *
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        return $this->authContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * Get Google client ID from Config
     *
     * @return string
     * @throws LocalizedException
     */
    public function getClientId(): string
    {
        $websiteId = (int)$this->_storeManager->getWebsite()->getId();
        return $this->config->getClientId($websiteId);
    }

    /**
     * Get callback URL for Google login
     *
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->getUrl('onetaplogin/checkout/response');
    }

    /**
     * Get button configuration as JSON for JavaScript
     *
     * @return string
     */
    public function getButtonConfigJson(): string
    {
        return json_encode([
            'theme' => $this->config->getButtonTheme(),
            'size' => $this->config->getButtonSize(),
            'shape' => $this->config->getButtonShape(),
            'text' => $this->config->getButtonText(),
            'logo_alignment' => $this->config->getButtonLogoAlignment(),
        ]);
    }

    /**
     * Get custom CSS selector for button placement
     *
     * @return string
     */
    public function getButtonCustomSelector(): string
    {
        $pages = $this->config->getButtonPages();
        if (in_array('custom', $pages)) {
            return $this->config->getButtonCustomSelector();
        }
        return '';
    }

    /**
     * Check if custom CSS selector page is configured
     *
     * @return bool
     */
    public function hasCustomSelector(): bool
    {
        return !empty($this->getButtonCustomSelector());
    }
}
