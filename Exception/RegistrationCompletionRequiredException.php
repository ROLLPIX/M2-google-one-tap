<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Exception;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class RegistrationCompletionRequiredException extends LocalizedException
{
    private string $email;

    private string $firstName;

    private string $lastName;

    private int $websiteId;

    private string $provider;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        int $websiteId,
        string $provider,
        ?Phrase $phrase = null,
        ?Exception $cause = null
    ) {
        parent::__construct(
            $phrase ?: __('Complete the missing account details to finish registration with Google.'),
            $cause
        );

        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->websiteId = $websiteId;
        $this->provider = $provider;
    }

    /**
     * @return array<string, int|string>
     */
    public function getPendingRegistrationData(): array
    {
        return [
            'email' => $this->email,
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'website_id' => $this->websiteId,
            'provider' => $this->provider
        ];
    }
}
