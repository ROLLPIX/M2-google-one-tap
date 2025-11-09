<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Block\Customer\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class GoogleLinkedNotice extends Template
{
    /**
     * Time threshold in seconds to consider account as created via Google One Tap
     * If linked_at is within 60 seconds of created_at, it's a new Google account
     */
    private const TIME_THRESHOLD_SECONDS = 60;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Check if customer account was created via Google One Tap (not linked afterward)
     *
     * @return bool
     */
    public function isGoogleOneTapCreated(): bool
    {
        $customer = $this->customerSession->getCustomer();

        if (!$customer || !$customer->getId()) {
            return false;
        }

        $linkedAt = $customer->getData('google_onetap_linked_at');
        $googleEmail = $customer->getData('google_onetap_email');

        // Must have both linked timestamp and Google email
        if (empty($linkedAt) || empty($googleEmail)) {
            return false;
        }

        // Get customer creation time
        $createdAt = $customer->getCreatedAt();

        if (empty($createdAt)) {
            return false;
        }

        // Parse timestamps
        $createdTimestamp = strtotime($createdAt);
        $linkedTimestamp = strtotime($linkedAt);

        // If linking happened within threshold of account creation, it's a new Google account
        $timeDifference = abs($linkedTimestamp - $createdTimestamp);

        return $timeDifference <= self::TIME_THRESHOLD_SECONDS;
    }

    /**
     * Get Google email associated with this account
     *
     * @return string
     */
    public function getGoogleEmail(): string
    {
        $customer = $this->customerSession->getCustomer();

        if (!$customer || !$customer->getId()) {
            return '';
        }

        return (string)$customer->getData('google_onetap_email');
    }

    /**
     * Check if customer account is linked with Google One Tap (created or linked)
     *
     * @return bool
     */
    public function isGoogleOneTapLinked(): bool
    {
        $customer = $this->customerSession->getCustomer();

        if (!$customer || !$customer->getId()) {
            return false;
        }

        $linkedAt = $customer->getData('google_onetap_linked_at');
        $googleEmail = $customer->getData('google_onetap_email');

        // Must have both linked timestamp and Google email
        return !empty($linkedAt) && !empty($googleEmail);
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }
}
