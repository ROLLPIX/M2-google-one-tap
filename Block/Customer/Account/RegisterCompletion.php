<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Block\Customer\Account;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Rollpix\GoogleOneTap\Model\PendingRegistration;

class RegisterCompletion extends Template
{
    private PendingRegistration $pendingRegistration;

    public function __construct(
        Context $context,
        PendingRegistration $pendingRegistration,
        array $data = []
    ) {
        $this->pendingRegistration = $pendingRegistration;
        parent::__construct($context, $data);
    }

    /**
     * Whether the register form is being shown to complete a pending Google sign-up.
     *
     * When true the password fields must be hidden so the passwordless Google flow is preserved.
     */
    public function isPendingGoogleRegistration(): bool
    {
        return $this->pendingRegistration->get() !== null;
    }

    /**
     * Email captured from Google for the pending sign-up. Empty if not pending.
     */
    public function getPendingEmail(): string
    {
        $data = $this->pendingRegistration->get();
        return (string)($data['email'] ?? '');
    }
}
