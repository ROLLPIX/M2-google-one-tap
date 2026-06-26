<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Plugin\Customer;

use Magento\Customer\Block\Form\Register;
use Magento\Framework\DataObject;
use Rollpix\GoogleOneTap\Model\PendingRegistration;

class RegisterFormPrefill
{
    private PendingRegistration $pendingRegistration;

    public function __construct(
        PendingRegistration $pendingRegistration
    ) {
        $this->pendingRegistration = $pendingRegistration;
    }

    public function afterGetFormData(Register $subject, DataObject $result): DataObject
    {
        $pendingRegistration = $this->pendingRegistration->get();
        if ($pendingRegistration === null) {
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
