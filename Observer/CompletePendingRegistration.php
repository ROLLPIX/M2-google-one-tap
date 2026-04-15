<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Rollpix\GoogleOneTap\Model\SocialLoginService;

class CompletePendingRegistration implements ObserverInterface
{
    private const SESSION_KEY = 'rollpix_google_onetap_pending_registration';

    public function __construct(
        private readonly Session $customerSession,
        private readonly SocialLoginService $socialLoginService,
        private readonly LoggerInterface $logger
    ) {}

    public function execute(Observer $observer): void
    {
        $pendingRegistration = $this->customerSession->getData(self::SESSION_KEY);
        if (!is_array($pendingRegistration) || empty($pendingRegistration['provider'])) {
            return;
        }

        $customer = $observer->getEvent()->getCustomer();
        if (!$customer || !$customer->getId()) {
            return;
        }

        $pendingEmail = strtolower((string)($pendingRegistration['email'] ?? ''));
        $customerEmail = strtolower((string)$customer->getEmail());
        if ($pendingEmail === '' || $pendingEmail !== $customerEmail) {
            return;
        }

        try {
            $this->socialLoginService->markProviderLinkedByCustomerId(
                (int)$customer->getId(),
                (string)$pendingRegistration['provider'],
                $customerEmail
            );
            $this->customerSession->unsData(self::SESSION_KEY);
            $this->customerSession->unsCustomerFormData();
        } catch (\Exception $e) {
            $this->logger->error('Google One Tap: failed to complete pending registration link', [
                'customer_id' => $customer->getId(),
                'email' => $customerEmail,
                'provider' => $pendingRegistration['provider'],
                'error' => $e->getMessage()
            ]);
        }
    }
}
