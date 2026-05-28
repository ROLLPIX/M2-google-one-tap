<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Observer;

use Magento\Framework\App\RequestInterface;
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

    private RequestInterface $request;

    public function __construct(
        PendingRegistration $pendingRegistration,
        SocialLoginService $socialLoginService,
        LoggerInterface $logger,
        RequestInterface $request
    ) {
        $this->pendingRegistration = $pendingRegistration;
        $this->socialLoginService = $socialLoginService;
        $this->logger = $logger;
        $this->request = $request;
    }

    public function execute(Observer $observer): void
    {
        $pendingRegistration = $this->pendingRegistration->get();
        if ($pendingRegistration === null) {
            return;
        }

        // Defence-in-depth: a registration carrying a user-typed password is native,
        // not a passwordless Google completion. Never link it, regardless of whether
        // the email happens to match the pending Google identity. Mirrors the
        // authoritative check in Plugin\Customer\PasswordlessRegistration.
        if ((string)$this->request->getParam('password') !== '') {
            $this->pendingRegistration->clear();
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
