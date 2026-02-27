<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonCheckoutPosition implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'above', 'label' => __('Above Checkout Form')],
            ['value' => 'below', 'label' => __('Below Email Section')],
        ];
    }
}
