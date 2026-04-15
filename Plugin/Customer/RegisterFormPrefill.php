<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Plugin\Customer;

use Magento\Customer\Block\Form\Register;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;

class RegisterFormPrefill
{
    private const SESSION_KEY = 'rollpix_google_onetap_pending_registration';

    public function __construct(
        private readonly Session $customerSession
    ) {}

    public function afterGetFormData(Register $subject, DataObject $result): DataObject
    {
        $pendingRegistration = $this->customerSession->getData(self::SESSION_KEY);
        if (!is_array($pendingRegistration)) {
            return $result;
        }

        if (!$result->getFirstname() && !empty($pendingRegistration['firstname'])) {
            $result->setFirstname((string)$pendingRegistration['firstname']);
        }

        if (!$result->getLastname() && !empty($pendingRegistration['lastname'])) {
            $result->setLastname((string)$pendingRegistration['lastname']);
        }

        if (!$result->getEmail() && !empty($pendingRegistration['email'])) {
            $result->setEmail((string)$pendingRegistration['email']);
        }

        if ($result->getFirstname() || $result->getLastname() || $result->getEmail()) {
            $result->setCustomerData(1);
        }

        return $result;
    }
}
