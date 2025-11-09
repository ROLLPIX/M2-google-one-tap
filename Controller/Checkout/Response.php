<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Controller\Checkout;

use Exception;
use Magento\Framework\App\Request\InvalidRequestException;
use Rollpix\GoogleOneTap\Model\Config\Data;
use Google\Client as Google_Client;
use Magento\Customer\Model\Session;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\{InputException, LocalizedException, NoSuchEntityException};
use Magento\Framework\Math\Random;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Rollpix\GoogleOneTap\Model\RateLimiter;
use Psr\Log\LoggerInterface;

class Response implements CsrfAwareActionInterface
{

    /**
     * @param Data $config
     * @param RequestInterface $request
     * @param CustomerFactory $customerFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     * @param Random $mathRandom
     * @param EncryptorInterface $encryptor
     * @param RemoteAddress $remoteAddress
     * @param RateLimiter $rateLimiter
     */
    public function __construct(
        private readonly Data $config,
        private readonly RequestInterface $request,
        private readonly CustomerFactory $customerFactory,
        private readonly Session $customerSession,
        private readonly StoreManagerInterface $storeManager,
        private readonly CustomerInterfaceFactory $customerInterfaceFactory,
        private readonly CustomerRepositoryInterface $customerRepositoryInterface,
        private readonly JsonFactory $resultJsonFactory,
        private readonly LoggerInterface $logger,
        private readonly Random $mathRandom,
        private readonly EncryptorInterface $encryptor,
        private readonly RemoteAddress $remoteAddress,
        private readonly RateLimiter $rateLimiter
    ) {}

