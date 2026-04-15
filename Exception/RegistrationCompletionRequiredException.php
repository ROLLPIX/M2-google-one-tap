<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Exception;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class RegistrationCompletionRequiredException extends LocalizedException
{
    public function __construct(
        private readonly string $email,
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly int $websiteId,
        private readonly string $provider,
        ?Phrase $phrase = null,
        ?Exception $cause = null
    ) {
        parent::__construct(
            $phrase ?: __('Complete the missing account details to finish registration with Google.'),
            $cause
        );
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
