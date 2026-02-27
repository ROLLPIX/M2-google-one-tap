<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Math\Random;
use Psr\Log\LoggerInterface;
use Rollpix\GoogleOneTap\Model\Config\Data;

class SocialLoginService
{
    public function __construct(
        private readonly CustomerFactory $customerFactory,
        private readonly CustomerInterfaceFactory $customerInterfaceFactory,
        private readonly CustomerRepositoryInterface $customerRepositoryInterface,
        private readonly Random $mathRandom,
        private readonly EncryptorInterface $encryptor,
        private readonly LoggerInterface $logger,
        private readonly Data $config
    ) {}

    /**
     * Find existing customer by email or create a new one
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param int $websiteId
     * @param string $provider Provider identifier (e.g. 'google_onetap')
     * @return \Magento\Customer\Model\Customer
     */
    public function findOrCreateCustomer(
        string $email,
        string $firstName,
        string $lastName,
        int $websiteId,
        string $provider
    ): \Magento\Customer\Model\Customer {
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($email);

        if (!$customer->getId()) {
            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info("Social Login ($provider): Customer not found, creating new", [
                    'email' => $email,
                    'firstname' => $firstName,
                    'lastname' => $lastName
                ]);
            }

            $randomPassword = $this->mathRandom->getRandomString(16);
            $passwordHash = $this->encryptor->getHash($randomPassword, true);

            $newCustomer = $this->customerInterfaceFactory->create();
            $newCustomer->setWebsiteId($websiteId);
            $newCustomer->setEmail($email);
            $newCustomer->setFirstname($firstName);
            $newCustomer->setLastname($lastName);
            $this->customerRepositoryInterface->save($newCustomer, $passwordHash);

            // Reload customer for session
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($email);

            // Mark as linked from creation
            $this->markProviderLinked($customer, $provider, $email);

            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info("Social Login ($provider): New customer created", [
                    'customer_id' => $customer->getId()
                ]);
            }
        } else {
            // Check if this is the first time linking this provider
            $linkedAtKey = $provider . '_linked_at';
            $linkedAt = $customer->getData($linkedAtKey);

            if (empty($linkedAt)) {
                $this->markProviderLinked($customer, $provider, $email);

                $this->logger->info("Social Login ($provider): Account linked successfully", [
                    'customer_id' => $customer->getId(),
                    'email' => $email,
                    'action' => 'account_linking'
                ]);
            } else {
                if ($this->config->isDebugLoggingEnabled()) {
                    $this->logger->info("Social Login ($provider): Existing customer found", [
                        'customer_id' => $customer->getId(),
                        'linked_since' => $linkedAt
                    ]);
                }
            }
        }

        return $customer;
    }

    /**
     * Log in customer and regenerate session ID
     */
    public function loginCustomer(
        \Magento\Customer\Model\Customer $customer,
        Session $customerSession
    ): void {
        if ($this->config->isDebugLoggingEnabled()) {
            $this->logger->info('Social Login: Logging in customer', ['customer_id' => $customer->getId()]);
        }

        $customerSession->setCustomerAsLoggedIn($customer);
        $customerSession->regenerateId();

        if ($this->config->isDebugLoggingEnabled()) {
            $this->logger->info('Social Login: Customer logged in successfully', [
                'session_id' => $customerSession->getSessionId()
            ]);
        }
    }

    /**
     * Mark customer as linked to a social login provider
     */
    public function markProviderLinked(
        \Magento\Customer\Model\Customer $customer,
        string $provider,
        string $email
    ): void {
        $customer->setData($provider . '_linked_at', date('Y-m-d H:i:s'));
        $customer->setData($provider . '_email', $email);
        $customer->save();
    }
}
