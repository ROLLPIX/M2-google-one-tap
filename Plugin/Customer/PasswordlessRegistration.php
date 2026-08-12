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

        // Authoritative native-vs-Google signal: a user who typed their own password is
        // doing a native registration, never a passwordless Google completion. Drop the
        // pending registration so the completion observer cannot link this account to
        // Google. Unlike the client-side opt-out flag below, this cannot be lost to a
        // re-validation round-trip, a Knockout re-render or browser autofill.
        if ((string)$this->request->getParam('password') !== '') {
            $this->pendingRegistration->clear();
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

        // Record the injection in the session. Observer\CompletePendingRegistration runs later
        // in this same request and rejects registrations that carry a password, on the premise
        // that only a native sign-up has one — without this flag it would read the password we
        // just injected and refuse to link the account to Google.
        $this->pendingRegistration->markPasswordInjected();
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
