<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Position implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'top-right', 'label' => __('Top Right')],
            ['value' => 'top-left', 'label' => __('Top Left')],
            ['value' => 'bottom-right', 'label' => __('Bottom Right')],
            ['value' => 'bottom-left', 'label' => __('Bottom Left')],
        ];
    }
}