    /**
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();

        try {
            // Security: Rate limiting to prevent brute force attacks
            if ($this->config->isRateLimitEnabled()) {
                $ipAddress = $this->remoteAddress->getRemoteAddress();
                $maxAttempts = $this->config->getRateLimitMaxAttempts();
                $timeWindow = $this->config->getRateLimitTimeWindow();

                if ($this->rateLimiter->isRateLimitExceeded($ipAddress, $maxAttempts, $timeWindow)) {
                    // Log without exposing full IP (privacy)
                    $this->logger->warning('Google One Tap: Rate limit exceeded', [
                        'ip_hash' => substr(hash('sha256', $ipAddress), 0, 16),
                        'max_attempts' => $maxAttempts,
                        'time_window' => $timeWindow
                    ]);

                    return $result->setData([
                        'success' => false,
                        'message' => __('Too many authentication attempts. Please try again later.')
                    ]);
                }

                // Record this attempt
                $this->rateLimiter->recordAttempt($ipAddress, $timeWindow);
            }

            // Debug logging - only if enabled (contains sensitive data)
            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info('Google One Tap Request Debug', [
                    'all_params' => $this->request->getParams(),
                    'post_params' => $this->request->getPostValue(),
                    'content_type' => $this->request->getHeader('Content-Type'),
                    'method' => $this->request->getMethod()
                ]);
            }

            // Validate ID token - use getPostValue() directly since getParam() may not work with POST body
            $postData = $this->request->getPostValue();
            $idToken = $postData['id_token'] ?? null;

            // Security: Validate token exists and length (prevent DoS attacks with huge payloads)
            if (!$idToken || strlen($idToken) > 2048) {
                if ($this->config->isDebugLoggingEnabled()) {
                    $this->logger->error('Missing or invalid ID token', [
                        'token_length' => $idToken ? strlen($idToken) : 0,
                        'post_data' => $postData,
                        'get_param_result' => $this->request->getParam('id_token')
                    ]);
                }
                throw new InputException(__('Invalid or missing ID token.'));
            }

            // Verify token with Google
            $googleOauthClientId = $this->config->getClientId($websiteId);

            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info('Google One Tap: Verifying token', ['client_id' => $googleOauthClientId]);
            }

            $client = new Google_Client(['client_id' => $googleOauthClientId]);
            $payload = $client->verifyIdToken($idToken);

            if (!$payload || ($payload['aud'] ?? null) !== $googleOauthClientId) {
                if ($this->config->isDebugLoggingEnabled()) {
                    $this->logger->error('Google One Tap: Token verification failed', [
                        'payload' => $payload,
                        'expected_aud' => $googleOauthClientId,
                        'actual_aud' => $payload['aud'] ?? 'null'
                    ]);
                }
                throw new LocalizedException(__('Invalid Google ID token.'));
            }

            // Security: Explicitly validate token expiration (defense in depth)
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                if ($this->config->isDebugLoggingEnabled()) {
                    $this->logger->error('Google One Tap: Token has expired', [
                        'exp' => $payload['exp'],
                        'current_time' => time()
                    ]);
                }
                throw new LocalizedException(__('Authentication token has expired. Please try again.'));
            }

            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info('Google One Tap: Token verified successfully', ['email' => $payload['email'] ?? 'unknown']);
            }

            // Validate email is verified
            if (!($payload['email_verified'] ?? false)) {
                throw new LocalizedException(__('Email not verified by Google.'));
            }

            // Validate email
            $email = $payload["email"] ?? null;
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InputException(__('Invalid or missing email address.'));
            }

            // Parse name with improved logic
            $fullName = trim($payload["name"] ?? '');
            if (empty($fullName)) {
                $firstName = 'Google';
                $lastName = 'User';
            } else {
                $nameParts = explode(' ', $fullName);
                $firstName = $nameParts[0];
                $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $nameParts[0];
            }

            // Load existing customer or create new one
            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info('Google One Tap: Looking for customer', ['email' => $email, 'website_id' => $websiteId]);
            }

            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($email);

            if (!$customer->getId()) {
                if ($this->config->isDebugLoggingEnabled()) {
                    $this->logger->info('Google One Tap: Customer not found, creating new', [
                        'email' => $email,
                        'firstname' => $firstName,
                        'lastname' => $lastName
                    ]);
                }

                // Generate a secure random password for the new customer
                // This allows the customer to use the "Change Password" functionality later
                $randomPassword = $this->mathRandom->getRandomString(16);
                $passwordHash = $this->encryptor->getHash($randomPassword, true);

                // Create new customer
                $newCustomer = $this->customerInterfaceFactory->create();
                $newCustomer->setWebsiteId($websiteId);
                $newCustomer->setEmail($email);
                $newCustomer->setFirstname($firstName);
                $newCustomer->setLastname($lastName);
                $this->customerRepositoryInterface->save($newCustomer, $passwordHash);

                // Reload customer for session - recreate with websiteId to avoid "website ID not specified" error
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId);
                $customer->loadByEmail($email);

                // Mark as linked via Google One Tap from creation and save Google email
                $customer->setData('google_onetap_linked_at', date('Y-m-d H:i:s'));
                $customer->setData('google_onetap_email', $email);
                $customer->save();

                if ($this->config->isDebugLoggingEnabled()) {
                    $this->logger->info('Google One Tap: New customer created with random password', [
                        'customer_id' => $customer->getId()
                    ]);
                }
            } else {
                // Check if this is the first time linking Google One Tap to existing account
                $linkedAt = $customer->getData('google_onetap_linked_at');
                $isAccountLinking = empty($linkedAt);

                if ($isAccountLinking) {
                    // First time using Google One Tap - link the account and save Google email
                    $customer->setData('google_onetap_linked_at', date('Y-m-d H:i:s'));
                    $customer->setData('google_onetap_email', $email);
                    $customer->save();

                    $this->logger->info('Google One Tap: Account linked successfully', [
                        'customer_id' => $customer->getId(),
                        'email' => $email,
                        'action' => 'account_linking'
                    ]);
                } else {
                    if ($this->config->isDebugLoggingEnabled()) {
                        $this->logger->info('Google One Tap: Existing customer found', [
                            'customer_id' => $customer->getId(),
                            'linked_since' => $linkedAt
                        ]);
                    }
                }
            }

            // Log in customer
            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info('Google One Tap: Logging in customer', ['customer_id' => $customer->getId()]);
            }
            $this->customerSession->setCustomerAsLoggedIn($customer);

            // Regenerate session ID for security and persistence
            $this->customerSession->regenerateId();

            if ($this->config->isDebugLoggingEnabled()) {
                $this->logger->info('Google One Tap: Customer logged in successfully', [
                    'session_id' => $this->customerSession->getSessionId()
                ]);
            }

            return $result->setData(['success' => true]);

        } catch (Exception $e) {
            // Log error for debugging
            $this->logger->error('Google One Tap authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
