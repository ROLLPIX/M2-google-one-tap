<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Plugin\Customer;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Math\Random;
use Rollpix\GoogleOneTap\Model\PendingRegistration;

/**
 * Fills a random password when the register form is submitted to complete a pending Google sign-up.
 *
 * The native register controller requires a password, but Google One Tap accounts are passwordless.
 * The password fields are hidden on the form (see register_completion.phtml); here we inject a strong
 * random password server side so account creation succeeds and the customer keeps signing in with Google.
 */
class PasswordlessRegistration
{
    private PendingRegistration $pendingRegistration;

    private RequestInterface $request;

    private Random $mathRandom;

    public function __construct(
        PendingRegistration $pendingRegistration,
        RequestInterface $request,
        Random $mathRandom
    ) {
        $this->pendingRegistration = $pendingRegistration;
        $this->request = $request;
        $this->mathRandom = $mathRandom;
    }

    public function beforeExecute(CreatePost $subject): void
    {
        $pendingRegistration = $this->pendingRegistration->get();
        if ($pendingRegistration === null) {
            return;
        }

        // Honour the explicit "switch to native" opt-out from the register form
        // so a user typing their own password is never overridden.
        if ((string)$this->request->getParam('_onetap_native_optout') === '1') {
            $this->pendingRegistration->clear();
            return;
        }

        // Only inject a password when the submitted email matches the pending Google email.
        // This prevents the passwordless path from being abused for an arbitrary email.
        $submittedEmail = strtolower(trim((string)$this->request->getParam('email')));
        $pendingEmail = strtolower(trim((string)($pendingRegistration['email'] ?? '')));
        if ($pendingEmail === '' || $submittedEmail !== $pendingEmail) {
            return;
        }

        $password = $this->generatePassword();
        $this->request->setParam('password', $password);
        $this->request->setParam('password_confirmation', $password);
    }

    /**
     * Build a random password that satisfies Magento's default strength requirements
     * (minimum length and at least 3 character classes).
     */
    private function generatePassword(): string
    {
        // 20 alphanumeric chars + a fixed suffix guaranteeing upper, lower, digit and special classes.
        return $this->mathRandom->getRandomString(20) . 'Aa1!';
    }
}
