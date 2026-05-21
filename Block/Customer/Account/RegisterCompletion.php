<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Block\Customer\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class RegisterCompletion extends Template
{
    private const SESSION_KEY = 'rollpix_google_onetap_pending_registration';

    private Session $customerSession;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Whether the register form is being shown to complete a pending Google sign-up.
     *
     * When true the password fields must be hidden so the passwordless Google flow is preserved.
     *
     * @return bool
     */
    public function isPendingGoogleRegistration(): bool
    {
        $pendingRegistration = $this->customerSession->getData(self::SESSION_KEY);

        return is_array($pendingRegistration) && !empty($pendingRegistration['provider']);
    }
}
