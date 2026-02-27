<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonRegisterPosition implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'below', 'label' => __('Below Registration Form')],
            ['value' => 'above', 'label' => __('Above Registration Form')],
            ['value' => 'side', 'label' => __('Side Column (Next to Form)')],
        ];
    }
}
