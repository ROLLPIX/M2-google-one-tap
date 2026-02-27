<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonSize implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'large', 'label' => __('Large')],
            ['value' => 'medium', 'label' => __('Medium')],
            ['value' => 'small', 'label' => __('Small')],
        ];
    }
}
