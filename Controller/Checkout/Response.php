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
        private readonly LoggerInterface $logger
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
            // Debug logging - see what we're receiving
            $this->logger->info('Google One Tap Request Debug', [
                'all_params' => $this->request->getParams(),
                'post_params' => $this->request->getPostValue(),
                'content_type' => $this->request->getHeader('Content-Type'),
                'method' => $this->request->getMethod()
            ]);

            // Validate ID token - use getPostValue() directly since getParam() may not work with POST body
            $postData = $this->request->getPostValue();
            $idToken = $postData['id_token'] ?? null;

            if (!$idToken) {
                $this->logger->error('Missing ID token after extraction', [
                    'post_data' => $postData,
                    'get_param_result' => $this->request->getParam('id_token')
                ]);
                throw new InputException(__('Missing ID token.'));
            }

            // Verify token with Google
            $googleOauthClientId = $this->config->getClientId($websiteId);
            $client = new Google_Client(['client_id' => $googleOauthClientId]);
            $payload = $client->verifyIdToken($idToken);

            if (!$payload || ($payload['aud'] ?? null) !== $googleOauthClientId) {
                throw new LocalizedException(__('Invalid Google ID token.'));
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
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($email);

            if (!$customer->getId()) {
                // Create new customer
                $newCustomer = $this->customerInterfaceFactory->create();
                $newCustomer->setWebsiteId($websiteId);
                $newCustomer->setEmail($email);
                $newCustomer->setFirstname($firstName);
                $newCustomer->setLastname($lastName);
                $this->customerRepositoryInterface->save($newCustomer);

                // Reload customer for session
                $customer->loadByEmail($email);
            }

            // Log in customer
            $this->customerSession->setCustomerAsLoggedIn($customer);

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
