<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonTheme implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'outline', 'label' => __('Outline')],
            ['value' => 'filled_blue', 'label' => __('Filled Blue')],
            ['value' => 'filled_black', 'label' => __('Filled Black')],
        ];
    }
}
