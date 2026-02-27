<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Context implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'signin', 'label' => __('Sign In')],
            ['value' => 'signup', 'label' => __('Sign Up')],
            ['value' => 'use', 'label' => __('Use')],
        ];
    }
}
