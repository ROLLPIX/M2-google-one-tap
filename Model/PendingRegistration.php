<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model;

use Magento\Customer\Model\Session;

/**
 * Wraps the pending Google sign-up data stored in the customer session.
 *
 * The data is treated as expired after TTL_SECONDS so a stale entry left by an
 * incomplete Google flow does not silently break a later native registration.
 */
class PendingRegistration
{
    public const SESSION_KEY = 'rollpix_google_onetap_pending_registration';
    public const TTL_SECONDS = 600;

    /**
     * Marks that the password on this request was injected by the module, not typed by the
     * user. Lives in the session (not in a request param) so it cannot be forged by a POST.
     */
    public const FLAG_PASSWORD_INJECTED = 'password_injected';

    private Session $customerSession;

    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Return the pending registration data when present and still fresh.
     * Stale entries are cleared transparently and null is returned.
     */
    public function get(): ?array
    {
        $data = $this->customerSession->getData(self::SESSION_KEY);
        if (!is_array($data) || empty($data['provider'])) {
            return null;
        }

        // Treat absence of created_at (legacy entries written before this field
        // existed) as immediately expired — otherwise they would linger forever.
        $createdAt = isset($data['created_at']) ? (int)$data['created_at'] : 0;
        if ($createdAt <= 0 || (time() - $createdAt) > self::TTL_SECONDS) {
            // Stale: also drop the cached customer_form_data populated alongside
            // the pending registration so the form does not stay prefilled.
            $this->customerSession->unsCustomerFormData();
            $this->clear();
            return null;
        }

        return $data;
    }

    public function set(array $data): void
    {
        $data['created_at'] = time();
        $this->customerSession->setData(self::SESSION_KEY, $data);
    }

    /**
     * Flag the pending registration as carrying a module-injected password.
     *
     * Written straight onto the stored array so `created_at` — and therefore the TTL — is
     * preserved; going through set() would silently restart the 10 minute window.
     */
    public function markPasswordInjected(): void
    {
        $data = $this->customerSession->getData(self::SESSION_KEY);
        if (!is_array($data)) {
            return;
        }

        $data[self::FLAG_PASSWORD_INJECTED] = true;
        $this->customerSession->setData(self::SESSION_KEY, $data);
    }

    public function clear(): void
    {
        $this->customerSession->unsData(self::SESSION_KEY);
    }
}
