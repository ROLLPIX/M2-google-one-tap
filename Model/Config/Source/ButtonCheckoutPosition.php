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
            ['value' => 'below', 'label' => __('Below Email Section')],
            ['value' => 'left', 'label' => __('Left of Login Button')],
            ['value' => 'right', 'label' => __('Right of Login Button')],
        ];
    }
}
