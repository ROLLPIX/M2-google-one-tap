<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonText implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'signin_with', 'label' => __('Sign in with Google')],
            ['value' => 'signup_with', 'label' => __('Sign up with Google')],
            ['value' => 'continue_with', 'label' => __('Continue with Google')],
            ['value' => 'signin', 'label' => __('Sign in')],
        ];
    }
}
