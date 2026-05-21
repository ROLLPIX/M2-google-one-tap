<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Plugin\Customer;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Math\Random;

/**
 * Fills a random password when the register form is submitted to complete a pending Google sign-up.
 *
 * The native register controller requires a password, but Google One Tap accounts are passwordless.
 * The password fields are hidden on the form (see register_completion.phtml); here we inject a strong
 * random password server side so account creation succeeds and the customer keeps signing in with Google.
 */
class PasswordlessRegistration
{
    private const SESSION_KEY = 'rollpix_google_onetap_pending_registration';

    private Session $customerSession;

    private RequestInterface $request;

    private Random $mathRandom;

    /**
     * @param Session $customerSession
     * @param RequestInterface $request
     * @param Random $mathRandom
     */
    public function __construct(
        Session $customerSession,
        RequestInterface $request,
        Random $mathRandom
    ) {
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->mathRandom = $mathRandom;
    }

    /**
     * @param CreatePost $subject
     * @return void
     */
    public function beforeExecute(CreatePost $subject): void
    {
        $pendingRegistration = $this->customerSession->getData(self::SESSION_KEY);
        if (!is_array($pendingRegistration) || empty($pendingRegistration['provider'])) {
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
     *
     * @return string
     */
    private function generatePassword(): string
    {
        // 20 alphanumeric chars + a fixed suffix guaranteeing upper, lower, digit and special classes.
        return $this->mathRandom->getRandomString(20) . 'Aa1!';
    }
}
