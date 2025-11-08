<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Block;

use Magento\Customer\Model\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Http\Context as AuthContext;
use Rollpix\GoogleOneTap\Model\Config\Data;

class OneTap extends Template
{

    /**
     * @param Template\Context $context
     * @param Data $config
     * @param AuthContext $authContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private readonly Data $config,
        private readonly AuthContext $authContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
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
     * Get background click disable config
     *
     * @return bool
     */
    public function getClickDisable(): bool
    {
        return $this->config->getClickDisable();
    }

    /**
     * Get auto sign-in config
     *
     * @return bool
     */
    public function getAutoSign(): bool
    {
        return $this->config->getAutoSign();
    }

    /**
     * Get position config
     *
     * @return string
     */
    public function getPosition(): string
    {
        return $this->config->getPosition();
    }

    /**
     * Prepare config
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isEnable(): bool
    {
        $websiteId = (int)$this->_storeManager->getWebsite()->getId();

        return $this->config->isEnable($websiteId);
    }
}
