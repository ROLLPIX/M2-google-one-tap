<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonPages implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'login', 'label' => __('Customer Login Page')],
            ['value' => 'register', 'label' => __('Customer Registration Page')],
            ['value' => 'checkout', 'label' => __('Checkout Page')],
            ['value' => 'custom', 'label' => __('Custom CSS Selector')],
        ];
    }
}
