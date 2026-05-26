<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Psr\Log\LoggerInterface;
use Rollpix\GoogleOneTap\Exception\RegistrationCompletionRequiredException;
use Rollpix\GoogleOneTap\Model\Config\Data;

class SocialLoginService
{
    private CustomerFactory $customerFactory;

    private CustomerInterfaceFactory $customerInterfaceFactory;

    private CustomerRepositoryInterface $customerRepositoryInterface;

    private Random $mathRandom;

    private EncryptorInterface $encryptor;

    private LoggerInterface $logger;

    private Data $config;

    private CustomerCollectionFactory $customerCollectionFactory;

    public function __construct(
        CustomerFactory $customerFactory,
        CustomerInterfaceFactory $customerInterfaceFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Random $mathRandom,
        EncryptorInterface $encryptor,
        LoggerInterface $logger,
        Data $config,
        CustomerCollectionFactory $customerCollectionFactory
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->mathRandom = $mathRandom;
        $this->encryptor = $encryptor;
        $this->logger = $logger;
        $this->config = $config;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

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
        // P4: resolve by the stored provider identity first. The Google email is
        // stable per Google account, but the customer's login email can change
        // (account edit / admin / REST). Looking up by `<provider>_email` keeps a
        // re-login bound to the original account instead of spawning a duplicate
        // one when the two emails have diverged. Falls through to email match for
        // first-time links and brand-new customers.
        $customer = $this->findByProviderEmail($email, $websiteId, $provider);

        if ($customer === null) {
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($email);
        }

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
            try {
                $this->customerRepositoryInterface->save($newCustomer, $passwordHash);
            } catch (LocalizedException $e) {
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId);
                $customer->loadByEmail($email);

                if ($customer->getId()) {
                    $this->markProviderLinked($customer, $provider, $email);
                    return $customer;
                }

                throw new RegistrationCompletionRequiredException(
                    $email,
                    $firstName,
                    $lastName,
                    $websiteId,
                    $provider,
                    null,
                    $e
                );
            }

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
     * Find a customer previously linked to this provider identity (provider email),
     * scoped to the website. Returns a fully loaded model or null when none exists.
     *
     * @param string $email Provider (e.g. Google) email captured at link time
     * @param int $websiteId
     * @param string $provider Provider identifier (e.g. 'google_onetap')
     * @return \Magento\Customer\Model\Customer|null
     */
    private function findByProviderEmail(
        string $email,
        int $websiteId,
        string $provider
    ): ?\Magento\Customer\Model\Customer {
        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToFilter($provider . '_email', ['eq' => $email])
            ->addFieldToFilter('website_id', $websiteId)
            ->setOrder('entity_id', 'ASC')
            ->setPageSize(1);

        $match = $collection->getFirstItem();
        if (!$match->getId()) {
            return null;
        }

        // Reload as a full Customer model so the session/login path behaves
        // exactly as with loadByEmail (collection items are partially loaded).
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->load((int)$match->getId());

        if (!$customer->getId()) {
            return null;
        }

        if ($this->config->isDebugLoggingEnabled()) {
            $this->logger->info("Social Login ($provider): Matched existing customer by provider identity", [
                'customer_id' => $customer->getId(),
                'provider_email' => $email,
                'login_email' => $customer->getEmail()
            ]);
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

    public function markProviderLinkedByCustomerId(
        int $customerId,
        string $provider,
        string $email
    ): void {
        $customer = $this->customerFactory->create()->load($customerId);
        if (!$customer->getId()) {
            return;
        }

        $this->markProviderLinked($customer, $provider, $email);
    }
}
