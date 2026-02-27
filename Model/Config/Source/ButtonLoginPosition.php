<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ButtonLoginPosition implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'below', 'label' => __('Below Login Form')],
            ['value' => 'above', 'label' => __('Above Login Form')],
        ];
    }
}
