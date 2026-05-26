<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Rollpix\GoogleOneTap\Model\PendingRegistration;
use Rollpix\GoogleOneTap\Model\SocialLoginService;

class CompletePendingRegistration implements ObserverInterface
{
    private PendingRegistration $pendingRegistration;

    private SocialLoginService $socialLoginService;

    private LoggerInterface $logger;

    public function __construct(
        PendingRegistration $pendingRegistration,
        SocialLoginService $socialLoginService,
        LoggerInterface $logger
    ) {
        $this->pendingRegistration = $pendingRegistration;
        $this->socialLoginService = $socialLoginService;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        $pendingRegistration = $this->pendingRegistration->get();
        if ($pendingRegistration === null) {
            return;
        }

        $customer = $observer->getEvent()->getCustomer();
        if (!$customer || !$customer->getId()) {
            return;
        }

        $pendingEmail = strtolower((string)($pendingRegistration['email'] ?? ''));
        $customerEmail = strtolower((string)$customer->getEmail());
        if ($pendingEmail === '' || $pendingEmail !== $customerEmail) {
            // Email mismatch (user opted out of Google sign-up via the form's escape hatch).
            // Drop the pending data so we don't try to link a different Google identity later.
            $this->pendingRegistration->clear();
            return;
        }

        try {
            $this->socialLoginService->markProviderLinkedByCustomerId(
                (int)$customer->getId(),
                (string)$pendingRegistration['provider'],
                $customerEmail
            );
            $this->pendingRegistration->clear();
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
